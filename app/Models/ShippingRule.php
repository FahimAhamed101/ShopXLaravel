<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRule extends Model
{
    protected $guarded = [];

    protected $casts = [
        'charge' => 'float',
    ];
}
