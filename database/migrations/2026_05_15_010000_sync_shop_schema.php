<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->createMissingTables();
        $this->syncExistingTables();
        $this->seedRequiredRows();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ([
            'withdraw_requests',
            'vendor_withdraw_methods',
            'withdraw_methods',
            'order_histories',
            'order_products',
            'admin_commissions',
            'banner_ads',
            'contact_section_settings',
            'custom_pages',
            'coupons',
            'shipping_rules',
            'settings',
            'subscribers',
            'contacts',
            'wishlists',
            'carts',
            'product_reviews',
            'product_variant_attribute_value',
            'product_attribute_values',
            'product_variants',
            'attribute_values',
            'attributes',
            'product_tag',
            'tags',
            'brands',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }

    private function createMissingTables(): void
    {
        if (! Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable()->index();
                $table->string('image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable()->index();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('product_tag')) {
            Schema::create('product_tag', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('tag_id')->index();
                $table->timestamps();
                $table->unique(['product_id', 'tag_id']);
            });
        }

        if (! Schema::hasTable('attributes')) {
            Schema::create('attributes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type')->default('text');
            });
        }

        if (! Schema::hasTable('attribute_values')) {
            Schema::create('attribute_values', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('attribute_id')->index();
                $table->string('value');
                $table->string('color')->nullable();
            });
        }

        if (! Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->string('name')->nullable();
                $table->string('sku')->nullable();
                $table->decimal('price', 12, 2)->default(0);
                $table->decimal('special_price', 12, 2)->nullable();
                $table->unsignedInteger('qty')->default(0);
                $table->boolean('manage_stock')->default(false);
                $table->boolean('in_stock')->default(true);
                $table->boolean('is_default')->default(false);
                $table->boolean('is_active')->default(true);
            });
        }

        if (! Schema::hasTable('product_attribute_values')) {
            Schema::create('product_attribute_values', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('attribute_id')->index();
                $table->unsignedBigInteger('attribute_value_id')->index();
            });
        }

        if (! Schema::hasTable('product_variant_attribute_value')) {
            Schema::create('product_variant_attribute_value', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_variant_id')->index();
                $table->unsignedBigInteger('attribute_id')->index();
                $table->unsignedBigInteger('attribute_value_id')->index();
            });
        }

        if (! Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->decimal('rating', 3, 1)->default(0);
                $table->text('review')->nullable();
            });
        }

        if (! Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variant_id')->nullable()->index();
                $table->unsignedInteger('quantity')->default(1);
                $table->string('name')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unique(['user_id', 'product_id']);
            });
        }

        if (! Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('subject')->nullable();
                $table->text('message')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('subscribers')) {
            Schema::create('subscribers', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('shipping_rules')) {
            Schema::create('shipping_rules', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type')->default('flat_amount');
                $table->decimal('minimum_amount', 12, 2)->default(0);
                $table->decimal('charge', 12, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('code')->unique();
                $table->decimal('value', 12, 2)->default(0);
                $table->boolean('is_percent')->default(false);
                $table->decimal('minimum_spend', 12, 2)->default(0);
                $table->decimal('maximum_spend', 12, 2)->default(99999999);
                $table->unsignedInteger('usage_limit_per_coupon')->default(999999);
                $table->unsignedInteger('used')->default(0);
                $table->dateTime('start_date')->nullable();
                $table->dateTime('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('custom_pages')) {
            Schema::create('custom_pages', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->longText('content')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('contact_section_settings')) {
            Schema::create('contact_section_settings', function (Blueprint $table) {
                $table->id();
                $table->longText('map_url')->nullable();
                foreach (['one', 'two', 'three'] as $suffix) {
                    $table->string("title_{$suffix}")->nullable();
                    $table->string("address_{$suffix}")->nullable();
                    $table->string("phone_{$suffix}")->nullable();
                    $table->string("email_{$suffix}")->nullable();
                }
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('banner_ads')) {
            Schema::create('banner_ads', function (Blueprint $table) {
                $table->id();
                $table->string('banner_id')->unique();
                $table->string('image')->nullable();
                $table->string('title')->nullable();
                $table->string('url')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('admin_commissions')) {
            Schema::create('admin_commissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->nullable()->index();
                $table->unsignedBigInteger('store_id')->nullable()->index();
                $table->decimal('commission_amount', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('order_products')) {
            Schema::create('order_products', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->unsignedBigInteger('product_id')->nullable()->index();
                $table->unsignedBigInteger('variant_id')->nullable()->index();
                $table->string('product_name')->nullable();
                $table->unsignedInteger('quantity')->default(1);
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('order_histories')) {
            Schema::create('order_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->string('status');
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('withdraw_methods')) {
            Schema::create('withdraw_methods', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->decimal('minimum_amount', 12, 2)->default(0);
                $table->decimal('maximum_amount', 12, 2)->default(0);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('vendor_withdraw_methods')) {
            Schema::create('vendor_withdraw_methods', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('vendor_id')->nullable()->index();
                $table->unsignedBigInteger('seller_id')->nullable()->index();
                $table->unsignedBigInteger('store_id')->nullable()->index();
                $table->unsignedBigInteger('withdraw_method_id')->nullable()->index();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('withdraw_requests')) {
            Schema::create('withdraw_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('vendor_id')->nullable()->index();
                $table->unsignedBigInteger('seller_id')->nullable()->index();
                $table->unsignedBigInteger('store_id')->nullable()->index();
                $table->unsignedBigInteger('method_id')->nullable()->index();
                $table->unsignedBigInteger('withdraw_method_id')->nullable()->index();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('payment_method')->nullable();
                $table->text('payment_details')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }
    }

    private function syncExistingTables(): void
    {
        $this->addColumn('users', 'phone', fn (Blueprint $table) => $table->string('phone')->nullable());
        $this->addColumn('users', 'store_name', fn (Blueprint $table) => $table->string('store_name')->nullable());
        $this->addColumn('users', 'shop_name', fn (Blueprint $table) => $table->string('shop_name')->nullable());
        $this->addColumn('users', 'address', fn (Blueprint $table) => $table->string('address')->nullable());

        foreach ([
            'user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('user_id')->nullable()->index(),
            'first_name' => fn (Blueprint $table) => $table->string('first_name')->nullable(),
            'last_name' => fn (Blueprint $table) => $table->string('last_name')->nullable(),
            'name' => fn (Blueprint $table) => $table->string('name')->nullable(),
            'phone' => fn (Blueprint $table) => $table->string('phone')->nullable(),
            'email' => fn (Blueprint $table) => $table->string('email')->nullable(),
            'city' => fn (Blueprint $table) => $table->string('city')->nullable(),
            'state' => fn (Blueprint $table) => $table->string('state')->nullable(),
            'zip' => fn (Blueprint $table) => $table->string('zip')->nullable(),
            'zip_code' => fn (Blueprint $table) => $table->string('zip_code')->nullable(),
            'postal_code' => fn (Blueprint $table) => $table->string('postal_code')->nullable(),
            'country' => fn (Blueprint $table) => $table->string('country')->nullable(),
            'address' => fn (Blueprint $table) => $table->string('address', 500)->nullable(),
            'is_default' => fn (Blueprint $table) => $table->boolean('is_default')->default(false),
        ] as $column => $definition) {
            $this->addColumn('addresses', $column, $definition);
        }

        foreach ([
            'vendor_id' => fn (Blueprint $table) => $table->unsignedBigInteger('vendor_id')->nullable()->index(),
            'user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('user_id')->nullable()->index(),
            'store_id' => fn (Blueprint $table) => $table->unsignedBigInteger('store_id')->nullable()->index(),
            'category_id' => fn (Blueprint $table) => $table->unsignedBigInteger('category_id')->nullable()->index(),
            'brand_id' => fn (Blueprint $table) => $table->unsignedBigInteger('brand_id')->nullable()->index(),
            'name' => fn (Blueprint $table) => $table->string('name')->nullable(),
            'slug' => fn (Blueprint $table) => $table->string('slug')->nullable()->index(),
            'sku' => fn (Blueprint $table) => $table->string('sku')->nullable(),
            'product_type' => fn (Blueprint $table) => $table->string('product_type')->default('physical'),
            'short_description' => fn (Blueprint $table) => $table->text('short_description')->nullable(),
            'description' => fn (Blueprint $table) => $table->longText('description')->nullable(),
            'long_description' => fn (Blueprint $table) => $table->longText('long_description')->nullable(),
            'image' => fn (Blueprint $table) => $table->string('image')->nullable(),
            'thumbnail' => fn (Blueprint $table) => $table->string('thumbnail')->nullable(),
            'thumb_image' => fn (Blueprint $table) => $table->string('thumb_image')->nullable(),
            'price' => fn (Blueprint $table) => $table->decimal('price', 12, 2)->default(0),
            'special_price' => fn (Blueprint $table) => $table->decimal('special_price', 12, 2)->nullable(),
            'offer_price' => fn (Blueprint $table) => $table->decimal('offer_price', 12, 2)->nullable(),
            'special_price_start' => fn (Blueprint $table) => $table->dateTime('special_price_start')->nullable(),
            'special_price_end' => fn (Blueprint $table) => $table->dateTime('special_price_end')->nullable(),
            'qty' => fn (Blueprint $table) => $table->unsignedInteger('qty')->default(0),
            'stock' => fn (Blueprint $table) => $table->unsignedInteger('stock')->default(0),
            'stock_qty' => fn (Blueprint $table) => $table->unsignedInteger('stock_qty')->default(0),
            'manage_stock' => fn (Blueprint $table) => $table->string('manage_stock')->default('no'),
            'in_stock' => fn (Blueprint $table) => $table->boolean('in_stock')->default(true),
            'status' => fn (Blueprint $table) => $table->string('status')->default('active'),
            'approved_status' => fn (Blueprint $table) => $table->string('approved_status')->default('approved'),
            'is_featured' => fn (Blueprint $table) => $table->boolean('is_featured')->default(false),
            'is_hot' => fn (Blueprint $table) => $table->boolean('is_hot')->default(false),
            'is_new' => fn (Blueprint $table) => $table->boolean('is_new')->default(false),
            'is_best' => fn (Blueprint $table) => $table->boolean('is_best')->default(false),
            'meta_title' => fn (Blueprint $table) => $table->string('meta_title')->nullable(),
            'meta_description' => fn (Blueprint $table) => $table->text('meta_description')->nullable(),
        ] as $column => $definition) {
            $this->addColumn('products', $column, $definition);
        }

        foreach ([
            'product_id' => fn (Blueprint $table) => $table->unsignedBigInteger('product_id')->nullable()->index(),
            'path' => fn (Blueprint $table) => $table->string('path')->nullable(),
            'image' => fn (Blueprint $table) => $table->string('image')->nullable(),
            'order' => fn (Blueprint $table) => $table->integer('order')->default(0),
            'position' => fn (Blueprint $table) => $table->integer('position')->default(0),
            'is_primary' => fn (Blueprint $table) => $table->boolean('is_primary')->default(false),
        ] as $column => $definition) {
            $this->addColumn('product_images', $column, $definition);
        }

        foreach ([
            'product_id' => fn (Blueprint $table) => $table->unsignedBigInteger('product_id')->nullable()->index(),
            'filename' => fn (Blueprint $table) => $table->string('filename')->nullable(),
            'name' => fn (Blueprint $table) => $table->string('name')->nullable(),
            'title' => fn (Blueprint $table) => $table->string('title')->nullable(),
            'path' => fn (Blueprint $table) => $table->string('path')->nullable(),
            'extension' => fn (Blueprint $table) => $table->string('extension')->nullable(),
            'size' => fn (Blueprint $table) => $table->unsignedBigInteger('size')->default(0),
            'file_size' => fn (Blueprint $table) => $table->unsignedBigInteger('file_size')->default(0),
        ] as $column => $definition) {
            $this->addColumn('product_files', $column, $definition);
        }

        foreach ([
            'user_id' => fn (Blueprint $table) => $table->unsignedBigInteger('user_id')->nullable()->index(),
            'vendor_id' => fn (Blueprint $table) => $table->unsignedBigInteger('vendor_id')->nullable()->index(),
            'seller_id' => fn (Blueprint $table) => $table->unsignedBigInteger('seller_id')->nullable()->index(),
            'store_id' => fn (Blueprint $table) => $table->unsignedBigInteger('store_id')->nullable()->index(),
            'product_id' => fn (Blueprint $table) => $table->unsignedBigInteger('product_id')->nullable()->index(),
            'invoice_id' => fn (Blueprint $table) => $table->string('invoice_id')->nullable()->index(),
            'transaction_id' => fn (Blueprint $table) => $table->string('transaction_id')->nullable(),
            'payment_method' => fn (Blueprint $table) => $table->string('payment_method')->nullable(),
            'payment_status' => fn (Blueprint $table) => $table->string('payment_status')->default('pending'),
            'order_status' => fn (Blueprint $table) => $table->string('order_status')->default('pending'),
            'status' => fn (Blueprint $table) => $table->string('status')->default('pending'),
            'currency' => fn (Blueprint $table) => $table->string('currency', 10)->nullable(),
            'currency_rate' => fn (Blueprint $table) => $table->decimal('currency_rate', 12, 4)->default(1),
            'sub_total' => fn (Blueprint $table) => $table->decimal('sub_total', 12, 2)->default(0),
            'shipping_charge' => fn (Blueprint $table) => $table->decimal('shipping_charge', 12, 2)->default(0),
            'shipping_cost' => fn (Blueprint $table) => $table->decimal('shipping_cost', 12, 2)->default(0),
            'total' => fn (Blueprint $table) => $table->decimal('total', 12, 2)->default(0),
            'qty' => fn (Blueprint $table) => $table->unsignedInteger('qty')->default(1),
            'product_qty' => fn (Blueprint $table) => $table->unsignedInteger('product_qty')->default(1),
            'billing_info' => fn (Blueprint $table) => $table->json('billing_info')->nullable(),
            'shipping_info' => fn (Blueprint $table) => $table->json('shipping_info')->nullable(),
            'billing_address' => fn (Blueprint $table) => $table->text('billing_address')->nullable(),
            'order_address' => fn (Blueprint $table) => $table->text('order_address')->nullable(),
        ] as $column => $definition) {
            $this->addColumn('orders', $column, $definition);
        }

        foreach ([
            'title' => fn (Blueprint $table) => $table->string('title')->nullable(),
            'categories' => fn (Blueprint $table) => $table->json('categories')->nullable(),
            'category_ids' => fn (Blueprint $table) => $table->json('category_ids')->nullable(),
            'is_active' => fn (Blueprint $table) => $table->boolean('is_active')->default(true),
        ] as $column => $definition) {
            $this->addColumn('popular_categories', $column, $definition);
        }

        foreach ([
            'title' => fn (Blueprint $table) => $table->string('title')->nullable(),
            'category_one' => fn (Blueprint $table) => $table->unsignedBigInteger('category_one')->nullable()->index(),
            'category_two' => fn (Blueprint $table) => $table->unsignedBigInteger('category_two')->nullable()->index(),
            'category_three' => fn (Blueprint $table) => $table->unsignedBigInteger('category_three')->nullable()->index(),
            'is_active' => fn (Blueprint $table) => $table->boolean('is_active')->default(true),
        ] as $column => $definition) {
            $this->addColumn('product_sections', $column, $definition);
        }
    }

    private function seedRequiredRows(): void
    {
        if (Schema::hasTable('banner_ads')) {
            foreach ([
                'banner_one',
                'banner_two',
                'banner_three',
                'banner_four',
                'banner_five',
                'banner_six',
                'banner_seven',
                'side_banner_one',
                'side_banner_two',
                'side_banner_three',
            ] as $bannerId) {
                DB::table('banner_ads')->updateOrInsert(
                    ['banner_id' => $bannerId],
                    [
                        'image' => '',
                        'title' => '',
                        'url' => '',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        if (Schema::hasTable('withdraw_methods') && ! DB::table('withdraw_methods')->exists()) {
            DB::table('withdraw_methods')->insert([
                [
                    'name' => 'Bank Transfer',
                    'minimum_amount' => 10,
                    'maximum_amount' => 10000,
                    'description' => 'Bank account details',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Manual Payment',
                    'minimum_amount' => 10,
                    'maximum_amount' => 10000,
                    'description' => 'Manual payout details',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    private function addColumn(string $table, string $column, callable $definition): void
    {
        if (! Schema::hasTable($table) || Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($definition) {
            $definition($table);
        });
    }
};
