<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'position',
        'image',
        'icon',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'position' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('position')
            ->orderBy('name');
    }

    public function childrenNested(): HasMany
    {
        return $this->children()->with('childrenNested');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public static function getNested(): Collection
    {
        return static::query()
            ->whereNull('parent_id')
            ->orderBy('position')
            ->orderBy('name')
            ->with('childrenNested')
            ->get();
    }

    public function allChildrenIds(): array
    {
        $this->loadMissing('childrenNested');

        return $this->collectDescendantIds($this->childrenNested);
    }

    protected function collectDescendantIds(Collection $categories): array
    {
        $ids = [];

        foreach ($categories as $category) {
            $ids[] = $category->id;

            $category->loadMissing('childrenNested');

            if ($category->childrenNested->isNotEmpty()) {
                $ids = array_merge($ids, $this->collectDescendantIds($category->childrenNested));
            }
        }

        return $ids;
    }
}
