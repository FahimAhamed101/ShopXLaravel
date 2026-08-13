<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'is_percent' => 'boolean',
        'minimum_spend' => 'float',
        'maximum_spend' => 'float',
        'value' => 'float',
        'usage_limit_per_coupon' => 'integer',
        'usage_limit_per_customer' => 'integer',
        'used' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}
