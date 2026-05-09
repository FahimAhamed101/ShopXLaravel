<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use stdClass;

class Order extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getCurrencyAttribute($value): string
    {
        return $value ?: config('settings.site_currency', 'USD');
    }

    public function getPaymentStatusAttribute($value): string
    {
        return $value ?: ($this->attributes['status'] ?? 'pending');
    }

    public function getOrderStatusAttribute($value): string
    {
        return $value ?: ($this->attributes['status'] ?? 'pending');
    }

    public function getTransactionIdAttribute($value): string
    {
        return $value ?: 'N/A';
    }

    public function getPaymentMethodAttribute($value): string
    {
        return $value ?: 'N/A';
    }

    public function getTotalAttribute($value): float|int
    {
        return $value ?? $this->attributes['sub_total'] ?? 0;
    }

    public function getBillingInfoAttribute($value): array
    {
        $payload = $this->normalizeAddressPayload($value);

        return array_filter($payload) ? $payload : $this->legacyAddressPayload();
    }

    public function getShippingInfoAttribute($value): ?array
    {
        $payload = $this->normalizeAddressPayload($value);

        if (array_filter($payload)) {
            return $payload;
        }

        $legacy = $this->legacyAddressPayload();

        return array_filter($legacy) ? $legacy : null;
    }

    public function getOrderProductsAttribute(): Collection
    {
        if (Schema::hasTable('order_products')) {
            return OrderProduct::query()
                ->where('order_id', $this->id)
                ->with(['product', 'variant'])
                ->get();
        }

        $product = $this->product;

        if (! $product) {
            return collect();
        }

        $item = new stdClass();
        $item->product = $product;
        $item->variant = null;
        $item->quantity = (int) ($this->attributes['qty'] ?? $this->attributes['product_qty'] ?? 1);
        $item->unit_price = (float) ($this->attributes['sub_total'] ?? $this->attributes['total'] ?? $product->price ?? 0) / max($item->quantity, 1);

        return collect([$item]);
    }

    public function getStoreAttribute(): ?Store
    {
        if (Schema::hasColumn('orders', 'store_id') && filled($this->attributes['store_id'] ?? null)) {
            return Store::find($this->attributes['store_id']);
        }

        if ($this->product?->store) {
            return $this->product->store;
        }

        return $this->user?->store ?: Store::query()->make([
            'name' => 'ShopX',
        ]);
    }

    public function getOrderHistoryAttribute(): Collection
    {
        if (Schema::hasTable('order_histories')) {
            return collect(DB::table('order_histories')->where('order_id', $this->id)->orderBy('id')->get());
        }

        $history = new stdClass();
        $history->status = ucfirst($this->order_status);

        return collect([$history]);
    }

    public function getShippingChargeAttribute($value): float|int
    {
        return $value ?? $this->attributes['shipping_cost'] ?? 0;
    }

    protected function normalizeAddressPayload($value): array
    {
        $defaults = [
            'first_name' => '',
            'last_name' => '',
            'address' => '',
            'city' => '',
            'state' => '',
            'country' => '',
            'email' => '',
            'phone' => '',
        ];

        if (is_array($value)) {
            return array_merge($defaults, $value);
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);

            if (is_array($decoded)) {
                return array_merge($defaults, $decoded);
            }
        }

        return $defaults;
    }

    protected function legacyAddressPayload(): array
    {
        $address = (string) ($this->attributes['billing_address'] ?? $this->attributes['order_address'] ?? '');
        $name = (string) ($this->user?->name ?? '');
        $parts = preg_split('/\s+/', trim($name));

        return [
            'first_name' => $parts[0] ?? '',
            'last_name' => count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '',
            'address' => $address,
            'city' => '',
            'state' => '',
            'country' => '',
            'email' => $this->user?->email ?? '',
            'phone' => $this->user?->phone ?? '',
        ];
    }
}
