<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopularCategory extends Model
{
    protected $guarded = [];

    protected $casts = [
        'categories' => 'array',
    ];
}
