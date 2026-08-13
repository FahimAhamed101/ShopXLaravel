<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    protected $guarded = [];

    protected $casts = [
        'products' => 'array',
        'sale_start' => 'date:Y-m-d',
        'sale_end' => 'date:Y-m-d',
        'is_active' => 'boolean',
    ];
}
