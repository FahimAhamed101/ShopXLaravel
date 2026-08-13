<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OurFeature extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }
}
