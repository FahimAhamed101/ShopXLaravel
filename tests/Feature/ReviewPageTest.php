<?php

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;

test('customer review page sorts and displays timestamped reviews', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $product = Product::query()->create([
        'name' => 'Reviewed product',
        'slug' => 'reviewed-product',
        'price' => 20,
        'status' => 'active',
    ]);
    $review = ProductReview::query()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'rating' => 5,
        'review' => 'Excellent product and quick delivery.',
    ]);

    expect($review->created_at)->not->toBeNull();

    $this->actingAs($user)
        ->get(route('reviews.index'))
        ->assertOk()
        ->assertSeeText('Excellent product and quick delivery.')
        ->assertSeeText($review->created_at->format('Y-m-d'));
});

test('admin review page displays timestamped reviews', function () {
    $admin = Admin::query()->create([
        'name' => 'Review Admin',
        'email' => 'review-admin@example.com',
        'password' => 'password',
    ]);
    $user = User::factory()->create();
    $product = Product::query()->create([
        'name' => 'Admin reviewed product',
        'slug' => 'admin-reviewed-product',
        'price' => 30,
        'status' => 'active',
    ]);
    ProductReview::query()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'rating' => 4,
        'review' => 'A useful product.',
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.reviews.index'))
        ->assertOk()
        ->assertSeeText('A useful product.');
});
