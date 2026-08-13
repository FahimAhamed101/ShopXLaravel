<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('purchased products page shows paid order items and hides unpaid items', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $paidProduct = Product::query()->create([
        'name' => 'Paid physical product',
        'slug' => 'paid-physical-product',
        'product_type' => 'physical',
        'price' => 30,
        'status' => 'active',
    ]);
    $unpaidProduct = Product::query()->create([
        'name' => 'Unpaid product',
        'slug' => 'unpaid-product',
        'product_type' => 'physical',
        'price' => 40,
        'status' => 'active',
    ]);

    $paidOrder = Order::query()->create([
        'user_id' => $user->id,
        'invoice_id' => 'INV-PAID-001',
        'payment_status' => 'paid',
        'status' => 'pending',
        'total' => 30,
    ]);
    $unpaidOrder = Order::query()->create([
        'user_id' => $user->id,
        'invoice_id' => 'INV-PENDING-001',
        'payment_status' => 'pending',
        'status' => 'pending',
        'total' => 40,
    ]);

    foreach ([[$paidOrder, $paidProduct], [$unpaidOrder, $unpaidProduct]] as [$order, $product]) {
        DB::table('order_products')->insert([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => $product->price,
            'total' => $product->price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $this->actingAs($user)
        ->get(route('purchased.products'))
        ->assertOk()
        ->assertSeeText('INV-PAID-001')
        ->assertSeeText('Paid physical product')
        ->assertSeeText('View Product')
        ->assertDontSeeText('Unpaid product');

    $this->get(route('purchased.products.show', $paidProduct))
        ->assertNotFound();
});

test('users cannot open another customers purchased product', function () {
    $owner = User::factory()->create(['email_verified_at' => now()]);
    $otherUser = User::factory()->create(['email_verified_at' => now()]);
    $product = Product::query()->create([
        'name' => 'Private digital product',
        'slug' => 'private-digital-product',
        'product_type' => 'digital',
        'price' => 15,
        'status' => 'active',
    ]);
    $order = Order::query()->create([
        'user_id' => $owner->id,
        'payment_status' => 'paid',
        'status' => 'pending',
        'total' => 15,
    ]);
    DB::table('order_products')->insert([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'quantity' => 1,
        'unit_price' => 15,
        'total' => 15,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($otherUser)
        ->get(route('purchased.products.show', $product))
        ->assertNotFound();
});
