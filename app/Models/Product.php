<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Product extends Model
{
    protected $guarded = [];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function store(): BelongsTo
    {
        if (Schema::hasColumn('products', 'store_id')) {
            return $this->belongsTo(Store::class, 'store_id');
        }

        if (Schema::hasColumn('products', 'vendor_id')) {
            return $this->belongsTo(Store::class, 'vendor_id', 'seller_id');
        }

        return $this->belongsTo(Store::class, 'user_id', 'seller_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function images(): HasMany
    {
        $relation = $this->hasMany(ProductImage::class);

        if (Schema::hasColumn('product_images', 'position')) {
            $relation->orderBy('position');
        } elseif (Schema::hasColumn('product_images', 'order')) {
            $relation->orderBy('order');
        }

        return $relation->orderBy('id');
    }

    public function primaryImage(): HasOne
    {
        $relation = $this->hasOne(ProductImage::class);

        if (Schema::hasColumn('product_images', 'is_primary')) {
            $relation->orderByDesc('is_primary');
        }

        if (Schema::hasColumn('product_images', 'position')) {
            $relation->orderBy('position');
        } elseif (Schema::hasColumn('product_images', 'order')) {
            $relation->orderBy('order');
        }

        return $relation->orderBy('id');
    }

    public function productFiles(): HasMany
    {
        return $this->hasMany(ProductFile::class);
    }

    public function files(): HasMany
    {
        return $this->productFiles();
    }

    public function getImagesAttribute(): Collection
    {
        if ($this->relationLoaded('images')) {
            return $this->getRelation('images');
        }

        if ($this->productImagesReady()) {
            return $this->images()->get();
        }

        return collect($this->fallbackImagePaths())
            ->map(fn (string $path) => (object) ['path' => $path]);
    }

    public function getPrimaryImageAttribute(): object|null
    {
        $fallbackImage = $this->fallbackImagePath();

        if ($this->relationLoaded('primaryImage')) {
            return $this->getRelation('primaryImage') ?: (object) ['path' => $fallbackImage];
        }

        if ($this->productImagesReady()) {
            return $this->primaryImage()->first() ?: (object) ['path' => $fallbackImage];
        }

        return (object) ['path' => $fallbackImage];
    }

    public function getPrimaryVariantAttribute(): mixed
    {
        if ($this->relationLoaded('variants')) {
            $variants = $this->getRelation('variants')->filter(fn ($variant) => (bool) ($variant->is_active ?? true));

            return $variants->firstWhere('is_default', 1) ?: $variants->first();
        }

        if ($this->productVariantsReady()) {
            $query = $this->variants();

            if (Schema::hasColumn('product_variants', 'is_active')) {
                $query->where('is_active', 1);
            }

            if (Schema::hasColumn('product_variants', 'is_default')) {
                $query->orderByDesc('is_default');
            }

            return $query->first();
        }

        return null;
    }

    public function getVariantsAttribute(): Collection
    {
        if ($this->relationLoaded('variants')) {
            return $this->getRelation('variants');
        }

        return $this->productVariantsReady() ? $this->variants()->get() : collect();
    }

    public function getAttributeWithValuesAttribute(): Collection
    {
        $attributes = collect();

        if (
            ! Schema::hasTable('product_attribute_values')
            || ! Schema::hasColumn('product_attribute_values', 'product_id')
            || ! Schema::hasColumn('product_attribute_values', 'attribute_id')
            || ! Schema::hasColumn('product_attribute_values', 'attribute_value_id')
        ) {
            return $this->buildAttributeGroupsFromVariants();
        }

        $pivotRows = DB::table('product_attribute_values')
            ->where('product_id', $this->id)
            ->get()
            ->groupBy('attribute_id');

        if ($pivotRows->isEmpty()) {
            return $this->buildAttributeGroupsFromVariants();
        }

        $attributes = Attribute::query()
            ->whereIn('id', $pivotRows->keys())
            ->get()
            ->keyBy('id');

        $attributeValues = AttributeValue::query()
            ->whereIn('id', $pivotRows->flatten(1)->pluck('attribute_value_id')->unique())
            ->get()
            ->keyBy('id');

        $attributes = $pivotRows->map(function ($rows, $attributeId) use ($attributes, $attributeValues) {
            $attribute = $attributes->get($attributeId);

            if (! $attribute) {
                return null;
            }

            $values = $rows
                ->pluck('attribute_value_id')
                ->map(fn ($valueId) => $attributeValues->get($valueId))
                ->filter()
                ->values();

            $attribute->setRelation('values', $values);

            return $attribute;
        })->filter()->values();

        if ($attributes->isEmpty()) {
            return $this->buildAttributeGroupsFromVariants();
        }

        return $this->mergeAttributeGroupsByName($attributes);
    }

    protected function buildAttributeGroupsFromVariants(): Collection
    {
        if (! $this->productVariantsReady()) {
            return collect();
        }

        if (
            ! Schema::hasTable('product_variant_attribute_value')
            || ! Schema::hasColumn('product_variant_attribute_value', 'product_variant_id')
            || ! Schema::hasColumn('product_variant_attribute_value', 'attribute_id')
            || ! Schema::hasColumn('product_variant_attribute_value', 'attribute_value_id')
        ) {
            return collect();
        }

        $variantIds = $this->variants()->pluck('id');

        if ($variantIds->isEmpty()) {
            return collect();
        }

        $pivotRows = DB::table('product_variant_attribute_value')
            ->whereIn('product_variant_id', $variantIds)
            ->get()
            ->groupBy('attribute_id');

        if ($pivotRows->isEmpty()) {
            return collect();
        }

        $attributeIds = $pivotRows->keys();

        $attributes = Attribute::query()
            ->whereIn('id', $attributeIds)
            ->get()
            ->keyBy('id');

        $attributeValues = AttributeValue::query()
            ->whereIn('id', $pivotRows->flatten(1)->pluck('attribute_value_id')->unique())
            ->get()
            ->keyBy('id');

        $attributes = $pivotRows->map(function ($rows, $attributeId) use ($attributes, $attributeValues) {
            $attribute = $attributes->get($attributeId);

            if (! $attribute) {
                return null;
            }

            $values = $rows
                ->pluck('attribute_value_id')
                ->map(fn ($valueId) => $attributeValues->get($valueId))
                ->filter()
                ->unique('id')
                ->values();

            $attribute->setRelation('values', $values);

            return $attribute;
        })->filter()->values();

        return $this->mergeAttributeGroupsByName($attributes);
    }

    protected function mergeAttributeGroupsByName(Collection $attributes): Collection
    {
        return $attributes
            ->groupBy(fn ($attribute) => mb_strtolower(trim((string) $attribute->name)))
            ->map(function (Collection $group) {
                $primary = $group->sortBy('id')->first();
                $values = $group
                    ->flatMap(fn ($attribute) => $attribute->values ?? collect())
                    ->filter()
                    ->unique(fn ($value) => mb_strtolower(trim((string) $value->value)).'|'.($value->color ?? ''))
                    ->values();

                $primary->setRelation('values', $values);

                return $primary;
            })
            ->values();
    }

    public function getTagsAttribute(): Collection
    {
        if ($this->relationLoaded('tags')) {
            return $this->getRelation('tags');
        }

        return Schema::hasTable('product_tag') ? $this->tags()->get() : collect();
    }

    public function getFilesAttribute(): Collection
    {
        if ($this->relationLoaded('files')) {
            return $this->getRelation('files');
        }

        return Schema::hasTable('product_files') ? $this->files()->get() : collect();
    }

    public function rating(): float
    {
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            return (float) $this->attributes['reviews_avg_rating'];
        }

        if (! Schema::hasTable('product_reviews')) {
            return 0;
        }

        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getEffectivePriceAndStock(): array
    {
        $price = $this->resolvePrice();
        $oldPrice = $this->resolveOldPrice();
        $qty = $this->resolveQuantity();
        $inStock = $this->resolveInStock($qty);

        return [
            'price' => $price,
            'old_price' => $oldPrice,
            'qty' => $qty,
            'in_stock' => $inStock,
        ];
    }

    public function getVariantOrProductPriceAndStock($variantId = null): array
    {
        if ($variantId && $this->productVariantsReady()) {
            $variant = $this->variants()->with('attributeValues')->find($variantId);

            if ($variant) {
                $specialPrice = (float) ($variant->special_price ?? 0);
                $price = $specialPrice > 0 ? (float) $variant->price : (float) ($variant->price ?? 0);
                $oldPrice = $specialPrice > 0 ? (float) ($variant->price ?? 0) : 0;
                $qty = $this->stockIsManaged($variant->manage_stock ?? false)
                    ? (int) ($variant->qty ?? 0)
                    : 'Unlimited';
                $inStock = array_key_exists('in_stock', $variant->getAttributes())
                    ? (bool) $variant->in_stock
                    : ($qty === 'Unlimited' ? true : $qty > 0);

                return [
                    'price' => $price,
                    'old_price' => $oldPrice,
                    'qty' => $qty,
                    'in_stock' => $inStock,
                ];
            }
        }

        if (! $variantId && $this->primaryVariant) {
            return $this->getVariantOrProductPriceAndStock($this->primaryVariant->id);
        }

        return $this->getEffectivePriceAndStock();
    }

    protected function productImagesReady(): bool
    {
        return Schema::hasTable('product_images')
            && Schema::hasColumn('product_images', 'product_id')
            && Schema::hasColumn('product_images', 'path');
    }

    protected function productVariantsReady(): bool
    {
        return Schema::hasTable('product_variants')
            && Schema::hasColumn('product_variants', 'product_id');
    }

    protected function fallbackImagePath(): string
    {
        return $this->fallbackImagePaths()[0];
    }

    protected function fallbackImagePaths(): array
    {
        if (filled($this->image ?? null)) {
            return [$this->normalizeAssetPath((string) $this->image)];
        }

        $imageNumber = (((int) ($this->id ?? 1)) - 1) % 9 + 1;

        return [
            "/assets/frontend/dist/imgs/shop/product-{$imageNumber}-1.jpg",
            "/assets/frontend/dist/imgs/shop/product-{$imageNumber}-2.jpg",
        ];
    }

    protected function normalizeAssetPath(string $path): string
    {
        return '/'.ltrim(str_replace('\\', '/', $path), '/');
    }

    protected function resolvePrice(): float
    {
        $specialPrice = (float) ($this->special_price ?? $this->offer_price ?? 0);

        if ($specialPrice > 0) {
            return $specialPrice;
        }

        return (float) ($this->price ?? 0);
    }

    protected function resolveOldPrice(): float
    {
        $specialPrice = (float) ($this->special_price ?? $this->offer_price ?? 0);

        if ($specialPrice > 0) {
            return (float) ($this->price ?? 0);
        }

        return 0;
    }

    protected function resolveQuantity(): int|string
    {
        $manageStock = $this->stockIsManaged($this->manage_stock ?? false);

        if (! $manageStock) {
            return 'Unlimited';
        }

        return (int) ($this->qty ?? $this->stock_qty ?? $this->stock ?? 0);
    }

    protected function resolveInStock(int|string $qty): bool
    {
        if (array_key_exists('in_stock', $this->attributes)) {
            return (bool) $this->attributes['in_stock'];
        }

        if ($qty === 'Unlimited') {
            return true;
        }

        return $qty > 0;
    }

    protected function stockIsManaged(mixed $value): bool
    {
        return in_array($value, [1, true, '1', 'yes', 'true', 'on'], true);
    }
}
