<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

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
        if (! Schema::hasTable('orders')) {
            return;
        }

        $payload = [
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'transaction_id' => $paymentId,
            'currency' => strtoupper($currency),
            'currency_rate' => $currencyRate,
            'total' => $paidAmount,
            'status' => 'pending',
            'order_status' => 'pending',
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('orders', 'user_id') && auth('web')->check()) {
            $payload['user_id'] = auth('web')->id();
        }

        if (Schema::hasColumn('orders', 'created_at')) {
            $payload['created_at'] = now();
        }

        $payload = array_filter(
            $payload,
            fn ($value, $key) => Schema::hasColumn('orders', $key),
            ARRAY_FILTER_USE_BOTH
        );

        if ($payload !== []) {
            DB::table('orders')->insert($payload);
        }

        if (Schema::hasTable('carts') && Schema::hasColumn('carts', 'user_id') && auth('web')->check()) {
            DB::table('carts')->where('user_id', auth('web')->id())->delete();
        }

        Session::forget(['billing_info', 'coupon']);
    }
}
