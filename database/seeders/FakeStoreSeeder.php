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

        $categories = $this->seedCategories();
        $products = $this->seedProducts($vendors, $categories);

        $this->seedCategoryProduct($categories, $products);
        $this->seedProductImages($products);
        $this->seedProductFiles($products);

        $addresses = $this->seedAddresses($users);

        $this->seedKycs($vendors->isNotEmpty() ? $vendors : $users->take(4));
        $this->seedPopularCategories($categories);
        $this->seedProductSections($categories);
        $this->seedOrders($users, $products, $addresses);
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

    protected function seedProducts(Collection $vendors, Collection $categories): Collection
    {
        if ($this->tableHasRows('products')) {
            return DB::table('products')->orderBy('id')->get();
        }

        $leafCategoryIds = $categories
            ->filter(fn (object $category) => filled($category->parent_id ?? null))
            ->pluck('id')
            ->values();

        $rows = [];

        foreach ($this->productBlueprints() as $index => $product) {
            $vendorId = $vendors->isNotEmpty()
                ? $vendors[$index % $vendors->count()]->id
                : null;

            $categoryId = $leafCategoryIds->isNotEmpty()
                ? $leafCategoryIds[$index % $leafCategoryIds->count()]
                : null;

            $rows[] = $this->timestamped('products', [
                'vendor_id' => $vendorId,
                'user_id' => $vendorId,
                'category_id' => $categoryId,
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'sku' => sprintf('SHOPX-%04d', $index + 1),
                'short_description' => $product['short_description'],
                'description' => $product['description'],
                'long_description' => $product['description'],
                'image' => $product['image'],
                'thumbnail' => $product['image'],
                'thumb_image' => $product['image'],
                'price' => $product['price'],
                'offer_price' => $product['offer_price'],
                'qty' => $product['qty'],
                'stock' => $product['qty'],
                'stock_qty' => $product['qty'],
                'product_type' => $product['product_type'],
                'is_featured' => $product['is_featured'],
                'is_new' => $product['is_new'],
                'is_hot' => $product['is_hot'],
                'is_best' => $product['is_best'],
                'meta_title' => $product['name'].' | ShopX',
                'meta_description' => $product['short_description'],
            ], 20 - min($index, 18));
        }

        DB::table('products')->insert($rows);

        return DB::table('products')->orderBy('id')->get();
    }

    protected function seedCategoryProduct(Collection $categories, Collection $products): void
    {
        if (! $this->tableExists('category_product') || DB::table('category_product')->exists()) {
            return;
        }

        $categoryIds = $categories
            ->filter(fn (object $category) => filled($category->parent_id ?? null))
            ->pluck('id')
            ->values();

        if ($categoryIds->isEmpty()) {
            $categoryIds = $categories->pluck('id')->values();
        }

        if ($categoryIds->isEmpty() || $products->isEmpty()) {
            return;
        }

        $rows = [];

        foreach ($products->values() as $index => $product) {
            $firstCategoryId = $categoryIds[$index % $categoryIds->count()];
            $secondCategoryId = $categoryIds[($index + 3) % $categoryIds->count()];

            $rows[] = $this->timestamped('category_product', [
                'category_id' => $firstCategoryId,
                'product_id' => $product->id,
            ], 12);

            if ($secondCategoryId !== $firstCategoryId) {
                $rows[] = $this->timestamped('category_product', [
                    'category_id' => $secondCategoryId,
                    'product_id' => $product->id,
                ], 11);
            }
        }

        DB::table('category_product')->insert($rows);
    }

    protected function seedProductImages(Collection $products): void
    {
        if (! $this->tableExists('product_images') || DB::table('product_images')->exists() || $products->isEmpty()) {
            return;
        }

        $rows = [];

        foreach ($products->values() as $index => $product) {
            $primaryImage = $this->pick(self::PRODUCT_IMAGE_PATHS, $index);
            $secondaryImage = $this->pick(self::PRODUCT_IMAGE_PATHS, $index + 1);

            $rows[] = $this->timestamped('product_images', [
                'product_id' => $product->id,
                'path' => $primaryImage,
                'image' => $primaryImage,
                'is_primary' => true,
                'position' => 1,
            ], 10);

            $rows[] = $this->timestamped('product_images', [
                'product_id' => $product->id,
                'path' => $secondaryImage,
                'image' => $secondaryImage,
                'is_primary' => false,
                'position' => 2,
            ], 9);
        }

        DB::table('product_images')->insert($rows);
    }

    protected function seedProductFiles(Collection $products): void
    {
        if (! $this->tableExists('product_files') || DB::table('product_files')->exists() || $products->isEmpty()) {
            return;
        }

        $rows = [];

        foreach ($products->values()->take(8) as $index => $product) {
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

        DB::table('product_files')->insert($rows);
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
