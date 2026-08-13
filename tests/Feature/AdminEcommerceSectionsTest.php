<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\OfferSlider;
use App\Models\OurFeature;
use App\Models\Product;
use App\Models\Slider;
use App\Models\SocialLink;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $admin = Admin::query()->create([
        'name' => 'Test Admin',
        'email' => 'test-admin@example.com',
        'password' => 'password',
    ]);

    $this->actingAs($admin, 'admin');
});

test('ecommerce management pages render', function () {
    $this->get(route('admin.flash-sales.index'))->assertOk()->assertSee('Update Flash Sale Items');
    $this->get(route('admin.coupons.index'))->assertOk()->assertSee('All Coupons');
    $this->get(route('admin.shipping-rules.index'))->assertOk()->assertSee('All Shipping Rules');
});

test('homepage section management pages render', function () {
    $this->get(route('admin.offer-sliders.index'))->assertOk()->assertSee('All Offer Sliders');
    $this->get(route('admin.sliders.index'))->assertOk()->assertSee('All Sliders');
    $this->get(route('admin.hero-banners.index'))->assertOk()->assertSee('Banner One');
    $this->get(route('admin.popular-categories.index'))->assertOk()->assertSee('Update Popular Category');
    $this->get(route('admin.product-sections.index'))->assertOk()->assertSee('Update Product Section');
    $this->get(route('admin.our-features.index'))->assertOk()->assertSee('Our Features');
    $this->get(route('admin.social-links.index'))->assertOk()->assertSee('Social Links');
});

test('admin can manage an offer slider', function () {
    $this->post(route('admin.offer-sliders.store'), [
        'title' => 'Weekend savings',
        'url' => '/flash-sales',
        'is_active' => 1,
    ])->assertRedirect(route('admin.offer-sliders.index'));

    $offer = OfferSlider::query()->firstOrFail();

    $this->put(route('admin.offer-sliders.update', $offer), [
        'title' => 'Updated weekend savings',
        'url' => '/products',
    ])->assertRedirect(route('admin.offer-sliders.index'));

    $this->assertDatabaseHas('offer_sliders', [
        'id' => $offer->id,
        'title' => 'Updated weekend savings',
        'is_active' => 0,
    ]);

    $this->deleteJson(route('admin.offer-sliders.destroy', $offer))
        ->assertOk()
        ->assertJsonPath('status', 'success');
});

test('admin can manage homepage media sections', function () {
    $this->post(route('admin.sliders.store'), [
        'image' => UploadedFile::fake()->image('slider.jpg', 1200, 500),
        'title' => 'New collection',
        'sub_title' => 'Products selected for the season.',
        'btn_url' => '/products',
        'status' => 1,
    ])->assertRedirect(route('admin.sliders.index'));

    $slider = Slider::query()->firstOrFail();
    $this->deleteJson(route('admin.sliders.destroy', $slider))->assertOk();

    $this->post(route('admin.our-features.store'), [
        'icon' => UploadedFile::fake()->image('feature.png', 100, 100),
        'title' => 'Reliable delivery',
        'subtitle' => 'Track every shipment.',
        'status' => 1,
    ])->assertRedirect(route('admin.our-features.index'));

    $feature = OurFeature::query()->firstOrFail();
    $this->deleteJson(route('admin.our-features.destroy', $feature))->assertOk();

    $this->post(route('admin.social-links.store'), [
        'icon' => UploadedFile::fake()->image('social.png', 100, 100),
        'url' => 'https://example.com/shopx',
        'status' => 1,
    ])->assertRedirect(route('admin.social-links.index'));

    $socialLink = SocialLink::query()->firstOrFail();
    $this->deleteJson(route('admin.social-links.destroy', $socialLink))->assertOk();

    $this->post(route('admin.hero-banners.store'), [
        'title_one' => 'Audio essentials',
        'btn_url_one' => '/products',
        'title_two' => 'Everyday footwear',
        'btn_url_two' => '/products',
    ])->assertRedirect();

    $this->assertDatabaseHas('hero_banners', [
        'title_one' => 'Audio essentials',
        'title_two' => 'Everyday footwear',
    ]);
});

test('admin can configure homepage category sections', function () {
    $categories = collect(['Electronics', 'Fashion', 'Home'])->map(function (string $name) {
        return Category::query()->create([
            'name' => $name,
            'slug' => strtolower($name),
            'status' => 'active',
        ]);
    });

    $this->post(route('admin.popular-categories.store'), [
        'categories' => $categories->pluck('id')->all(),
    ])->assertRedirect();

    $this->post(route('admin.product-sections.store'), [
        'category_one' => $categories[0]->id,
        'category_two' => $categories[1]->id,
        'category_three' => $categories[2]->id,
    ])->assertRedirect();

    $this->assertDatabaseHas('product_sections', [
        'category_one' => $categories[0]->id,
        'category_two' => $categories[1]->id,
        'category_three' => $categories[2]->id,
    ]);
});

test('admin can configure a flash sale', function () {
    $product = Product::query()->create([
        'name' => 'Flash product',
        'slug' => 'flash-product',
        'price' => 20,
        'status' => 'active',
    ]);

    $this->post(route('admin.flash-sales.store'), [
        'sale_start' => now()->toDateString(),
        'sale_end' => now()->addWeek()->toDateString(),
        'products' => [$product->id],
        'status' => 1,
    ])->assertRedirect(route('admin.flash-sales.index'));

    $this->assertDatabaseHas('flash_sales', ['is_active' => 1]);
});

test('admin can create coupons and shipping rules', function () {
    $this->post(route('admin.coupons.store'), [
        'code' => 'save10',
        'value' => 10,
        'is_percent' => 1,
        'minimum_spend' => 20,
        'maximum_spend' => 500,
        'usage_limit_per_coupon' => 100,
        'usage_limit_per_customer' => 1,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addMonth()->toDateString(),
        'is_active' => 1,
    ])->assertRedirect(route('admin.coupons.index'));

    $this->post(route('admin.shipping-rules.store'), [
        'name' => 'Standard delivery',
        'type' => 'flat_amount',
        'charge' => 5,
        'is_active' => 1,
    ])->assertRedirect(route('admin.shipping-rules.index'));

    $this->assertDatabaseHas('coupons', [
        'code' => 'SAVE10',
        'usage_limit_per_customer' => 1,
    ]);
    $this->assertDatabaseHas('shipping_rules', [
        'name' => 'Standard delivery',
        'charge' => 5,
    ]);
});
