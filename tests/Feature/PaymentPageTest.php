<?php

use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

test('payment page renders configured gateways with bundled logos', function () {
    $user = User::factory()->create();
    $product = Product::query()->create([
        'name' => 'Test product',
        'slug' => 'test-product',
        'price' => 15,
        'status' => 'active',
    ]);

    DB::table('carts')->insert([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    foreach (['paypal', 'stripe', 'razorpay'] as $gateway) {
        Setting::query()->create(['key' => "{$gateway}_status", 'value' => 'active']);
        Setting::query()->create(['key' => "{$gateway}_client_id", 'value' => 'test-client']);
        Setting::query()->create(['key' => "{$gateway}_secret", 'value' => 'test-secret']);
        Setting::query()->create(['key' => "{$gateway}_rate", 'value' => '1']);
    }
    Setting::query()->create(['key' => 'stripe_key', 'value' => 'pk_test_example']);
    Setting::query()->create(['key' => 'stripe_webhook_secret', 'value' => 'whsec_example']);
    Cache::forget('site_settings');

    $response = $this->actingAs($user)->get(route('payment.index'));

    $response
        ->assertOk()
        ->assertSee('Billing Summary')
        ->assertSee('payment_4.png')
        ->assertSee('payment_1.png')
        ->assertSee('payment_12.png')
        ->assertDontSee('paypal.png')
        ->assertDontSee('stripe.png')
        ->assertDontSee('razorpay.png');
});

test('payment page hides unavailable gateways', function () {
    $user = User::factory()->create();
    $product = Product::query()->create([
        'name' => 'Test product',
        'slug' => 'another-test-product',
        'price' => 10,
        'status' => 'active',
    ]);

    DB::table('carts')->insert([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    config()->set([
        'settings.paypal_status' => 'inactive',
        'settings.stripe_status' => 'inactive',
        'settings.razorpay_status' => 'inactive',
    ]);
    Cache::forget('site_settings');

    $this->actingAs($user)
        ->get(route('payment.index'))
        ->assertOk()
        ->assertSee('No payment method is available right now.');
});

test('stripe can be enabled from environment settings before a webhook is configured', function () {
    $user = User::factory()->create();
    $product = Product::query()->create([
        'name' => 'Stripe product',
        'slug' => 'stripe-product',
        'price' => 18,
        'status' => 'active',
    ]);

    DB::table('carts')->insert([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    config()->set([
        'settings.stripe_status' => 'active',
        'settings.stripe_rate' => 1,
        'settings.stripe_key' => 'pk_test_environment',
        'settings.stripe_secret' => 'sk_test_environment',
        'settings.stripe_webhook_secret' => '',
    ]);
    Cache::forget('site_settings');

    $this->actingAs($user)
        ->get(route('payment.index'))
        ->assertOk()
        ->assertSee('payment_1.png')
        ->assertDontSee('No payment method is available right now.');
});

test('successful payment stores order products and clears the cart', function () {
    $user = User::factory()->create();
    $product = Product::query()->create([
        'name' => 'Purchased product',
        'slug' => 'purchased-product',
        'price' => 25,
        'status' => 'active',
    ]);

    DB::table('carts')->insert([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($user);
    session()->put('billing_info', [
        'shipping_method_id' => null,
        'billing_address' => ['first_name' => 'Test', 'address' => '123 Test Street'],
        'shipping_address' => ['first_name' => 'Test', 'address' => '123 Test Street'],
    ]);

    OrderService::storeOrder(
        paymentId: 'payment-test-123',
        paidAmount: 50,
        paymentMethod: 'Stripe',
        currency: 'USD',
        currencyRate: 1,
        paymentStatus: 'paid',
    );

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'transaction_id' => 'payment-test-123',
        'payment_status' => 'paid',
        'total' => 50,
    ]);
    $this->assertDatabaseHas('order_products', [
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_price' => 25,
        'total' => 50,
    ]);
    $this->assertDatabaseMissing('carts', ['user_id' => $user->id]);
});

test('verified stripe webhook fulfills a pending order idempotently', function () {
    $user = User::factory()->create();
    $product = Product::query()->create([
        'name' => 'Stripe product',
        'slug' => 'stripe-product',
        'price' => 15,
        'status' => 'active',
    ]);
    DB::table('carts')->insert([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    Setting::query()->create(['key' => 'stripe_webhook_secret', 'value' => 'whsec_testsecret']);
    Cache::forget('site_settings');

    $this->actingAs($user);
    $orderId = OrderService::createPendingStripeOrder('USD', 1);
    OrderService::attachStripeSession($orderId, 'cs_test_checkout');

    $payload = json_encode([
        'id' => 'evt_test_checkout',
        'object' => 'event',
        'api_version' => '2026-06-24.dahlia',
        'created' => now()->timestamp,
        'data' => [
            'object' => [
                'id' => 'cs_test_checkout',
                'object' => 'checkout.session',
                'amount_total' => 1500,
                'client_reference_id' => (string) $user->id,
                'currency' => 'usd',
                'metadata' => ['order_id' => (string) $orderId],
                'payment_intent' => 'pi_test_payment',
                'payment_status' => 'paid',
            ],
        ],
        'livemode' => false,
        'pending_webhooks' => 1,
        'request' => null,
        'type' => 'checkout.session.completed',
    ], JSON_THROW_ON_ERROR);
    $timestamp = now()->timestamp;
    $signature = hash_hmac('sha256', $timestamp.'.'.$payload, 'whsec_testsecret');

    $sendWebhook = fn () => $this->call(
        'POST',
        route('stripe.webhook'),
        server: ['HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}"],
        content: $payload,
    );

    $sendWebhook()->assertOk();
    $sendWebhook()->assertOk();

    $this->assertDatabaseHas('orders', [
        'id' => $orderId,
        'payment_status' => 'paid',
        'transaction_id' => 'pi_test_payment',
    ]);
    $this->assertDatabaseCount('order_histories', 2);
    $this->assertDatabaseMissing('carts', ['user_id' => $user->id]);
});
