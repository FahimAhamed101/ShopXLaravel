<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $guarded = [];

    public function getPathAttribute($value): string
    {
        return $value ?: (string) ($this->attributes['image'] ?? '/assets/frontend/dist/imgs/shop/product-1-1.jpg');
    }
}
