<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }
}
