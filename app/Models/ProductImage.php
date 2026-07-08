<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $guarded = [];

    public function getPathAttribute($value): ?string
    {
        return $value ?: ($this->attributes['image'] ?? null);
    }

    public function getUrlAttribute(): string
    {
        return imageUrl($this->path);
    }
}
