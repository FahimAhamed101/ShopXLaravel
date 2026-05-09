<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFile extends Model
{
    protected $guarded = [];

    public function getFilenameAttribute($value): string
    {
        return $value ?: (string) ($this->attributes['name'] ?? $this->attributes['title'] ?? 'File');
    }

    public function getSizeAttribute($value): int
    {
        return (int) ($value ?? $this->attributes['file_size'] ?? 0);
    }
}
