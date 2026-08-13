<?php

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;

test('authenticated cart quantity and totals persist after refresh', function () {
    $user = User::factory()->create();
    $product = Product::query()->create([
        'name' => 'Quantity product',
        'slug' => 'quantity-product',
        'price' => 10,
        'qty' => 3,
        'manage_stock' => 'yes',
        'in_stock' => true,
        'status' => 'active',
    ]);
    $cartItem = new Cart;
    $cartItem->user_id = $user->id;
    $cartItem->product_id = $product->id;
    $cartItem->quantity = 1;
    $cartItem->name = $product->name;
    $cartItem->save();

    $response = $this->actingAs($user)
        ->putJson(route('cart.update'), ['id' => $cartItem->id, 'qty' => 3])
        ->assertOk()
        ->assertJsonPath('cart_sub_total', 30)
        ->assertJsonPath('total', 30);

    expect($response->json('html'))->toContain('value="3"');

    $this->assertDatabaseHas('carts', ['id' => $cartItem->id, 'quantity' => 3]);

    $this->get(route('cart.index'))
        ->assertOk()
        ->assertSee('value="3"', false)
        ->assertSeeText('$ 30');
});

test('guest cart quantity and totals persist in the session', function () {
    $product = Product::query()->create([
        'name' => 'Guest quantity product',
        'slug' => 'guest-quantity-product',
        'price' => 12,
        'qty' => 5,
        'manage_stock' => 'yes',
        'in_stock' => true,
        'status' => 'active',
    ]);
    $cartItemId = $product->id.'-product';

    $this->withSession([
        'guest_cart' => [
            $cartItemId => [
                'id' => $cartItemId,
                'product_id' => $product->id,
                'variant_id' => null,
                'quantity' => 1,
                'name' => $product->name,
            ],
        ],
    ])->putJson(route('cart.update'), ['id' => $cartItemId, 'qty' => 4])
        ->assertOk()
        ->assertJsonPath('cart_sub_total', 48)
        ->assertJsonPath('total', 48);

    expect(session("guest_cart.{$cartItemId}.quantity"))->toBe(4);

    $this->get(route('cart.index'))
        ->assertOk()
        ->assertSee('value="4"', false)
        ->assertSeeText('$ 48');
});

test('cart rejects quantities above stock and accepts the exact stock amount', function () {
    $product = Product::query()->create([
        'name' => 'Limited product',
        'slug' => 'limited-product',
        'price' => 8,
        'qty' => 2,
        'manage_stock' => 'yes',
        'in_stock' => true,
        'status' => 'active',
    ]);
    $cartItemId = $product->id.'-product';
    $session = [
        'guest_cart' => [
            $cartItemId => [
                'id' => $cartItemId,
                'product_id' => $product->id,
                'variant_id' => null,
                'quantity' => 1,
                'name' => $product->name,
            ],
        ],
    ];

    $this->withSession($session)
        ->putJson(route('cart.update'), ['id' => $cartItemId, 'qty' => 2])
        ->assertOk();

    $this->putJson(route('cart.update'), ['id' => $cartItemId, 'qty' => 3])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Product out of stock');

    expect(session("guest_cart.{$cartItemId}.quantity"))->toBe(2);
});
