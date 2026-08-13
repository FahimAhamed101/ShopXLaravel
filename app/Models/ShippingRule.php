<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShippingRule extends Model
{
    protected $guarded = [];

    protected $casts = [
        'charge' => 'float',
        'minimum_amount' => 'float',
        'is_active' => 'boolean',
    ];

    public function scopeAvailableFor(Builder $query, float $orderTotal): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query) use ($orderTotal): void {
                $query->where('type', 'flat_amount')
                    ->orWhere(function (Builder $query) use ($orderTotal): void {
                        $query->where('type', 'minimum_order_amount')
                            ->where('minimum_amount', '<=', $orderTotal);
                    });
            });
    }
}
