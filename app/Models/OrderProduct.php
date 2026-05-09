<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class OrderProduct extends Model
{
    protected $guarded = [];

    public function getTable()
    {
        foreach (['order_products', 'order_items'] as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return parent::getTable();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
