<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
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
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller implements HasMiddleware
{
    use FileUploadTrait;

    static function Middleware(): array
    {
        return [
            new Middleware('permission:Product Management')
        ];
    }

    function index(): View
    {
        $products = Product::orderBy('id', 'desc')->paginate(30);
        return view('admin.product.index', compact('products'));
    }

    function create(): View
    {
        $stores = Store::select(['name', 'id'])->get();
        $brands = Brand::select(['name', 'id'])->where('is_active', 1)->get();
        $tags = Tag::where('is_active', 1)->get();
        $categories = Category::getNested();
        return view('admin.product.create', compact('stores', 'brands', 'tags', 'categories'));
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
        $product->approved_status = 'approved';
        $product->store_id = $request->store;
        $product->brand_id = $request->brand;
        $product->is_featured = $request->has('is_featured') ? 1 : 0;
        $product->is_hot = $request->has('is_hot') ? 1 : 0;
        $product->is_new = $request->has('is_new') ? 1 : 0;
        $product->save();

        /** Attach categories */
        $product->categories()->sync($request->categories);

        /** Attach tags */
        $product->tags()->sync($request->tags);

        if ($type == 'physical') {
            return response()->json([
                'id' => $product->id,
                'redirect_url' => route('admin.products.edit', $product->id) . '#product-images',
                'status' => 'success',
                'message' => 'Product created successfully'
            ]);
        } else {

            return response()->json([
                'id' => $product->id,
                'redirect_url' => route('admin.digital-products.edit', $product->id) . '#product-images',
                'status' => 'success',
                'message' => 'Product created successfully'
            ]);
        }
    }

    function edit(int $id)
    {

        $product = Product::findOrFail($id);
        // dd($product->attributes);
        $productCategoryIds = $product->categories->pluck('id')->toArray();
        $productTagIds = $product->tags->pluck('id')->toArray();
        $stores = Store::select(['name', 'id'])->get();
        $brands = Brand::select(['name', 'id'])->where('is_active', 1)->get();
        $tags = Tag::where('is_active', 1)->get();
        $categories = Category::getNested();

        $attributesWithValues = $product?->attributeWithValues ?? [];
        $variants = $product?->variants ?? [];
        // dd($attributesValues);
        return view('admin.product.edit', compact('stores', 'brands', 'tags', 'categories', 'product', 'productCategoryIds', 'productTagIds', 'attributesWithValues', 'variants'));
    }

    function editDigitalProduct(int $id)
    {

        $product = Product::findOrFail($id);
        if ($product->product_type != 'digital') abort(404);
        // dd($product->attributes);
        $productCategoryIds = $product->categories->pluck('id')->toArray();
        $productTagIds = $product->tags->pluck('id')->toArray();
        $stores = Store::select(['name', 'id'])->get();
        $brands = Brand::select(['name', 'id'])->where('is_active', 1)->get();
        $tags = Tag::where('is_active', 1)->get();
        $categories = Category::getNested();

        return view('admin.product.digital-edit', compact('stores', 'brands', 'tags', 'categories', 'product', 'productCategoryIds', 'productTagIds'));
    }

    function uploadDigitalProductFile(Request $request)
    {
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
        $product->approved_status = $request->approved_status;
        $product->store_id = $request->store;
        $product->brand_id = $request->brand;
        $product->is_featured = $request->has('is_featured') ? 1 : 0;
        $product->is_hot = $request->has('is_hot') ? 1 : 0;
        $product->is_new = $request->has('is_new') ? 1 : 0;
        $product->save();

        /** Attach categories */
        $product->categories()->sync($request->categories);

        /** Attach tags */
        $product->tags()->sync($request->tags);

        AlertService::created();

        return response()->json([
            'id' => $product->id,
            'status' => 'success',
            'message' => 'Product updated successfully',
            'redirect_url' => route('admin.products.index')
        ]);
    }

    function uploadImages(Request $request, Product $product)
    {

        $request->validate([
            'file' => ['required', 'image', 'max:3048']
        ]);

        $filePath = $this->uploadFile($request->file('file'));

        $productImage = new ProductImage();
        $productImage->product_id = $product->id;
        $productImage->path = $filePath;
        $productImage->order = ProductImage::where('product_id', 1)->max('order') + $product->id;
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
        $attribute = new Attribute();
        $attribute->name = $request->attribute_name;
        $attribute->type = $request->attribute_type;
        $attribute->save();

        $this->addAttributesValue($attribute, $request, $product);
    }

    function updateExistingAttribute(Request $request, Product $product)
    {
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
        DB::table('product_attribute_values')
            ->where('product_id', $product->id)
            ->where('attribute_id', $attribute->id)
            ->delete();

        AttributeValue::where('attribute_id', $attribute->id)->delete();
    }

    function addAttributesValue(Attribute $attribute, Request $request, Product $product)
    {
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
        $product->refresh();

        $attributes = $product->attributeWithValues;


        $html = '';
        $variantHtml = '';

        foreach ($attributes as $attribute) {

            $html .= view('admin.product.partials.attribute', compact('attribute', 'product'))->render();
        }

        foreach ($product->variants as $variant) {
            $variantHtml .= view('admin.product.partials.variant', compact('variant'))->render();
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
            $attribute = Attribute::findOrFail($attributeId);

            $this->clearAttributeData($attribute, $product);
            $this->regenerateProductVariants($product);

            $product->refresh();

            $attributes = $product->attributeWithValues;

            $attribute->delete();


            $html = '';
            $variantHtml = '';

            foreach ($attributes as $attribute) {

                $html .= view('admin.product.partials.attribute', compact('attribute', 'product'))->render();
            }

            foreach ($product->variants as $variant) {
                $variantHtml .= view('admin.product.partials.variant', compact('variant'))->render();
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
        if (Auth::user()->hasRole('Super Admin') || hasPermission(['Product Management'])) {
            $product->delete();
            notyf()->success('Product deleted successfully');
            return response()->json(['status' => 'success', 'message' => 'Product deleted successfully']);
        }

        notyf()->error('You do not have permission to delete this product');
        return response()->json(['status' => 'error', 'message' => 'You do not have permission to delete this product']);
    }
}
