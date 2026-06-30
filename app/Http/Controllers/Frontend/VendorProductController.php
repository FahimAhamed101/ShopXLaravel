<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ProductStoreRequest;
use App\Http\Requests\Frontend\ProductUpdateRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\Tag;
use App\Services\AlertService;
use App\Traits\FileUploadTrait;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class VendorProductController extends Controller
{
    use FileUploadTrait;

    function index(): View
    {
        $products = $this->vendorProductsQuery()->latest()->paginate(30);
        return view('vendor-dashboard.product.index', compact('products'));
    }

    function create(string $type): View
    {
        abort_unless(in_array($type, ['physical', 'digital']), 404);

        $stores = $this->availableStores();
        $brands = $this->availableBrands();
        $tags = $this->availableTags();
        $categories = $this->nestedCategories();
        return view('vendor-dashboard.product.create', compact('stores', 'brands', 'tags', 'categories', 'type'));
    }

    function store(ProductStoreRequest $request, string $type)
    {

        if (!in_array($type, ['physical', 'digital'])) abort(404);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->product_type = $type;
        $product->short_description = $request->short_description;
        $product->description = $request->content;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->special_price = $request->special_price;
        $product->special_price_start = $request->from_date;
        $product->special_price_end = $request->to_date;
        $product->qty = $request->quantity;
        $product->manage_stock = $request->has('manage_stock') ? 'yes' : 'no';
        $product->in_stock = $request->stock_status == 'in_stock' ? 1 : 0;
        $product->status = $request->status;
        $product->approved_status = 'pending';
        $product->brand_id = $request->brand;
        $product->is_featured = $request->has('is_featured') ? 1 : 0;
        $product->is_hot = $request->has('is_hot') ? 1 : 0;
        $product->is_new = $request->has('is_new') ? 1 : 0;
        $this->assignVendorOwnership($product);
        $product->save();

        /** Attach categories */
        if ($this->productCategorySyncReady()) {
            $product->categories()->sync($request->categories ?? []);
        }

        /** Attach tags */
        if ($this->productTagSyncReady()) {
            $product->tags()->sync($request->tags ?? []);
        }

        if ($type == 'physical') {
            return response()->json([
                'id' => $product->id,
                'redirect_url' => route('vendor.products.edit', $product->id) . '#product-images',
                'status' => 'success',
                'message' => 'Product created successfully'
            ]);
        } else {

            return response()->json([
                'id' => $product->id,
                'redirect_url' => route('vendor.digital-products.edit', $product->id) . '#product-images',
                'status' => 'success',
                'message' => 'Product created successfully'
            ]);
        }
    }

    function edit(int $id)
    {

        $product = Product::findOrFail($id);
        $this->ensureVendorOwnsProduct($product);

        $productCategoryIds = $this->productCategorySyncReady() ? $product->categories->pluck('id')->toArray() : [];
        $productTagIds = $this->productTagSyncReady() ? $product->tags->pluck('id')->toArray() : [];
        $stores = $this->availableStores();
        $brands = $this->availableBrands();
        $tags = $this->availableTags();
        $categories = $this->nestedCategories();

        $attributesWithValues = $product?->attributeWithValues ?? [];
        $variants = $product?->variants ?? [];
        // dd($attributesValues);
        return view('vendor-dashboard.product.edit', compact('stores', 'brands', 'tags', 'categories', 'product', 'productCategoryIds', 'productTagIds', 'attributesWithValues', 'variants'));
    }

    function editDigitalProduct(int $id)
    {

        $product = Product::findOrFail($id);
        if ($product->product_type != 'digital') abort(404);
        $this->ensureVendorOwnsProduct($product);

        // dd($product->attributes);
        $productCategoryIds = $this->productCategorySyncReady() ? $product->categories->pluck('id')->toArray() : [];
        $productTagIds = $this->productTagSyncReady() ? $product->tags->pluck('id')->toArray() : [];
        $stores = $this->availableStores();
        $brands = $this->availableBrands();
        $tags = $this->availableTags();
        $categories = $this->nestedCategories();

        return view('vendor-dashboard.product.digital-edit', compact('stores', 'brands', 'tags', 'categories', 'product', 'productCategoryIds', 'productTagIds'));
    }

    function uploadDigitalProductFile(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $this->ensureVendorOwnsProduct($product);

        $file = $request->file('file');
        $chunkIndex = $request->dzchunkindex;
        $totalChunks = $request->dztotalchunkcount;
        $fileName = $request->name;

        $chunkFolder = storage_path('app/private/chunks/' . $fileName);
        if (!file_exists($chunkFolder)) {
            mkdir($chunkFolder, 0777, true);
        }

        $chunkPath = $chunkFolder . '/' . $chunkIndex;

        file_put_contents($chunkPath, file_get_contents($file->getRealPath()));

        if ($chunkIndex == $totalChunks - 1) {
            $finalFileName = \Str::uuid() . '.' . $file->getClientOriginalExtension();
            $finalPath = storage_path('app/private/uploads/' . $finalFileName);
            $output = fopen($finalPath, 'ab');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkFile = $chunkFolder . '/' . $i;
                $input = fopen($chunkFile, 'rb');
                stream_copy_to_stream($input, $output);
                fclose($input);
                unlink($chunkFile);
            }

            fclose($output);

            rmdir($chunkFolder);

            $validationResponse = $this->validateFinalFile($finalPath);
            if ($validationResponse !== true) {
                unlink($finalPath);
                return $validationResponse;
            }

            $this->storeDigitalFile($file, $request->product_id, $fileName, $finalFileName);


            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'chunk_received']);
    }

    function validateFinalFile(string $finalPath)
    {
        $maxSizeMb = 1000;
        $maxSizeBytes = $maxSizeMb * 1024 * 1024;
        if (filesize($finalPath) > $maxSizeBytes) {
            return response()->json(['status' => 'error', 'message' => 'File size limit exceeded'], 413);
        }

        // mime validation

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $finalPath);
        finfo_close($finfo);

        $allowedMimeTypes = [
            // Images
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'bmp'  => 'image/bmp',
            'svg'  => 'image/svg+xml',
            'ico'  => 'image/vnd.microsoft.icon',

            // Documents
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt'  => 'text/plain',
            'csv'  => 'text/csv',
            'rtf'  => 'application/rtf',

            // Audio
            'mp3'  => 'audio/mpeg',
            'wav'  => 'audio/wav',
            'ogg'  => 'audio/ogg',
            'm4a'  => 'audio/mp4',
            'flac' => 'audio/flac',

            // Video
            'mp4'  => 'video/mp4',
            'webm' => 'video/webm',
            'mov'  => 'video/quicktime',

            // Archives (still consider validating contents before extracting)
            'zip'  => 'application/zip',
            '7z'   => 'application/x-7z-compressed',
            'tar'  => 'application/x-tar',
            'gz'   => 'application/gzip',
        ];

        if (!in_array($mimeType, $allowedMimeTypes)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid file type'], 400);
        }

        return true;
    }

    function storeDigitalFile($file, $product_id, $fileName, $finalFileName)
    {

        $productFile = new ProductFile();
        $productFile->product_id = $product_id;
        $productFile->filename = $fileName;
        $productFile->path = "/uploads/" . $finalFileName;
        $productFile->extension = $file->getClientOriginalExtension();
        $productFile->size = $file->getSize();
        $productFile->save();
    }

    function destroyDigitalProductFile(int $productId, int $id)
    {
        try {
            $product = Product::findOrFail($productId);
            $this->ensureVendorOwnsProduct($product);

            $productFile = ProductFile::where('id', $id)->where('product_id', $productId)->firstOrFail();
            // delete from storage
            if (Storage::disk('local')->exists($productFile->path)) {
                Storage::disk('local')->delete($productFile->path);
            }
            $productFile->delete();
            return response()->json(['status' => 'success', 'message' => 'File deleted successfully']);
        } catch (\Exception $e) {
            logger('Failed to delete file: ' . $e);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    function update(ProductUpdateRequest $request, int $id)
    {
        $product = Product::findOrFail($id);
        $this->ensureVendorOwnsProduct($product);

        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->short_description = $request->short_description;
        $product->description = $request->content;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->special_price = $request->special_price;
        $product->special_price_start = $request->from_date;
        $product->special_price_end = $request->to_date;
        $product->qty = $request->quantity;
        $product->manage_stock = $request->has('manage_stock') ? 'yes' : 'no';
        $product->in_stock = $request->stock_status == 'in_stock' ? 1 : 0;
        $product->status = $request->status;
        $product->brand_id = $request->brand;
        $product->is_featured = $request->has('is_featured') ? 1 : 0;
        $product->is_hot = $request->has('is_hot') ? 1 : 0;
        $product->is_new = $request->has('is_new') ? 1 : 0;
        $this->assignVendorOwnership($product);
        $product->save();

        /** Attach categories */
        if ($this->productCategorySyncReady()) {
            $product->categories()->sync($request->categories ?? []);
        }

        /** Attach tags */
        if ($this->productTagSyncReady()) {
            $product->tags()->sync($request->tags ?? []);
        }

        AlertService::created();

        return response()->json([
            'id' => $product->id,
            'status' => 'success',
            'message' => 'Product updated successfully',
            'redirect_url' => route('vendor.products.index')
        ]);
    }

    function uploadImages(Request $request, Product $product)
    {
        $this->ensureVendorOwnsProduct($product);

        $request->validate([
            'file' => ['required', 'image', 'max:3048']
        ]);

        $filePath = $this->uploadFile($request->file('file'));

        $productImage = new ProductImage();
        $productImage->product_id = $product->id;
        $productImage->path = $filePath;
        $productImage->order = (ProductImage::where('product_id', $product->id)->max('order') ?? 0) + 1;
        $productImage->save();

        return response()->json([
            'status' => 'success',
            'id' => $productImage->id,
            'path' => asset($filePath),
            'message' => 'Image uploaded successfully'
        ]);
    }

    function destroyImage(int $id)
    {
        $image = ProductImage::findOrFail($id);
        $product = Product::findOrFail($image->product_id);
        $this->ensureVendorOwnsProduct($product);

        $this->deleteFile($image->path);
        $image->delete();
        return response()->json(['status' => 'success', 'message' => 'Image deleted successfully']);
    }

    function imagesReorder(Request $request)
    {
        foreach ($request->images as $image) {
            ProductImage::where('id', $image['id'])->update(['order' => $image['order']]);
        }
    }


    function storeAttributes(Request $request, Product $product)
    {
        $this->ensureVendorOwnsProduct($product);

        $request->validate([
            'attribute_name' => ['required', 'string', 'max:255'],
            'attribute_type' => ['required', 'string', 'in:text,color'],
        ]);

        DB::beginTransaction();

        try {
            if ($request->filled('attribute_id')) {
                $this->updateExistingAttribute($request, $product);
            } else {
                $this->createNewAttribute($request, $product);
            }

            DB::commit();

            // regenerate product variants
            $this->regenerateProductVariants($product);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }


        return $this->buildSuccessResponse($product);
    }

    function createNewAttribute(Request $request, Product $product)
    {
        $this->ensureVendorOwnsProduct($product);

        $attribute = new Attribute();
        $attribute->name = $request->attribute_name;
        $attribute->type = $request->attribute_type;
        $attribute->save();

        $this->addAttributesValue($attribute, $request, $product);
    }

    function updateExistingAttribute(Request $request, Product $product)
    {
        $this->ensureVendorOwnsProduct($product);

        $attribute = Attribute::findOrFail($request->attribute_id);
        $attribute->name = $request->attribute_name;
        $attribute->type = $request->attribute_type;
        $attribute->save();

        // remove existing relations and values for this attribute
        $this->clearAttributeData($attribute, $product);

        // add new attributes values
        $this->addAttributesValue($attribute, $request, $product);
    }

    function clearAttributeData(Attribute $attribute, Product $product)
    {
        $this->ensureVendorOwnsProduct($product);

        DB::table('product_attribute_values')
            ->where('product_id', $product->id)
            ->where('attribute_id', $attribute->id)
            ->delete();

        AttributeValue::where('attribute_id', $attribute->id)->delete();
    }

    function addAttributesValue(Attribute $attribute, Request $request, Product $product)
    {
        $this->ensureVendorOwnsProduct($product);

        $labels = $request->label ?? [];

        foreach ($labels as $index => $label) {
            if (empty($label)) continue;

            $attributeValue = new AttributeValue();
            $attributeValue->attribute_id = $attribute->id;
            $attributeValue->value = $label;
            $attributeValue->color = $request->color_value[$index] ?? null;
            $attributeValue->save();

            // link to product
            DB::table('product_attribute_values')->insert([
                'product_id' => $product->id,
                'attribute_id' => $attribute->id,
                'attribute_value_id' => $attributeValue->id
            ]);
        }
    }

    function buildSuccessResponse(Product $product)
    {
        $this->ensureVendorOwnsProduct($product);

        $product->refresh();

        $attributes = $product->attributeWithValues;


        $html = '';
        $variantHtml = '';

        foreach ($attributes as $attribute) {

            $html .= view('vendor-dashboard.product.partials.attribute', compact('attribute', 'product'))->render();
        }

        foreach ($product->variants as $variant) {
            $variantHtml .= view('vendor-dashboard.product.partials.variant', compact('variant'))->render();
        }

        return response()->json([
            'message' => 'Attribute generated successfully',
            'html' => $html,
            'variantHtml' => $variantHtml
        ]);
    }

    function destroyAttribute(int $productId, int $attributeId)
    {
        try {
            $product = Product::findOrFail($productId);
            $this->ensureVendorOwnsProduct($product);

            $attribute = Attribute::findOrFail($attributeId);

            $this->clearAttributeData($attribute, $product);
            $this->regenerateProductVariants($product);

            $product->refresh();

            $attributes = $product->attributeWithValues;

            $attribute->delete();


            $html = '';
            $variantHtml = '';

            foreach ($attributes as $attribute) {

                $html .= view('vendor-dashboard.product.partials.attribute', compact('attribute', 'product'))->render();
            }

            foreach ($product->variants as $variant) {
                $variantHtml .= view('vendor-dashboard.product.partials.variant', compact('variant'))->render();
            }

            return response()->json([
                'message' => 'Attribute deleted successfully',
                'html' => $html,
                'variantHtml' => $variantHtml
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    function regenerateProductVariants(Product $product)
    {
        $this->ensureVendorOwnsProduct($product);

        // clear existing variants
        $this->clearExistingVariants($product);


        // get current attribute values group by attributes
        $attributeGroups = $this->getAttributeGroups($product);

        if ($attributeGroups->isEmpty()) {
            return;
        }

        $combinations = $this->cartesianProduct($attributeGroups);

        $this->createVariantsFromCombinations($product, $combinations);
    }

    function getAttributeGroups(Product $product)
    {
        $this->ensureVendorOwnsProduct($product);

        $groupedAttributes = DB::table('product_attribute_values')
            ->where('product_id', $product->id)
            ->get()->groupBy('attribute_id');

        $attributeGroups = collect();

        foreach ($groupedAttributes as $attributeId => $items) {
            $attributeValues = AttributeValue::whereIn('id', $items->pluck('attribute_value_id'))->get();
            $attributeGroups->push($attributeValues);
        }


        return $attributeGroups;
    }

    function cartesianProduct(Collection $attributeGroups)
    {
        $result = [[]];

        foreach ($attributeGroups as $attributeValues) {
            $temp = [];

            foreach ($result as $resultItem) {
                foreach ($attributeValues as $attributeValue) {
                    $temp[] = array_merge($resultItem, [$attributeValue]);
                }
            }

            $result = $temp;
        }

        return $result;
    }

    function createVariantsFromCombinations(Product $product, array $combinations)
    {
        foreach ($combinations as $combination) {
            $variant = $this->createSingleVariant($product, $combination);
            $this->attachAttributesToVariant($variant, $combination);
        }
    }

    function createSingleVariant(Product $product, array $combination)
    {
        $variantName = collect($combination)->pluck('value')->implode('/');

        return ProductVariant::create([
            'product_id' => $product->id,
            'name' => $variantName,
            'price' => 0,
            'sku' => '',
            'qty' => 0,
            'is_active' => 1
        ]);
    }

    function attachAttributesToVariant(ProductVariant $variant, array $combination)
    {
        foreach ($combination as $attributeValue) {
            DB::table('product_variant_attribute_value')->insert([
                'product_variant_id' => $variant->id,
                'attribute_id' => $attributeValue->attribute_id,
                'attribute_value_id' => $attributeValue->id,
            ]);
        }
    }

    function updateVariants(Request $request, int $product)
    {
        $request->validate([
            'variant_sku' => ['nullable', 'string', 'max:255'],
            'variant_price' => ['required', 'numeric'],
            'variant_special_price' => ['nullable', 'numeric'],
            'variant_manage_stock' => ['nullable'],
            'variant_quantity' => ['nullable', 'numeric'],
            'variant_stock_status' => ['required', 'in:in_stock,out_of_stock'],
            'variant_is_default' => ['nullable'],
            'variant_is_active' => ['nullable'],
        ]);

        $product = Product::findOrFail($product);

        $variant = ProductVariant::findOrFail($request->variant_id);
        $variant->sku = $request->variant_sku;
        $variant->price = $request->variant_price;
        $variant->special_price = $request->variant_special_price;
        $variant->manage_stock = $request->variant_manage_stock ? 1 : 0;
        $variant->qty = $request->variant_quantity;
        $variant->in_stock = $request->variant_stock_status == 'in_stock' ? 1 : 0;
        $variant->is_default = $request->variant_is_default;
        $variant->is_active = $request->variant_is_active;
        $variant->save();

        return response()->json(['message' => 'Variant updated successfully']);
    }

    function clearExistingVariants(Product $product)
    {
        foreach ($product->variants as $variant) {
            DB::table('product_variant_attribute_value')
                ->where('product_variant_id', $variant->id)
                ->delete();
            $variant->delete();
        }
    }

    function destroy(Product $product)
    {
        if ($this->productBelongsToVendor($product)) {
            $product->delete();
            notyf()->success('Product deleted successfully');
            return response()->json(['status' => 'success', 'message' => 'Product deleted successfully']);
        }

        notyf()->error('You do not have permission to delete this product');
        return response()->json(['status' => 'error', 'message' => 'You do not have permission to delete this product']);
    }

    protected function vendorProductsQuery()
    {
        $query = Product::query();
        $vendorId = auth('web')->id();

        if (! $vendorId) {
            return $query->whereRaw('1 = 0');
        }

        foreach (['vendor_id', 'user_id'] as $column) {
            if (Schema::hasColumn('products', $column)) {
                return $query->where($column, $vendorId);
            }
        }

        if (Schema::hasColumn('products', 'store_id')) {
            $storeId = $this->vendorStoreId();

            return $storeId
                ? $query->where('store_id', $storeId)
                : $query->whereRaw('1 = 0');
        }

        return $query->whereRaw('1 = 0');
    }

    protected function availableStores(): Collection
    {
        if (! class_exists(Store::class) || ! Schema::hasTable('stores')) {
            return collect();
        }

        if (! Schema::hasColumn('stores', 'name')) {
            return collect();
        }

        return Store::query()->select(['name', 'id'])->get();
    }

    protected function availableBrands(): Collection
    {
        if (! class_exists(Brand::class) || ! Schema::hasTable('brands') || ! Schema::hasColumn('brands', 'name')) {
            return collect();
        }

        $query = Brand::query()->select(['name', 'id']);

        if (Schema::hasColumn('brands', 'is_active')) {
            $query->where('is_active', 1);
        }

        return $query->get();
    }

    protected function availableTags(): Collection
    {
        if (! class_exists(Tag::class) || ! Schema::hasTable('tags')) {
            return collect();
        }

        $query = Tag::query();

        if (Schema::hasColumn('tags', 'is_active')) {
            $query->where('is_active', 1);
        }

        return $query->get();
    }

    protected function nestedCategories(): Collection
    {
        return tableHasColumns('categories', ['parent_id']) ? Category::getNested() : collect();
    }

    protected function productCategorySyncReady(): bool
    {
        return Schema::hasTable('category_product');
    }

    protected function productTagSyncReady(): bool
    {
        return Schema::hasTable('product_tag');
    }

    protected function assignVendorOwnership(Product $product): void
    {
        $vendorId = auth('web')->id();

        if (! $vendorId) {
            return;
        }

        if (Schema::hasColumn('products', 'vendor_id')) {
            $product->vendor_id = $vendorId;
        }

        if (Schema::hasColumn('products', 'user_id')) {
            $product->user_id = $vendorId;
        }

        if (Schema::hasColumn('products', 'store_id')) {
            $storeId = $this->vendorStoreId();

            if ($storeId) {
                $product->store_id = $storeId;
            }
        }
    }

    protected function ensureVendorOwnsProduct(Product $product): void
    {
        abort_unless($this->productBelongsToVendor($product), 404);
    }

    protected function productBelongsToVendor(Product $product): bool
    {
        $vendorId = auth('web')->id();

        if (! $vendorId) {
            return false;
        }

        foreach (['vendor_id', 'user_id'] as $column) {
            if (Schema::hasColumn('products', $column) && (int) ($product->{$column} ?? 0) === (int) $vendorId) {
                return true;
            }
        }

        if (Schema::hasColumn('products', 'store_id')) {
            $storeId = $this->vendorStoreId();

            return $storeId !== null && (int) ($product->store_id ?? 0) === $storeId;
        }

        return false;
    }

    protected function vendorStoreId(): ?int
    {
        $vendorId = auth('web')->id();

        if (! $vendorId || ! class_exists(Store::class) || ! Schema::hasTable('stores')) {
            return null;
        }

        if (! Schema::hasColumn('stores', 'seller_id')) {
            return null;
        }

        return DB::table('stores')->where('seller_id', $vendorId)->value('id');
    }
}
