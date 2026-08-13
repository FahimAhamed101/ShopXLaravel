<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use RuntimeException;
use Stripe\Checkout\Session as StripeSession;

class OrderService
{
    public static function storeOrder(
        string $paymentId,
        float|int $paidAmount,
        string $paymentMethod,
        string $currency,
        float|int $currencyRate,
        string $paymentStatus
    ): void {
        if (! Schema::hasTable('orders') || ! auth('web')->check()) {
            return;
        }

        DB::transaction(function () use (
            $paymentId,
            $paidAmount,
            $paymentMethod,
            $currency,
            $currencyRate,
            $paymentStatus
        ): void {
            if (Schema::hasColumn('orders', 'transaction_id') &&
                DB::table('orders')->where('transaction_id', $paymentId)->exists()) {
                return;
            }

            $cartItems = Cart::query()
                ->with('product')
                ->where('user_id', auth('web')->id())
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                return;
            }

            $billing = Session::get('billing_info', []);
            $coupon = Session::get('coupon');
            $subTotal = cartTotal() * $currencyRate;
            $shipping = getShippingCharge() * $currencyRate;
            $quantity = (int) $cartItems->sum('quantity');
            $now = now();
            $payload = [
                'user_id' => auth('web')->id(),
                'coupon_id' => $coupon['id'] ?? null,
                'invoice_id' => 'INV-'.now()->format('Ymd').'-'.strtoupper(Str::random(8)),
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'transaction_id' => $paymentId,
                'currency' => strtoupper($currency),
                'currency_rate' => $currencyRate,
                'sub_total' => round($subTotal, 2),
                'shipping_charge' => round($shipping, 2),
                'shipping_cost' => round($shipping, 2),
                'total' => $paidAmount,
                'qty' => $quantity,
                'product_qty' => $quantity,
                'billing_info' => json_encode($billing['billing_address'] ?? null),
                'shipping_info' => json_encode($billing['shipping_address'] ?? null),
                'status' => 'pending',
                'order_status' => 'pending',
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $payload = self::onlyExistingColumns('orders', $payload);
            $orderId = DB::table('orders')->insertGetId($payload);

            if (Schema::hasTable('order_products')) {
                foreach ($cartItems as $cartItem) {
                    if (! $cartItem->product ||
                        ! method_exists($cartItem->product, 'getVariantOrProductPriceAndStock')) {
                        continue;
                    }

                    $price = $cartItem->product
                        ->getVariantOrProductPriceAndStock($cartItem->variant_id)['price'] * $currencyRate;
                    DB::table('order_products')->insert(self::onlyExistingColumns('order_products', [
                        'order_id' => $orderId,
                        'product_id' => $cartItem->product_id,
                        'variant_id' => $cartItem->variant_id,
                        'product_name' => $cartItem->product->name,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => round($price, 2),
                        'total' => round($price * $cartItem->quantity, 2),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]));
                }
            }

            if (Schema::hasTable('order_histories')) {
                DB::table('order_histories')->insert(self::onlyExistingColumns('order_histories', [
                    'order_id' => $orderId,
                    'status' => 'pending',
                    'note' => "Payment completed via {$paymentMethod}.",
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }

            if (is_array($coupon) && isset($coupon['id']) && Schema::hasTable('coupons')) {
                DB::table('coupons')->where('id', $coupon['id'])->increment('used');
            }

            DB::table('carts')->where('user_id', auth('web')->id())->delete();
            Session::forget([
                'billing_info',
                'coupon',
                'paypal_order_id',
                'razorpay_order',
            ]);
        });
    }

    public static function createPendingStripeOrder(string $currency, float $currencyRate): int
    {
        return DB::transaction(function () use ($currency, $currencyRate): int {
            $cartItems = Cart::query()
                ->with('product')
                ->where('user_id', auth('web')->id())
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                throw new RuntimeException('The cart is empty.');
            }

            $billing = Session::get('billing_info', []);
            $coupon = Session::get('coupon');
            $shipping = getShippingCharge() * $currencyRate;
            $quantity = (int) $cartItems->sum('quantity');
            $now = now();
            $payload = self::onlyExistingColumns('orders', [
                'user_id' => auth('web')->id(),
                'coupon_id' => $coupon['id'] ?? null,
                'invoice_id' => 'INV-'.$now->format('Ymd').'-'.strtoupper(Str::random(8)),
                'payment_method' => 'Stripe',
                'payment_status' => 'pending',
                'currency' => strtoupper($currency),
                'currency_rate' => $currencyRate,
                'sub_total' => round(cartTotal() * $currencyRate, 2),
                'shipping_charge' => round($shipping, 2),
                'shipping_cost' => round($shipping, 2),
                'total' => round(getPayableAmount() * $currencyRate, 2),
                'qty' => $quantity,
                'product_qty' => $quantity,
                'billing_info' => json_encode($billing['billing_address'] ?? null),
                'shipping_info' => json_encode($billing['shipping_address'] ?? null),
                'checkout_cart_ids' => json_encode($cartItems->pluck('id')->values()->all()),
                'status' => 'pending',
                'order_status' => 'pending',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $orderId = DB::table('orders')->insertGetId($payload);

            self::insertOrderProducts($orderId, $cartItems, $currencyRate, $now);

            if (Schema::hasTable('order_histories')) {
                DB::table('order_histories')->insert(self::onlyExistingColumns('order_histories', [
                    'order_id' => $orderId,
                    'status' => 'pending',
                    'note' => 'Awaiting payment via Stripe.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }

            return $orderId;
        });
    }

    public static function attachStripeSession(int $orderId, string $sessionId): void
    {
        DB::table('orders')
            ->where('id', $orderId)
            ->where('payment_method', 'Stripe')
            ->where('payment_status', 'pending')
            ->update(['provider_session_id' => $sessionId, 'updated_at' => now()]);
    }

    public static function completeStripeOrder(StripeSession $session): bool
    {
        $orderId = (int) data_get($session, 'metadata.order_id');

        if ($orderId < 1) {
            return false;
        }

        return DB::transaction(function () use ($orderId, $session): bool {
            $order = DB::table('orders')->where('id', $orderId)->lockForUpdate()->first();

            if (! $order || $order->payment_method !== 'Stripe' ||
                ! hash_equals((string) $order->provider_session_id, (string) $session->id) ||
                ! hash_equals((string) $order->user_id, (string) $session->client_reference_id) ||
                strtoupper((string) $order->currency) !== strtoupper((string) $session->currency) ||
                (int) round((float) $order->total * 100) !== (int) $session->amount_total) {
                return false;
            }

            if ($order->payment_status === 'paid') {
                return true;
            }

            if ($session->payment_status !== 'paid') {
                return false;
            }

            DB::table('orders')->where('id', $orderId)->update([
                'payment_status' => 'paid',
                'transaction_id' => (string) ($session->payment_intent ?: $session->id),
                'updated_at' => now(),
            ]);

            if ($order->coupon_id && Schema::hasTable('coupons')) {
                DB::table('coupons')->where('id', $order->coupon_id)->increment('used');
            }

            $cartIds = json_decode((string) $order->checkout_cart_ids, true);
            if (is_array($cartIds) && $cartIds !== []) {
                DB::table('carts')
                    ->where('user_id', $order->user_id)
                    ->whereIn('id', $cartIds)
                    ->delete();
            }

            if (Schema::hasTable('order_histories')) {
                DB::table('order_histories')->insert(self::onlyExistingColumns('order_histories', [
                    'order_id' => $orderId,
                    'status' => 'paid',
                    'note' => 'Stripe payment confirmed.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }

            return true;
        });
    }

    public static function discardPendingStripeOrder(int $orderId, ?int $userId = null): void
    {
        DB::transaction(function () use ($orderId, $userId): void {
            $query = DB::table('orders')
                ->where('id', $orderId)
                ->where('payment_method', 'Stripe')
                ->where('payment_status', 'pending');

            if ($userId !== null) {
                $query->where('user_id', $userId);
            }

            $pending = $query->exists();

            if (! $pending) {
                return;
            }

            if (Schema::hasTable('order_products')) {
                DB::table('order_products')->where('order_id', $orderId)->delete();
            }

            if (Schema::hasTable('order_histories')) {
                DB::table('order_histories')->where('order_id', $orderId)->delete();
            }

            DB::table('orders')->where('id', $orderId)->delete();
        });
    }

    private static function insertOrderProducts(int $orderId, $cartItems, float $currencyRate, $now): void
    {
        if (! Schema::hasTable('order_products')) {
            return;
        }

        foreach ($cartItems as $cartItem) {
            if (! $cartItem->product || ! method_exists($cartItem->product, 'getVariantOrProductPriceAndStock')) {
                continue;
            }

            $price = $cartItem->product
                ->getVariantOrProductPriceAndStock($cartItem->variant_id)['price'] * $currencyRate;
            DB::table('order_products')->insert(self::onlyExistingColumns('order_products', [
                'order_id' => $orderId,
                'product_id' => $cartItem->product_id,
                'variant_id' => $cartItem->variant_id,
                'product_name' => $cartItem->product->name,
                'quantity' => $cartItem->quantity,
                'unit_price' => round($price, 2),
                'total' => round($price * $cartItem->quantity, 2),
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    private static function onlyExistingColumns(string $table, array $payload): array
    {
        return array_intersect_key($payload, array_flip(Schema::getColumnListing($table)));
    }
}
