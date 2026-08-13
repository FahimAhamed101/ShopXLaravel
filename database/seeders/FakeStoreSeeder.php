<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FakeStoreSeeder extends Seeder
{
    private const AVATAR_PATHS = [
        '/defaults/avatar.png',
        '/assets/frontend/dist/imgs/blog/author-1.png',
        '/assets/frontend/dist/imgs/blog/author-2.png',
        '/assets/frontend/dist/imgs/blog/author-3.png',
        '/assets/frontend/dist/imgs/blog/author-4.png',
    ];

    private const CATEGORY_IMAGE_PATHS = [
        '/assets/frontend/dist/imgs/shop/cat-1.png',
        '/assets/frontend/dist/imgs/shop/cat-2.png',
        '/assets/frontend/dist/imgs/shop/cat-3.png',
        '/assets/frontend/dist/imgs/shop/cat-4.png',
        '/assets/frontend/dist/imgs/shop/cat-5.png',
        '/assets/frontend/dist/imgs/shop/cat-6.png',
        '/assets/frontend/dist/imgs/shop/cat-7.png',
        '/assets/frontend/dist/imgs/shop/cat-8.png',
        '/assets/frontend/dist/imgs/shop/cat-9.png',
        '/assets/frontend/dist/imgs/shop/cat-10.png',
        '/assets/frontend/dist/imgs/shop/cat-11.png',
        '/assets/frontend/dist/imgs/shop/cat-12.png',
    ];

    private const PRODUCT_IMAGE_PATHS = [
        '/assets/frontend/dist/imgs/shop/product-1-1.jpg',
        '/assets/frontend/dist/imgs/shop/product-2-1.jpg',
        '/assets/frontend/dist/imgs/shop/product-3-1.jpg',
        '/assets/frontend/dist/imgs/shop/product-4-1.jpg',
        '/assets/frontend/dist/imgs/shop/product-5-1.jpg',
        '/assets/frontend/dist/imgs/shop/product-6-1.jpg',
        '/assets/frontend/dist/imgs/shop/product-7-1.jpg',
        '/assets/frontend/dist/imgs/shop/product-8-1.jpg',
        '/assets/frontend/dist/imgs/shop/product-9-1.jpg',
        '/assets/frontend/dist/imgs/shop/thumbnail-1.jpg',
        '/assets/frontend/dist/imgs/shop/thumbnail-2.jpg',
        '/assets/frontend/dist/imgs/shop/thumbnail-3.jpg',
    ];

    private const KYC_SOURCE_PATHS = [
        '/assets/frontend/dist/imgs/shop/thumbnail-4.jpg',
        '/assets/frontend/dist/imgs/shop/thumbnail-5.jpg',
        '/assets/frontend/dist/imgs/shop/thumbnail-6.jpg',
        '/assets/frontend/dist/imgs/shop/thumbnail-7.jpg',
    ];

    private array $tableColumns = [];

    public function run(): void
    {
        $this->seedAdmins();

        $users = $this->seedUsers();
        $vendors = $users->where('user_type', 'vendor')->values();
        $stores = $this->seedStores($vendors);

        $categories = $this->seedCategories();
        $products = $this->seedProducts($vendors, $categories, $stores);
        $this->syncProductStores($products, $stores);

        $this->seedCategoryProduct($categories, $products);
        $this->seedProductImages($products);
        $this->seedProductVariations($products);
        $this->seedProductTags($products);
        $this->seedProductFiles($products);

        $addresses = $this->seedAddresses($users);

        $this->seedKycs($vendors->isNotEmpty() ? $vendors : $users->take(4));
        $this->seedPopularCategories($categories);
        $this->seedProductSections($categories);
        $this->seedManageSections();
        $this->seedOrders($users, $products, $addresses);
        $this->seedProductReviews($users, $products);
    }

    protected function seedAdmins(): void
    {
        $password = Hash::make('1234');
        $rows = [
            [
                'avatar' => '/defaults/avatar.png',
                'name' => 'Super Admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now()->subMonths(6),
                'password' => $password,
                'remember_token' => Str::random(10),
            ],
            [
                'avatar' => '/defaults/avatar.png',
                'name' => 'Demo Admin',
                'email' => 'admin@example.com',
                'email_verified_at' => now()->subMonths(6),
                'password' => $password,
                'remember_token' => Str::random(10),
            ],
            [
                'avatar' => $this->pick(self::AVATAR_PATHS, 1),
                'name' => 'Operations Admin',
                'email' => 'ops@shopx.test',
                'email_verified_at' => now()->subMonths(4),
                'password' => $password,
                'remember_token' => Str::random(10),
            ],
        ];

        foreach ($rows as $index => $row) {
            DB::table('admins')->updateOrInsert(
                ['email' => $row['email']],
                $this->timestamped('admins', $row, 60 - ($index * 15))
            );
        }
    }

    protected function seedUsers(): Collection
    {
        $password = Hash::make('1234');

        $profiles = [
            ['name' => 'Test User', 'email' => 'user@gmail.com', 'user_type' => 'user'],
            ['name' => 'Demo Vendor', 'email' => 'vendor@gmail.com', 'user_type' => 'vendor'],
            ['name' => 'Demo Vendor', 'email' => 'vendor@example.com', 'user_type' => 'vendor'],
            ['name' => 'Ariana Khan', 'email' => 'ariana@shopx.test', 'user_type' => 'user'],
            ['name' => 'Mahmud Hasan', 'email' => 'mahmud@shopx.test', 'user_type' => 'user'],
            ['name' => 'Luna Fashion', 'email' => 'luna-fashion@shopx.test', 'user_type' => 'vendor'],
            ['name' => 'Nova Gadgets', 'email' => 'nova-gadgets@shopx.test', 'user_type' => 'vendor'],
            ['name' => 'Sadia Noor', 'email' => 'sadia@shopx.test', 'user_type' => 'user'],
            ['name' => 'Karim Uddin', 'email' => 'karim@shopx.test', 'user_type' => 'user'],
            ['name' => 'Urban Basket', 'email' => 'urban-basket@shopx.test', 'user_type' => 'vendor'],
            ['name' => 'Tanin Ahmed', 'email' => 'tanin@shopx.test', 'user_type' => 'user'],
        ];

        foreach ($profiles as $index => $profile) {
            $isVendor = $profile['user_type'] === 'vendor';

            DB::table('users')->updateOrInsert(
                ['email' => $profile['email']],
                $this->timestamped('users', [
                    'avatar' => $this->pick(self::AVATAR_PATHS, $index),
                    'name' => $profile['name'],
                    'email' => $profile['email'],
                    'email_verified_at' => now()->subDays(120 - ($index * 5)),
                    'password' => $password,
                    'user_type' => $profile['user_type'],
                    'remember_token' => Str::random(10),
                    'phone' => sprintf('+8801700%04d', 1000 + $index),
                    'store_name' => $isVendor ? $profile['name'] : null,
                    'shop_name' => $isVendor ? $profile['name'] : null,
                    'address' => fake()->streetAddress(),
                ], 40 - min($index, 35))
            );
        }

        return DB::table('users')->orderBy('id')->get();
    }

    protected function seedCategories(): Collection
    {
        $groups = [
            'Electronics' => ['Smartphones', 'Laptops'],
            'Fashion' => ['Mens Wear', 'Womens Wear'],
            'Beauty' => ['Skin Care', 'Makeup'],
            'Home Living' => ['Kitchen', 'Furniture'],
            'Groceries' => ['Snacks', 'Beverages'],
            'Sports' => ['Fitness', 'Outdoor'],
        ];

        $imageIndex = 0;
        $parentPosition = 1;

        foreach ($groups as $parentName => $children) {
            $parentSlug = Str::slug($parentName);

            DB::table('categories')->updateOrInsert(
                ['slug' => $parentSlug],
                $this->timestamped('categories', [
                    'name' => $parentName,
                    'slug' => $parentSlug,
                    'parent_id' => null,
                    'position' => $parentPosition++,
                    'image' => $this->pick(self::CATEGORY_IMAGE_PATHS, $imageIndex++),
                    'icon' => $this->pick(self::CATEGORY_IMAGE_PATHS, $imageIndex++),
                    'is_active' => true,
                    'is_featured' => true,
                    'description' => "{$parentName} seed category",
                ], 30)
            );

            $parentId = DB::table('categories')->where('slug', $parentSlug)->value('id');

            foreach ($children as $childPosition => $childName) {
                $childSlug = Str::slug($parentName.' '.$childName);

                DB::table('categories')->updateOrInsert(
                    ['slug' => $childSlug],
                    $this->timestamped('categories', [
                        'name' => $childName,
                        'slug' => $childSlug,
                        'parent_id' => $parentId,
                        'position' => $childPosition + 1,
                        'image' => $this->pick(self::CATEGORY_IMAGE_PATHS, $imageIndex++),
                        'icon' => $this->pick(self::CATEGORY_IMAGE_PATHS, $imageIndex++),
                        'is_active' => true,
                        'is_featured' => false,
                        'description' => "{$childName} sub-category",
                    ], 25 - $childPosition)
                );
            }
        }

        return DB::table('categories')->orderBy('id')->get();
    }

    protected function seedStores(Collection $vendors): Collection
    {
        if (! $this->tableExists('stores') || $vendors->isEmpty()) {
            return collect();
        }

        foreach ($vendors as $index => $vendor) {
            DB::table('stores')->updateOrInsert(
                ['seller_id' => $vendor->id],
                $this->timestamped('stores', [
                    'seller_id' => $vendor->id,
                    'logo' => $vendor->avatar ?? '/defaults/avatar.png',
                    'banner' => '/assets/frontend/dist/imgs/vendor/vendor-header-bg.png',
                    'name' => $vendor->store_name ?? $vendor->shop_name ?? $vendor->name,
                    'phone' => $vendor->phone ?? null,
                    'email' => $vendor->email ?? null,
                    'address' => $vendor->address ?? null,
                    'short_description' => ($vendor->name ?? 'Vendor').' store profile',
                    'long_description' => 'Seeded vendor store for demo products.',
                    'is_active' => true,
                ], 35 - min($index, 30))
            );
        }

        return DB::table('stores')->orderBy('id')->get();
    }

    protected function seedProducts(Collection $vendors, Collection $categories, Collection $stores): Collection
    {
        $categoryIds = $categories->keyBy('slug');
        $seededSkus = [];

        foreach ($this->productBlueprints() as $index => $product) {
            $sku = sprintf('SHOPX-%04d', $index + 1);
            $vendorId = $vendors->isNotEmpty()
                ? $vendors[$index % $vendors->count()]->id
                : null;

            $storeId = $vendorId && $stores->isNotEmpty()
                ? $stores->firstWhere('seller_id', $vendorId)?->id
                : null;

            $categoryId = $categoryIds->get($product['category_slug'])?->id;

            $payload = $this->timestamped('products', [
                'vendor_id' => $vendorId,
                'user_id' => $vendorId,
                'store_id' => $storeId,
                'category_id' => $categoryId,
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'sku' => $sku,
                'short_description' => $product['short_description'],
                'description' => $product['description'],
                'long_description' => $product['description'],
                'image' => $product['images'][0],
                'thumbnail' => $product['images'][0],
                'thumb_image' => $product['images'][0],
                'price' => $product['price'],
                'offer_price' => $product['offer_price'],
                'qty' => collect($product['variants'])->sum('qty'),
                'stock' => collect($product['variants'])->sum('qty'),
                'stock_qty' => collect($product['variants'])->sum('qty'),
                'manage_stock' => 'yes',
                'in_stock' => true,
                'status' => 'active',
                'approved_status' => 'approved',
                'product_type' => $product['product_type'],
                'is_featured' => $product['is_featured'],
                'is_new' => $product['is_new'],
                'is_hot' => $product['is_hot'],
                'is_best' => $product['is_best'],
                'meta_title' => $product['name'].' | ShopX',
                'meta_description' => $product['short_description'],
            ], 20 - min($index, 18));

            $existingId = DB::table('products')->where('sku', $sku)->value('id');

            if ($existingId) {
                unset($payload['created_at']);
                DB::table('products')->where('id', $existingId)->update($payload);
            } else {
                DB::table('products')->insert($payload);
            }

            $seededSkus[] = $sku;
        }

        return DB::table('products')->whereIn('sku', $seededSkus)->orderBy('sku')->get();
    }

    protected function syncProductStores(Collection $products, Collection $stores): void
    {
        if (! $this->tableExists('products')
            || ! isset($this->columnsFor('products')['store_id'])
            || $products->isEmpty()
            || $stores->isEmpty()) {
            return;
        }

        foreach ($products as $product) {
            if (filled($product->store_id ?? null)) {
                continue;
            }

            $vendorId = $product->vendor_id ?? $product->user_id ?? null;
            $storeId = $vendorId ? $stores->firstWhere('seller_id', $vendorId)?->id : null;

            if ($storeId) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['store_id' => $storeId, 'updated_at' => now()]);
            }
        }
    }

    protected function seedCategoryProduct(Collection $categories, Collection $products): void
    {
        if (! $this->tableExists('category_product') || $products->isEmpty()) {
            return;
        }

        $blueprints = collect($this->productBlueprints())->values();
        $categoryIds = $categories->keyBy('slug');
        DB::table('category_product')->whereIn('product_id', $products->pluck('id'))->delete();
        $rows = [];

        foreach ($products->values() as $index => $product) {
            $categoryId = $categoryIds->get($blueprints[$index]['category_slug'])?->id;

            if (! $categoryId) {
                continue;
            }

            $rows[] = $this->timestamped('category_product', [
                'category_id' => $categoryId,
                'product_id' => $product->id,
            ], 12);
        }

        if ($rows !== []) {
            DB::table('category_product')->insert($rows);
        }
    }

    protected function seedProductImages(Collection $products): void
    {
        if (! $this->tableExists('product_images') || $products->isEmpty()) {
            return;
        }

        $blueprints = collect($this->productBlueprints())->values();
        DB::table('product_images')->whereIn('product_id', $products->pluck('id'))->delete();
        $rows = [];

        foreach ($products->values() as $index => $product) {
            foreach ($blueprints[$index]['images'] as $position => $image) {
                $rows[] = $this->timestamped('product_images', [
                    'product_id' => $product->id,
                    'path' => $image,
                    'image' => $image,
                    'is_primary' => $position === 0,
                    'position' => $position + 1,
                    'order' => $position + 1,
                ], 10 - min($position, 9));
            }
        }

        DB::table('product_images')->insert($rows);
    }

    protected function seedProductVariations(Collection $products): void
    {
        $requiredTables = [
            'attributes',
            'attribute_values',
            'product_attribute_values',
            'product_variants',
            'product_variant_attribute_value',
        ];

        if ($products->isEmpty() || collect($requiredTables)->contains(fn (string $table) => ! $this->tableExists($table))) {
            return;
        }

        $productIds = $products->pluck('id');
        $variantIds = DB::table('product_variants')->whereIn('product_id', $productIds)->pluck('id');
        DB::table('product_variant_attribute_value')->whereIn('product_variant_id', $variantIds)->delete();
        DB::table('product_variants')->whereIn('product_id', $productIds)->delete();
        DB::table('product_attribute_values')->whereIn('product_id', $productIds)->delete();

        foreach ($products->values() as $index => $product) {
            $blueprint = $this->productBlueprints()[$index];
            $valuesByAttribute = [];

            foreach ($blueprint['attributes'] as $attributeDefinition) {
                DB::table('attributes')->updateOrInsert(
                    ['name' => $attributeDefinition['name']],
                    ['type' => $attributeDefinition['type']],
                );
                $attributeId = DB::table('attributes')->where('name', $attributeDefinition['name'])->value('id');

                foreach ($attributeDefinition['values'] as $valueDefinition) {
                    DB::table('attribute_values')->updateOrInsert(
                        ['attribute_id' => $attributeId, 'value' => $valueDefinition['label']],
                        ['color' => $valueDefinition['color'] ?? null],
                    );
                    $valueId = DB::table('attribute_values')
                        ->where('attribute_id', $attributeId)
                        ->where('value', $valueDefinition['label'])
                        ->value('id');

                    DB::table('product_attribute_values')->insert([
                        'product_id' => $product->id,
                        'attribute_id' => $attributeId,
                        'attribute_value_id' => $valueId,
                    ]);
                    $valuesByAttribute[$attributeDefinition['name']][$valueDefinition['label']] = [
                        'attribute_id' => $attributeId,
                        'value_id' => $valueId,
                    ];
                }
            }

            foreach ($blueprint['variants'] as $variantIndex => $variantDefinition) {
                $variantId = DB::table('product_variants')->insertGetId([
                    'product_id' => $product->id,
                    'name' => collect($variantDefinition['options'])->values()->implode(' / '),
                    'sku' => $product->sku.'-'.strtoupper(collect($variantDefinition['options'])->values()->map(fn ($value) => Str::slug($value))->implode('-')),
                    'price' => $variantDefinition['price'],
                    'special_price' => $variantDefinition['special_price'] ?? null,
                    'qty' => $variantDefinition['qty'],
                    'manage_stock' => true,
                    'in_stock' => $variantDefinition['qty'] > 0,
                    'is_default' => $variantIndex === 0,
                    'is_active' => true,
                ]);

                foreach ($variantDefinition['options'] as $attributeName => $valueLabel) {
                    $value = $valuesByAttribute[$attributeName][$valueLabel];
                    DB::table('product_variant_attribute_value')->insert([
                        'product_variant_id' => $variantId,
                        'attribute_id' => $value['attribute_id'],
                        'attribute_value_id' => $value['value_id'],
                    ]);
                }
            }
        }
    }

    protected function seedProductFiles(Collection $products): void
    {
        if (! $this->tableExists('product_files') || $products->isEmpty()) {
            return;
        }

        DB::table('product_files')->whereIn('product_id', $products->pluck('id'))->delete();
        $rows = [];

        foreach ($products->where('product_type', 'digital')->values() as $index => $product) {
            $filePath = $this->ensurePrivateTextFile(
                "seeders/product-files/product-{$product->id}.txt",
                "Demo file for seeded product #{$product->id}"
            );

            $rows[] = $this->timestamped('product_files', [
                'product_id' => $product->id,
                'name' => "Product {$product->id} File",
                'title' => "Product {$product->id} File",
                'path' => $filePath,
                'file' => $filePath,
                'extension' => 'txt',
                'file_type' => 'txt',
                'download_limit' => 5,
            ], 8 - min($index, 7));
        }

        if ($rows !== []) {
            DB::table('product_files')->insert($rows);
        }
    }

    protected function seedProductTags(Collection $products): void
    {
        if (! $this->tableExists('tags') || ! $this->tableExists('product_tag') || $products->isEmpty()) {
            return;
        }

        $tagsByProduct = [
            ['Apparel', 'Cotton'],
            ['Organic', 'Beverages'],
            ['Furniture', 'Seating'],
            ['Television', '4K'],
            ['Footwear', 'Fitness'],
            ['Watches', 'Accessories'],
            ['Apparel', 'Hoodies'],
            ['Camera', 'Photography'],
            ['Apparel', 'Denim'],
            ['Skin Care', 'Moisturizer'],
            ['Audio', 'Wireless'],
            ['Kids', 'Clothing'],
        ];

        DB::table('product_tag')->whereIn('product_id', $products->pluck('id'))->delete();

        foreach ($products->values() as $index => $product) {
            foreach ($tagsByProduct[$index] as $tagName) {
                $slug = Str::slug($tagName);
                DB::table('tags')->updateOrInsert(
                    ['slug' => $slug],
                    $this->timestamped('tags', ['name' => $tagName, 'slug' => $slug, 'is_active' => true]),
                );
                $tagId = DB::table('tags')->where('slug', $slug)->value('id');
                DB::table('product_tag')->insert($this->timestamped('product_tag', [
                    'product_id' => $product->id,
                    'tag_id' => $tagId,
                ]));
            }
        }
    }

    protected function seedProductReviews(Collection $users, Collection $products): void
    {
        if (! $this->tableExists('product_reviews') || $users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $reviewers = $users->where('user_type', 'user')->values();
        $comments = [
            'The product matched the photos and arrived in excellent condition.',
            'Good quality for the price, and the selected variation fits perfectly.',
            'Accurate description, solid build quality, and quick delivery.',
        ];
        $ratings = [5, 4, 4.5];

        foreach ($products->values() as $productIndex => $product) {
            foreach ($reviewers->take(3) as $reviewIndex => $reviewer) {
                DB::table('product_reviews')->updateOrInsert(
                    ['product_id' => $product->id, 'user_id' => $reviewer->id],
                    $this->timestamped('product_reviews', [
                        'rating' => $ratings[($productIndex + $reviewIndex) % count($ratings)],
                        'review' => $comments[($productIndex + $reviewIndex) % count($comments)],
                    ], 10 - $reviewIndex),
                );
            }
        }
    }

    protected function seedAddresses(Collection $users): Collection
    {
        if (! $this->tableHasRows('addresses') && $users->isNotEmpty()) {
            $rows = [];

            foreach ($users->values()->take(8) as $index => $user) {
                $rows[] = $this->timestamped('addresses', [
                    'user_id' => $user->id,
                    'name' => $user->name ?? 'Seed Address',
                    'email' => $user->email ?? null,
                    'phone' => sprintf('+8801900%04d', 2000 + $index),
                    'country' => 'Bangladesh',
                    'state' => 'Dhaka',
                    'city' => 'Dhaka',
                    'zip_code' => sprintf('%04d', 1200 + $index),
                    'postal_code' => sprintf('%04d', 1200 + $index),
                    'address' => fake()->streetAddress(),
                    'type' => $index === 0 ? 'home' : 'shipping',
                    'is_default' => $index === 0,
                ], 14 - min($index, 13));
            }

            DB::table('addresses')->insert($rows);
        }

        return $this->tableExists('addresses')
            ? DB::table('addresses')->orderBy('id')->get()
            : collect();
    }

    protected function seedKycs(Collection $users): void
    {
        if (! $this->tableExists('kycs') || DB::table('kycs')->exists() || $users->isEmpty()) {
            return;
        }

        $statuses = ['approved', 'pending', 'rejected'];
        $documentTypes = ['passport', 'driving_license', 'id_card'];
        $rows = [];

        foreach ($users->values()->take(4) as $index => $user) {
            $status = $statuses[$index % count($statuses)];
            $documentPath = $this->ensurePrivateImageCopy(
                $this->pick(self::KYC_SOURCE_PATHS, $index),
                "seeders/kyc/document-{$user->id}.jpg"
            );

            $rows[] = $this->timestamped('kycs', [
                'user_id' => $user->id,
                'status' => $status,
                'rejected_reason' => $status === 'rejected' ? 'The uploaded copy is intentionally marked as a sample seed record.' : null,
                'verified_at' => now()->subDays(5 - min($index, 4)),
                'full_name' => $user->name ?? 'Seed User',
                'date_of_birth' => now()->subYears(24 + $index)->format('Y-m-d'),
                'gender' => $index % 2 === 0 ? 'male' : 'female',
                'full_address' => fake()->address(),
                'document_type' => $documentTypes[$index % count($documentTypes)],
                'document_scan_copy' => $documentPath,
            ], 7 - min($index, 6));
        }

        DB::table('kycs')->insert($rows);
    }

    protected function seedPopularCategories(Collection $categories): void
    {
        if (! $this->tableExists('popular_categories') || DB::table('popular_categories')->exists()) {
            return;
        }

        $categoryIds = $categories
            ->filter(fn (object $category) => blank($category->parent_id ?? null))
            ->pluck('id')
            ->take(6)
            ->values()
            ->all();

        DB::table('popular_categories')->insert([
            $this->timestamped('popular_categories', [
                'title' => 'Popular Categories',
                'categories' => json_encode($categoryIds),
                'category_ids' => json_encode($categoryIds),
                'is_active' => true,
            ], 6),
        ]);
    }

    protected function seedProductSections(Collection $categories): void
    {
        if (! $this->tableExists('product_sections') || DB::table('product_sections')->exists()) {
            return;
        }

        $rootCategoryIds = $categories
            ->filter(fn (object $category) => blank($category->parent_id ?? null))
            ->pluck('id')
            ->values();

        DB::table('product_sections')->insert([
            $this->timestamped('product_sections', [
                'title' => 'Featured Product Sections',
                'category_one' => $rootCategoryIds->get(0),
                'category_two' => $rootCategoryIds->get(1),
                'category_three' => $rootCategoryIds->get(2),
                'is_active' => true,
            ], 5),
        ]);
    }

    protected function seedManageSections(): void
    {
        if ($this->tableExists('sliders') && ! DB::table('sliders')->exists()) {
            DB::table('sliders')->insert([
                $this->timestamped('sliders', [
                    'image' => '/assets/frontend/dist/imgs/slider/slider-1.png',
                    'title' => 'Upgrade Your Everyday Tech',
                    'sub_title' => 'Discover smart electronics, audio, and accessories selected for daily life.',
                    'btn_url' => '/products',
                    'is_active' => true,
                ], 4),
                $this->timestamped('sliders', [
                    'image' => '/assets/frontend/dist/imgs/slider/slider-2.png',
                    'title' => 'Fresh Styles for Every Season',
                    'sub_title' => 'Shop comfortable apparel and footwear in colors and sizes you will love.',
                    'btn_url' => '/products?category=fashion',
                    'is_active' => true,
                ], 3),
            ]);
        }

        if ($this->tableExists('hero_banners') && ! DB::table('hero_banners')->exists()) {
            DB::table('hero_banners')->insert($this->timestamped('hero_banners', [
                'banner_one' => '/assets/frontend/dist/imgs/banner/banner-1.png',
                'title_one' => 'Wireless Audio Essentials',
                'btn_url_one' => '/products',
                'banner_two' => '/assets/frontend/dist/imgs/banner/banner-2.png',
                'title_two' => 'Comfortable Everyday Footwear',
                'btn_url_two' => '/products',
            ], 4));
        }

        if ($this->tableExists('offer_sliders') && ! DB::table('offer_sliders')->exists()) {
            DB::table('offer_sliders')->insert([
                $this->timestamped('offer_sliders', ['title' => 'Free delivery on qualifying orders', 'url' => '/products', 'is_active' => true], 3),
                $this->timestamped('offer_sliders', ['title' => 'Explore this week’s featured products', 'url' => '/flash-sales', 'is_active' => true], 2),
            ]);
        }

        if ($this->tableExists('our_features') && ! DB::table('our_features')->exists()) {
            $features = [
                ['/assets/frontend/dist/imgs/theme/icons/icon-1.png', 'Best prices and offers', 'Competitive pricing across the catalog.'],
                ['/assets/frontend/dist/imgs/theme/icons/icon-2.png', 'Fast delivery', 'Reliable shipping with clear order tracking.'],
                ['/assets/frontend/dist/imgs/theme/icons/icon-3.png', 'Easy returns', 'Straightforward support when an order is not right.'],
                ['/assets/frontend/dist/imgs/theme/icons/icon-4.png', 'Secure payments', 'Protected checkout with trusted payment providers.'],
            ];

            DB::table('our_features')->insert(collect($features)->map(fn (array $feature, int $index) => $this->timestamped('our_features', [
                'icon' => $feature[0],
                'title' => $feature[1],
                'subtitle' => $feature[2],
                'status' => true,
            ], 4 - $index))->all());
        }

        if ($this->tableExists('social_links') && ! DB::table('social_links')->exists()) {
            DB::table('social_links')->insert([
                $this->timestamped('social_links', ['icon' => '/assets/frontend/dist/imgs/theme/icons/social-fb.svg', 'url' => 'https://www.facebook.com/', 'status' => true], 2),
                $this->timestamped('social_links', ['icon' => '/assets/frontend/dist/imgs/theme/icons/social-insta.svg', 'url' => 'https://www.instagram.com/', 'status' => true], 1),
            ]);
        }
    }

    protected function seedOrders(Collection $users, Collection $products, Collection $addresses): void
    {
        if (! $this->tableExists('orders') || DB::table('orders')->exists()) {
            return;
        }

        $statuses = ['pending', 'processed', 'packed', 'shipped', 'delivered'];
        $paymentMethods = ['cash_on_delivery', 'stripe', 'paypal'];
        $rows = [];

        foreach (range(0, 11) as $index) {
            $user = $users[$index % $users->count()] ?? null;
            $product = $products[$index % $products->count()] ?? null;
            $address = $addresses->isNotEmpty() ? $addresses[$index % $addresses->count()] : null;

            $quantity = ($index % 4) + 1;
            $subTotal = 45 + ($index * 8);
            $shippingCost = 5 + ($index % 3);
            $discount = $index % 3 === 0 ? 4 : 0;
            $total = $subTotal + $shippingCost - $discount;
            $status = $statuses[$index % count($statuses)];

            $rows[] = $this->timestamped('orders', [
                'user_id' => $user?->id,
                'product_id' => $product?->id,
                'address_id' => $address?->id,
                'invoice_id' => sprintf('INV-%s-%03d', now()->format('Ymd'), $index + 1),
                'sub_total' => $subTotal,
                'total' => $total,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'qty' => $quantity,
                'product_qty' => $quantity,
                'status' => $status,
                'payment_status' => $status === 'delivered' ? 'completed' : 'pending',
                'payment_method' => $paymentMethods[$index % count($paymentMethods)],
                'order_address' => $address->address ?? fake()->streetAddress(),
                'billing_address' => $address->address ?? fake()->streetAddress(),
                'order_notes' => fake()->sentence(),
            ], 12 - min($index, 11));
        }

        DB::table('orders')->insert($rows);
    }

    protected function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    protected function tableHasRows(string $table): bool
    {
        return $this->tableExists($table) && DB::table($table)->exists();
    }

    protected function columnsFor(string $table): array
    {
        if (! isset($this->tableColumns[$table])) {
            $this->tableColumns[$table] = array_flip(Schema::getColumnListing($table));
        }

        return $this->tableColumns[$table];
    }

    protected function onlyExistingColumns(string $table, array $attributes): array
    {
        if (! $this->tableExists($table)) {
            return [];
        }

        return array_intersect_key($attributes, $this->columnsFor($table));
    }

    protected function timestamped(string $table, array $attributes, int $daysAgo = 0): array
    {
        $payload = $this->onlyExistingColumns($table, $attributes);
        $timestamp = now()->subDays(max($daysAgo, 0));

        if (isset($this->columnsFor($table)['created_at']) && ! array_key_exists('created_at', $payload)) {
            $payload['created_at'] = $timestamp;
        }

        if (isset($this->columnsFor($table)['updated_at']) && ! array_key_exists('updated_at', $payload)) {
            $payload['updated_at'] = $timestamp;
        }

        return $payload;
    }

    protected function pick(array $items, int $index): string
    {
        return $items[$index % count($items)];
    }

    protected function ensurePrivateImageCopy(string $publicPath, string $storagePath): string
    {
        if (! Storage::disk('local')->exists($storagePath)) {
            $contents = file_get_contents(public_path(ltrim($publicPath, '/')));
            Storage::disk('local')->put($storagePath, $contents);
        }

        return $storagePath;
    }

    protected function ensurePrivateTextFile(string $storagePath, string $contents): string
    {
        if (! Storage::disk('local')->exists($storagePath)) {
            Storage::disk('local')->put($storagePath, $contents);
        }

        return $storagePath;
    }

    protected function productBlueprints(): array
    {
        $image = fn (string $filename): string => '/assets/frontend/dist/imgs/shop/'.$filename;
        $attribute = fn (string $name, string $type, array $values): array => [
            'name' => $name,
            'type' => $type,
            'values' => collect($values)->map(fn ($value, $label) => is_string($label)
                ? ['label' => $label, 'color' => $value]
                : ['label' => $value])->values()->all(),
        ];
        $variant = fn (array $options, float $price, ?float $specialPrice, int $qty): array => [
            'options' => $options,
            'price' => $price,
            'special_price' => $specialPrice,
            'qty' => $qty,
        ];
        $product = fn (
            string $name,
            string $shortDescription,
            string $description,
            string $categorySlug,
            array $images,
            float $price,
            float $offerPrice,
            array $attributes,
            array $variants,
            array $flags = [],
        ): array => array_merge([
            'name' => $name,
            'short_description' => $shortDescription,
            'description' => $description,
            'category_slug' => $categorySlug,
            'images' => $images,
            'price' => $price,
            'offer_price' => $offerPrice,
            'product_type' => 'physical',
            'is_featured' => false,
            'is_new' => false,
            'is_hot' => false,
            'is_best' => false,
            'attributes' => $attributes,
            'variants' => $variants,
        ], $flags);

        return [
            $product(
                'Men’s Casual Shirt Collection',
                'Soft cotton shirts in plaid and washed-denim styles.',
                'A breathable everyday shirt collection with reinforced seams, button cuffs, and a comfortable regular fit.',
                'fashion-mens-wear',
                [$image('product-1-1.jpg'), $image('product-1-2.jpg'), $image('product-9-1.jpg'), $image('product-9-2.jpg')],
                42,
                36,
                [$attribute('Style', 'text', ['Blue Plaid', 'Light Denim']), $attribute('Size', 'text', ['M', 'L'])],
                [
                    $variant(['Style' => 'Blue Plaid', 'Size' => 'M'], 42, 36, 18),
                    $variant(['Style' => 'Blue Plaid', 'Size' => 'L'], 42, 36, 14),
                    $variant(['Style' => 'Light Denim', 'Size' => 'M'], 46, 39, 12),
                    $variant(['Style' => 'Light Denim', 'Size' => 'L'], 46, 39, 10),
                ],
                ['is_featured' => true, 'is_new' => true],
            ),
            $product(
                '365 Organic Aloe Vera Juice',
                'Organic whole-leaf aloe vera juice in a family-size bottle.',
                'A refreshing, unsweetened aloe vera drink made for daily use. Refrigerate after opening and serve chilled.',
                'groceries-beverages',
                [$image('product-2-1.jpg')],
                24,
                21,
                [$attribute('Pack Size', 'text', ['Single Bottle', 'Two-Pack'])],
                [
                    $variant(['Pack Size' => 'Single Bottle'], 24, 21, 35),
                    $variant(['Pack Size' => 'Two-Pack'], 45, 39, 20),
                ],
                ['is_hot' => true],
            ),
            $product(
                'Modern Chair Collection',
                'Supportive seating for living rooms, offices, and gaming spaces.',
                'Choose a warm tan accent chair for relaxed interiors or a high-back gaming chair with adjustable support.',
                'home-living-furniture',
                [$image('product-3-1.jpg'), $image('product-2-2.jpg'), $image('product-6-2.jpg')],
                249,
                219,
                [$attribute('Style', 'text', ['Tan Lounge', 'Red Gaming'])],
                [
                    $variant(['Style' => 'Tan Lounge'], 249, 219, 9),
                    $variant(['Style' => 'Red Gaming'], 289, 259, 7),
                ],
                ['is_featured' => true, 'is_best' => true],
            ),
            $product(
                '4K UHD Smart Television',
                'Slim 4K television with streaming apps and HDR picture processing.',
                'Enjoy crisp Ultra HD video, built-in Wi-Fi, popular streaming services, and multiple HDMI inputs.',
                'electronics-laptops',
                [$image('product-4-1.jpg'), $image('thumbnail-8.jpg')],
                449,
                399,
                [$attribute('Screen Size', 'text', ['43-inch', '55-inch'])],
                [
                    $variant(['Screen Size' => '43-inch'], 449, 399, 11),
                    $variant(['Screen Size' => '55-inch'], 599, 549, 8),
                ],
                ['is_featured' => true, 'is_hot' => true],
            ),
            $product(
                'Women’s Performance Sneakers',
                'Cushioned athletic shoes with breathable uppers and grippy soles.',
                'Lightweight training shoes designed for walking, gym sessions, and comfortable everyday wear.',
                'sports-fitness',
                [$image('product-5-1.jpg'), $image('product-3-2.jpg'), $image('thumbnail-12.jpg')],
                89,
                75,
                [$attribute('Color', 'color', ['Lilac' => '#b9a6d8', 'Blue' => '#2457d6']), $attribute('Size', 'text', ['7', '9'])],
                [
                    $variant(['Color' => 'Lilac', 'Size' => '7'], 89, 75, 16),
                    $variant(['Color' => 'Lilac', 'Size' => '9'], 89, 75, 12),
                    $variant(['Color' => 'Blue', 'Size' => '7'], 94, 79, 10),
                    $variant(['Color' => 'Blue', 'Size' => '9'], 94, 79, 8),
                ],
                ['is_new' => true, 'is_best' => true],
            ),
            $product(
                'Classic Analog Wristwatch',
                'A precise analog watch offered with leather and steel straps.',
                'A versatile timepiece featuring an easy-to-read dial, durable case, and comfortable interchangeable strap styles.',
                'fashion-mens-wear',
                [$image('product-6-1.jpg'), $image('product-4-2.jpg'), $image('product-8-1.jpg'), $image('product-8-2.jpg'), $image('thumbnail-5.jpg')],
                129,
                109,
                [$attribute('Strap', 'text', ['Black Leather', 'Black Steel', 'Brown Leather'])],
                [
                    $variant(['Strap' => 'Black Leather'], 129, 109, 15),
                    $variant(['Strap' => 'Black Steel'], 149, 129, 10),
                    $variant(['Strap' => 'Brown Leather'], 139, 119, 9),
                ],
                ['is_featured' => true, 'is_best' => true],
            ),
            $product(
                'Unisex Cotton Hoodie',
                'Midweight cotton-blend fleece with a relaxed everyday fit.',
                'A soft pullover layer with ribbed cuffs, a roomy hood, and colors that work across seasons.',
                'fashion-womens-wear',
                [$image('product-7-1.jpg'), $image('product-5-2.jpg'), $image('product-7-2.jpg')],
                54,
                46,
                [$attribute('Color', 'color', ['Forest Green' => '#3f8f58', 'Dusty Rose' => '#c88f79']), $attribute('Size', 'text', ['M', 'L'])],
                [
                    $variant(['Color' => 'Forest Green', 'Size' => 'M'], 54, 46, 18),
                    $variant(['Color' => 'Forest Green', 'Size' => 'L'], 54, 46, 14),
                    $variant(['Color' => 'Dusty Rose', 'Size' => 'M'], 54, 46, 15),
                    $variant(['Color' => 'Dusty Rose', 'Size' => 'L'], 54, 46, 11),
                ],
                ['is_new' => true, 'is_hot' => true],
            ),
            $product(
                'Mirrorless Camera Kit',
                'Compact interchangeable-lens camera for detailed photos and 4K video.',
                'A travel-friendly camera with fast autofocus, manual controls, Wi-Fi sharing, and an optional everyday zoom lens.',
                'electronics-smartphones',
                [$image('thumbnail-4.jpg')],
                749,
                699,
                [$attribute('Kit', 'text', ['Body Only', '18-55mm Lens'])],
                [
                    $variant(['Kit' => 'Body Only'], 749, 699, 6),
                    $variant(['Kit' => '18-55mm Lens'], 899, 829, 5),
                ],
                ['is_featured' => true, 'is_new' => true],
            ),
            $product(
                'Men’s Washed Denim Shirt',
                'A durable denim button-down in two versatile washes.',
                'Made from soft washed cotton denim with a classic collar, chest pockets, and a comfortable straight cut.',
                'fashion-mens-wear',
                [$image('product-9-1.jpg'), $image('product-1-2.jpg')],
                48,
                41,
                [$attribute('Wash', 'text', ['Light Blue', 'Medium Blue']), $attribute('Size', 'text', ['M', 'L'])],
                [
                    $variant(['Wash' => 'Light Blue', 'Size' => 'M'], 48, 41, 13),
                    $variant(['Wash' => 'Light Blue', 'Size' => 'L'], 48, 41, 10),
                    $variant(['Wash' => 'Medium Blue', 'Size' => 'M'], 50, 43, 12),
                    $variant(['Wash' => 'Medium Blue', 'Size' => 'L'], 50, 43, 9),
                ],
                ['is_hot' => true],
            ),
            $product(
                'Daily Hydrating Body Lotion',
                'Lightweight daily moisturizer for smooth, comfortable skin.',
                'A fast-absorbing body lotion with a clean finish, suited to normal and dry skin without a greasy feel.',
                'beauty-skin-care',
                [$image('thumbnail-1.jpg'), $image('thumbnail-7.jpg')],
                18,
                15,
                [$attribute('Volume', 'text', ['250 ml', '500 ml'])],
                [
                    $variant(['Volume' => '250 ml'], 18, 15, 28),
                    $variant(['Volume' => '500 ml'], 29, 25, 19),
                ],
                ['is_new' => true],
            ),
            $product(
                'Wireless Over-Ear Headphones',
                'Comfortable wireless headphones with clear sound and deep bass.',
                'Foldable over-ear headphones with cushioned earcups, Bluetooth connectivity, a built-in microphone, and USB-C charging.',
                'electronics-smartphones',
                [$image('thumbnail-3.jpg')],
                79,
                65,
                [$attribute('Connection', 'text', ['Bluetooth 5.3', 'USB-C Wired'])],
                [
                    $variant(['Connection' => 'Bluetooth 5.3'], 79, 65, 22),
                    $variant(['Connection' => 'USB-C Wired'], 69, 59, 16),
                ],
                ['is_featured' => true, 'is_hot' => true],
            ),
            $product(
                'Kids Clothing and Sneaker Set',
                'Coordinated children’s outfit with comfortable everyday sneakers.',
                'A playful matching set made for active days, with a soft top, easy-fit bottoms, and lightweight shoes.',
                'fashion-womens-wear',
                [$image('thumbnail-6.jpg')],
                45,
                39,
                [$attribute('Kids Size', 'text', ['2-3 Years', '4-5 Years'])],
                [
                    $variant(['Kids Size' => '2-3 Years'], 45, 39, 12),
                    $variant(['Kids Size' => '4-5 Years'], 49, 42, 10),
                ],
                ['is_new' => true, 'is_best' => true],
            ),
        ];
    }

    protected function legacyProductBlueprints(): array
    {
        return [
            [
                'name' => 'Noise Cancelling Headphones',
                'short_description' => 'Wireless over-ear headphones with deep bass and fast charging.',
                'description' => 'A seeded demo product for storefront previews, quick cards, and catalog listings.',
                'image' => $this->pick(self::PRODUCT_IMAGE_PATHS, 0),
                'price' => 129.00,
                'offer_price' => 109.00,
                'qty' => 28,
                'product_type' => 'physical',
                'is_featured' => true,
                'is_new' => true,
                'is_hot' => true,
                'is_best' => false,
            ],
            [
                'name' => 'Smart Fitness Watch',
                'short_description' => 'AMOLED smartwatch with health tracking and 7-day battery life.',
                'description' => 'Designed to give the seeded catalog a wearable tech product with image coverage.',
                'image' => $this->pick(self::PRODUCT_IMAGE_PATHS, 1),
                'price' => 95.00,
                'offer_price' => 84.00,
                'qty' => 40,
                'product_type' => 'physical',
                'is_featured' => true,
                'is_new' => true,
                'is_hot' => false,
                'is_best' => true,
            ],
            [
                'name' => 'Leather Travel Backpack',
                'short_description' => 'Everyday backpack with padded laptop sleeve and hidden pocket.',
                'description' => 'Useful seeded item for fashion and lifestyle category pages.',
                'image' => $this->pick(self::PRODUCT_IMAGE_PATHS, 2),
                'price' => 72.00,
                'offer_price' => 64.00,
                'qty' => 24,
                'product_type' => 'physical',
                'is_featured' => false,
                'is_new' => true,
                'is_hot' => true,
                'is_best' => false,
            ],
            [
                'name' => 'Ergonomic Office Chair',
                'short_description' => 'Mesh back chair with lumbar support and adjustable armrests.',
                'description' => 'Seeded furniture product with enough metadata for card and detail page demos.',
                'image' => $this->pick(self::PRODUCT_IMAGE_PATHS, 3),
                'price' => 210.00,
                'offer_price' => 189.00,
                'qty' => 12,
                'product_type' => 'physical',
                'is_featured' => true,
                'is_new' => false,
                'is_hot' => false,
                'is_best' => true,
            ],
            [
                'name' => 'Vitamin C Skin Serum',
                'short_description' => 'Brightening serum with hyaluronic acid and lightweight texture.',
                'description' => 'A beauty-focused seeded product for home and category layouts.',
                'image' => $this->pick(self::PRODUCT_IMAGE_PATHS, 4),
                'price' => 32.00,
                'offer_price' => 27.00,
                'qty' => 64,
                'product_type' => 'physical',
                'is_featured' => false,
                'is_new' => true,
                'is_hot' => true,
                'is_best' => false,
            ],
            [
                'name' => 'Ceramic Coffee Set',
                'short_description' => 'Minimal ceramic cup set for home kitchen displays.',
                'description' => 'Included to add home-goods variety to seeded shop pages.',
                'image' => $this->pick(self::PRODUCT_IMAGE_PATHS, 5),
                'price' => 44.00,
                'offer_price' => 38.00,
                'qty' => 30,
                'product_type' => 'physical',
                'is_featured' => false,
                'is_new' => false,
                'is_hot' => false,
                'is_best' => true,
            ],
            [
                'name' => 'Wireless Gaming Mouse',
                'short_description' => 'Lightweight RGB mouse with programmable side buttons.',
                'description' => 'Tech catalog seed data with a strong card image and compact copy.',
                'image' => $this->pick(self::PRODUCT_IMAGE_PATHS, 6),
                'price' => 58.00,
                'offer_price' => 49.00,
                'qty' => 35,
                'product_type' => 'physical',
                'is_featured' => true,
                'is_new' => false,
                'is_hot' => true,
                'is_best' => false,
            ],
            [
                'name' => 'Linen Bed Sheet Set',
                'short_description' => 'Breathable linen sheet set with soft neutral finish.',
                'description' => 'Seed record intended for lifestyle and home decor blocks.',
                'image' => $this->pick(self::PRODUCT_IMAGE_PATHS, 7),
                'price' => 88.00,
                'offer_price' => 76.00,
                'qty' => 18,
                'product_type' => 'physical',
                'is_featured' => false,
                'is_new' => true,
                'is_hot' => false,
                'is_best' => false,
            ],
            [
                'name' => 'Portable Blender Bottle',
                'short_description' => 'USB rechargeable blender for smoothies on the go.',
                'description' => 'A small appliance item for seeded promotional sections.',
                'image' => $this->pick(self::PRODUCT_IMAGE_PATHS, 8),
                'price' => 39.00,
                'offer_price' => 33.00,
                'qty' => 22,
                'product_type' => 'physical',
                'is_featured' => true,
                'is_new' => true,
                'is_hot' => false,
                'is_best' => true,
            ],
            [
                'name' => 'Stainless Water Bottle',
                'short_description' => 'Insulated steel bottle that keeps drinks cold for hours.',
                'description' => 'Seeded accessory product for outdoor and fitness collections.',
                'image' => $this->pick(self::PRODUCT_IMAGE_PATHS, 9),
                'price' => 26.00,
                'offer_price' => 22.00,
                'qty' => 55,
                'product_type' => 'physical',
                'is_featured' => false,
                'is_new' => false,
                'is_hot' => true,
                'is_best' => false,
            ],
            [
                'name' => 'Sneaker Runner Pro',
                'short_description' => 'Breathable running sneakers with cushioned sole.',
                'description' => 'Fashion and sports hybrid product for seeded storefront grids.',
                'image' => $this->pick(self::PRODUCT_IMAGE_PATHS, 10),
                'price' => 110.00,
                'offer_price' => 95.00,
                'qty' => 20,
                'product_type' => 'physical',
                'is_featured' => true,
                'is_new' => false,
                'is_hot' => true,
                'is_best' => true,
            ],
            [
                'name' => 'Digital Recipe Pack',
                'short_description' => 'Downloadable recipe collection for healthy meals.',
                'description' => 'A seeded digital product so product file seeding has a realistic use case.',
                'image' => $this->pick(self::PRODUCT_IMAGE_PATHS, 11),
                'price' => 18.00,
                'offer_price' => 15.00,
                'qty' => 999,
                'product_type' => 'digital',
                'is_featured' => false,
                'is_new' => true,
                'is_hot' => false,
                'is_best' => false,
            ],
        ];
    }
}
