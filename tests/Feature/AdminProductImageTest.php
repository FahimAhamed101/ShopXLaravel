<?php

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $admin = Admin::query()->create([
        'name' => 'Product Image Admin',
        'email' => 'product-image-admin@example.com',
        'password' => 'password',
    ]);

    $this->actingAs($admin, 'admin');
});

test('admin can upload and delete a product image', function () {
    $product = Product::query()->create([
        'name' => 'Image product',
        'slug' => 'image-product',
        'price' => 20,
        'status' => 'active',
    ]);

    $upload = $this->postJson(route('admin.products.images.upload', $product), [
        'file' => UploadedFile::fake()->image('product.jpg'),
    ])->assertOk();

    $image = ProductImage::query()->findOrFail($upload->json('id'));
    $absolutePath = public_path($image->path);

    expect(is_file($absolutePath))->toBeTrue();

    $this->deleteJson(route('admin.products.images.destroy', $image->id))
        ->assertOk()
        ->assertJsonPath('status', 'success');

    $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
    expect(is_file($absolutePath))->toBeFalse();
});

test('deleting a seeded gallery record preserves its bundled asset', function () {
    $product = Product::query()->create([
        'name' => 'Seeded image product',
        'slug' => 'seeded-image-product',
        'price' => 20,
        'status' => 'active',
    ]);
    $assetPath = 'assets/frontend/dist/imgs/shop/product-1-1.jpg';
    $image = ProductImage::query()->create([
        'product_id' => $product->id,
        'path' => '/'.$assetPath,
        'order' => 1,
    ]);

    expect(is_file(public_path($assetPath)))->toBeTrue();

    $this->deleteJson(route('admin.products.images.destroy', $image->id))
        ->assertOk();

    $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
    expect(is_file(public_path($assetPath)))->toBeTrue();
});
