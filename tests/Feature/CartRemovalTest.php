<?php

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;

test('guest can remove a cart item using the delete form', function () {
    $product = Product::query()->create([
        'name' => 'Cart product',
        'slug' => 'cart-product',
        'price' => 20,
        'status' => 'active',
        'in_stock' => true,
    ]);

    $this->postJson(route('cart.add'), [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertOk();

    $cartItemId = $product->id.'-product';

    $this->get(route('cart.index'))
        ->assertOk()
        ->assertSee('action="'.route('cart.destroy', $cartItemId).'"', false)
        ->assertSee('name="_method" value="DELETE"', false);

    $this->delete(route('cart.destroy', $cartItemId))
        ->assertRedirect(route('cart.index'));

    expect(session('guest_cart', []))->not->toHaveKey($cartItemId);
});

test('authenticated user cannot remove another users cart item', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::query()->create([
        'name' => 'Private cart product',
        'slug' => 'private-cart-product',
        'price' => 25,
        'status' => 'active',
    ]);
    $cartItem = new Cart;
    $cartItem->user_id = $owner->id;
    $cartItem->product_id = $product->id;
    $cartItem->quantity = 1;
    $cartItem->name = $product->name;
    $cartItem->save();

    $this->actingAs($otherUser)
        ->delete(route('cart.destroy', $cartItem->id))
        ->assertNotFound();

    $this->assertDatabaseHas('carts', ['id' => $cartItem->id]);
});
