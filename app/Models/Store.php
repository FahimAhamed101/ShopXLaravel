<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Schema;

class Store extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function products(): HasMany
    {
        if (Schema::hasColumn('products', 'store_id')) {
            return $this->hasMany(Product::class, 'store_id');
        }

        if (Schema::hasColumn('products', 'vendor_id')) {
            return $this->hasMany(Product::class, 'vendor_id', 'seller_id');
        }

        return $this->hasMany(Product::class, 'user_id', 'seller_id');
    }

    public function reviews(): HasManyThrough
    {
        if (Schema::hasColumn('products', 'store_id')) {
            return $this->hasManyThrough(ProductReview::class, Product::class, 'store_id', 'product_id');
        }

        if (Schema::hasColumn('products', 'vendor_id')) {
            return $this->hasManyThrough(ProductReview::class, Product::class, 'vendor_id', 'product_id', 'seller_id', 'id');
        }

        return $this->hasManyThrough(ProductReview::class, Product::class, 'user_id', 'product_id', 'seller_id', 'id');
    }

    public function getLogoAttribute($value): string
    {
        return $value ?: '/defaults/avatar.png';
    }

    public function getBannerAttribute($value): string
    {
        return $value ?: '/assets/frontend/dist/imgs/vendor/vendor-header-bg.png';
    }
}
