<?php

use Database\Seeders\FakeStoreSeeder;
use Illuminate\Support\Facades\DB;

test('catalog seeder creates matching product images and variations idempotently', function () {
    $this->seed(FakeStoreSeeder::class);
    $this->seed(FakeStoreSeeder::class);

    $products = DB::table('products')
        ->where('sku', 'like', 'SHOPX-%')
        ->orderBy('sku')
        ->get();
    $productIds = $products->pluck('id');

    expect($products)->toHaveCount(12)
        ->and(DB::table('product_images')->whereIn('product_id', $productIds)->count())->toBe(28)
        ->and(DB::table('product_variants')->whereIn('product_id', $productIds)->count())->toBe(33)
        ->and(DB::table('product_reviews')->whereIn('product_id', $productIds)->count())->toBe(36)
        ->and(DB::table('product_tag')->whereIn('product_id', $productIds)->count())->toBe(24);

    $aloeJuice = $products->firstWhere('sku', 'SHOPX-0002');

    expect($aloeJuice->name)->toBe('365 Organic Aloe Vera Juice')
        ->and($aloeJuice->image)->toBe('/assets/frontend/dist/imgs/shop/product-2-1.jpg')
        ->and(DB::table('product_images')->where('product_id', $aloeJuice->id)->orderBy('position')->pluck('path')->all())
        ->toBe([
            '/assets/frontend/dist/imgs/shop/product-2-1.jpg',
        ]);

    $watch = $products->firstWhere('sku', 'SHOPX-0006');
    $watchVariantIds = DB::table('product_variants')->where('product_id', $watch->id)->pluck('id');

    expect($watch->name)->toBe('Classic Analog Wristwatch')
        ->and(DB::table('product_variants')->where('product_id', $watch->id)->count())->toBe(3)
        ->and(DB::table('product_variant_attribute_value')->whereIn('product_variant_id', $watchVariantIds)->count())->toBe(3)
        ->and(DB::table('product_images')->where('product_id', $watch->id)->orderBy('position')->pluck('path')->all())
        ->toBe([
            '/assets/frontend/dist/imgs/shop/product-6-1.jpg',
            '/assets/frontend/dist/imgs/shop/product-4-2.jpg',
            '/assets/frontend/dist/imgs/shop/product-8-1.jpg',
            '/assets/frontend/dist/imgs/shop/product-8-2.jpg',
            '/assets/frontend/dist/imgs/shop/thumbnail-5.jpg',
        ]);

    $this->get(route('products.show', $watch->slug))
        ->assertOk()
        ->assertSeeText('Classic Analog Wristwatch')
        ->assertSeeText('Black Leather')
        ->assertSeeText('Black Steel')
        ->assertSeeText('Brown Leather')
        ->assertSee('product-6-1.jpg', false)
        ->assertDontSee('product-2-1.jpg', false);
});
