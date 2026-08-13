<?php

use App\Models\Address;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;

function validAddressData(array $overrides = []): array
{
    return array_merge([
        'first_name' => 'Test',
        'last_name' => 'Customer',
        'email' => 'customer@example.com',
        'phone' => '+8801700000000',
        'address' => '123 Test Road',
        'city' => 'Dhaka',
        'state' => 'Dhaka',
        'zip' => '1207',
        'country' => 'Bangladesh',
        'is_default' => 0,
    ], $overrides);
}

test('address page contains a working inline form', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $this->actingAs($user)
        ->get(route('address.index'))
        ->assertOk()
        ->assertSeeText('Add a New Address')
        ->assertSee('action="'.route('address.store').'"', false)
        ->assertSee('name="address"', false);
});

test('first address becomes default and a new default replaces it', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $this->actingAs($user);

    $this->post(route('address.store'), validAddressData())
        ->assertRedirect(route('address.index'));

    $firstAddress = Address::query()->where('user_id', $user->id)->firstOrFail();
    expect($firstAddress->is_default)->toBeTruthy();

    $this->post(route('address.store'), validAddressData([
        'first_name' => 'Second',
        'email' => 'second@example.com',
        'address' => '456 New Road',
        'is_default' => 1,
    ]))->assertRedirect(route('address.index'));

    $secondAddress = Address::query()->where('user_id', $user->id)->latest('id')->firstOrFail();

    expect((bool) $firstAddress->refresh()->is_default)->toBeFalse()
        ->and((bool) $secondAddress->is_default)->toBeTrue();
});

test('deleting the default promotes another owned address', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $first = Address::query()->create(array_merge(validAddressData(), [
        'user_id' => $user->id,
        'name' => 'Test Customer',
        'is_default' => 0,
    ]));
    $default = Address::query()->create(array_merge(validAddressData([
        'email' => 'default@example.com',
        'address' => '789 Default Road',
    ]), [
        'user_id' => $user->id,
        'name' => 'Default Customer',
        'is_default' => 1,
    ]));

    $this->actingAs($user)
        ->delete(route('address.destroy', $default))
        ->assertRedirect(route('address.index'));

    expect((bool) $first->refresh()->is_default)->toBeTrue();
});

test('users cannot modify another customers address', function () {
    $owner = User::factory()->create(['email_verified_at' => now()]);
    $otherUser = User::factory()->create(['email_verified_at' => now()]);
    $address = Address::query()->create(array_merge(validAddressData(), [
        'user_id' => $owner->id,
        'name' => 'Private Address',
        'is_default' => 1,
    ]));

    $this->actingAs($otherUser)
        ->patch(route('address.default', $address))
        ->assertNotFound();

    $this->delete(route('address.destroy', $address))
        ->assertNotFound();

    $this->assertDatabaseHas('addresses', ['id' => $address->id]);
});

test('checkout displays the custom address form and returns after saving', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $product = Product::query()->create([
        'name' => 'Checkout address product',
        'slug' => 'checkout-address-product',
        'price' => 20,
        'qty' => 10,
        'manage_stock' => 'yes',
        'in_stock' => true,
        'status' => 'active',
    ]);
    $cart = new Cart;
    $cart->user_id = $user->id;
    $cart->product_id = $product->id;
    $cart->quantity = 1;
    $cart->name = $product->name;
    $cart->save();

    $this->actingAs($user)
        ->get(route('checkout.index'))
        ->assertOk()
        ->assertSeeText('Enter a New Address')
        ->assertSeeText('Add a new address')
        ->assertSee('id="new-address-choice"', false)
        ->assertSee('name="return_to" value="checkout"', false)
        ->assertSee('id="checkout-address-form"', false);

    $this->post(route('address.store'), validAddressData(['return_to' => 'checkout']))
        ->assertRedirect(route('checkout.index'));

    $address = Address::query()->where('user_id', $user->id)->firstOrFail();

    $this->get(route('checkout.index'))
        ->assertOk()
        ->assertSee('id="billing-'.$address->id.'"', false)
        ->assertSee('value="'.$address->id.'" checked', false)
        ->assertSee('id="checkout-address-form" class="mt-4 d-none"', false);
});
