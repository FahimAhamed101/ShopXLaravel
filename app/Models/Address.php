<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFirstNameAttribute($value): string
    {
        if (filled($value)) {
            return $value;
        }

        $parts = preg_split('/\s+/', trim((string) ($this->attributes['name'] ?? '')));

        return $parts[0] ?? '';
    }

    public function getLastNameAttribute($value): string
    {
        if (filled($value)) {
            return $value;
        }

        $parts = preg_split('/\s+/', trim((string) ($this->attributes['name'] ?? '')));

        return count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
    }

    public function getZipAttribute($value): string
    {
        return $value
            ?: (string) ($this->attributes['zip_code'] ?? $this->attributes['postal_code'] ?? '');
    }
}
