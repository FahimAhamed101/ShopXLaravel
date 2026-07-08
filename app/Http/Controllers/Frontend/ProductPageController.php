<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class ProductPageController extends Controller
{
    function index(Request $request): View
    {
        $brandIds = collect($request->input('brands', []))->filter()->map(fn ($id) => (int) $id)->values()->all();
        $tagIds = collect($request->input('tags', []))->filter()->map(fn ($id) => (int) $id)->values()->all();
        $from = $request->filled('from') ? max(0, (float) $request->from) : null;
        $to = $request->filled('to') ? max(0, (float) $request->to) : null;

        if ($from !== null && $to !== null && $from > $to) {
            [$from, $to] = [$to, $from];
        }

        $productQuery = Product::query();

        if ($this->productImagesReady()) {
            $productQuery->with(['images' => function ($query) {
                $query->limit(2);
            }]);
        }

        if ($this->reviewFeaturesReady()) {
            $productQuery->withAvg('reviews', 'rating');
        }

        $productQuery->when($request->filled('category') && $this->categoryFeaturesReady(), function ($query) use ($request) {
            $category = Category::where('slug', $request->category)->first();

            if ($category) {
                $categoryIds = [$category->id];
                $categoryIds = array_merge($categoryIds, $category->allChildrenIds());

                $query->whereHas('categories', function ($query) use ($categoryIds) {
                    $query->whereIn('categories.id', $categoryIds);
                });
            }
        });

        $productQuery->when($from !== null && $to !== null && tableHasColumns('products', ['price']), function ($query) use ($from, $to) {
            $productPriceExpression = $this->productPriceExpression();

            $query->where(function ($q) use ($from, $to, $productPriceExpression) {
                if ($this->variantFeaturesReady()) {
                    $q->whereHas('variants', function ($variantQuery) use ($from, $to) {
                        $variantQuery->whereRaw($this->variantPriceExpression().' between ? and ?', [$from, $to]);
                    })->orWhere(function ($productOnlyQuery) use ($from, $to, $productPriceExpression) {
                        $productOnlyQuery->whereDoesntHave('variants')
                            ->whereRaw($productPriceExpression.' between ? and ?', [$from, $to]);
                    });

                    return;
                }

                $q->whereRaw($productPriceExpression.' between ? and ?', [$from, $to]);
            });
        });

        $productQuery->when($brandIds && tableHasColumns('products', ['brand_id']), function ($query) use ($brandIds) {
            $query->whereIn('brand_id', $brandIds);
        });

        $productQuery->when($tagIds && $this->tagFeaturesReady(), function ($query) use ($tagIds) {
            $query->whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            });
        });

        $productQuery->when(
            $request->filled('search') && (tableHasColumns('products', ['name']) || tableHasColumns('products', ['description'])),
            function ($query) use ($request) {
            $query->where(function ($searchQuery) use ($request) {
                if (tableHasColumns('products', ['name'])) {
                    $searchQuery->where('name', 'like', '%' . $request->search . '%');
                }

                if (tableHasColumns('products', ['description'])) {
                    $method = tableHasColumns('products', ['name']) ? 'orWhere' : 'where';
                    $searchQuery->{$method}('description', 'like', '%' . $request->search . '%');
                }
            });
        });

        if (tableHasColumns('products', ['status'])) {
            $productQuery->where('status', 'active');
        }

        if (tableHasColumns('products', ['approved_status'])) {
            $productQuery->where('approved_status', 'approved');
        }

        $allMatingProductIds = (clone $productQuery)->pluck('id');

        match ($request->input('sort')) {
            'price_low' => $productQuery->orderByRaw($this->productPriceExpression().' asc'),
            'price_high' => $productQuery->orderByRaw($this->productPriceExpression().' desc'),
            'oldest' => $productQuery->orderBy('id', 'asc'),
            default => $productQuery->orderBy('id', 'desc'),
        };

        $products = $productQuery->paginate(20)->withQueryString();

        $brands = $this->brandFeaturesReady()
            ? Brand::whereHas('products', function ($query) use ($allMatingProductIds) {
                $query->whereIn('products.id', $allMatingProductIds);
            })->withCount('products')->take(15)->get()
            : collect();

        $tags = $this->tagFeaturesReady()
            ? Tag::whereHas('products', function ($query) use ($allMatingProductIds) {
                $query->whereIn('products.id', $allMatingProductIds);
            })->withCount('products')->take(15)->get()
            : collect();

        $categories = tableHasColumns('categories', ['parent_id'])
            ? Category::getNested()
            : collect();

        $priceMin = 0;
        $priceMax = tableHasColumns('products', ['price'])
            ? max(1000, (int) ceil((float) Product::query()->selectRaw('MAX('.$this->productPriceExpression().') as max_price')->value('max_price')))
            : 1000;

        return view('frontend.pages.product', compact('products', 'categories', 'brands', 'tags', 'from', 'to', 'priceMin', 'priceMax'));
    }

    function show(string $slug): View
    {
        abort_unless(tableHasColumns('products', ['slug']), 404);

        $productQuery = Product::query();

        if ($this->productImagesReady()) {
            $productQuery->with(['images:id,path,product_id']);
        }

        if ($this->reviewFeaturesReady()) {
            $productQuery->with(['reviews'])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating');
        }

        if ($this->variantFeaturesReady()) {
            $productQuery->with(['variants.attributeValues']);
        }

        $product = $productQuery->where('slug', $slug)->firstOrFail();

        $relatedProductsQuery = Product::query()
            ->where('id', '!=', $product->id)
            ->distinct()
            ->take(6);

        if ($this->productImagesReady()) {
            $relatedProductsQuery->with(['images' => function ($query) {
                $query->limit(2);
            }]);
        }

        if ($this->categoryFeaturesReady()) {
            $relatedProductsQuery->whereHas('categories', function ($query) use ($product) {
                $query->whereIn('categories.id', $product->categories->pluck('id')->toArray());
            });
        }

        if (tableHasColumns('products', ['status'])) {
            $relatedProductsQuery->where('status', 'active');
        }

        if (tableHasColumns('products', ['approved_status'])) {
            $relatedProductsQuery->where('approved_status', 'approved');
        }

        if ($this->reviewFeaturesReady()) {
            $relatedProductsQuery->withAvg('reviews', 'rating');
        }

        $relatedProducts = $relatedProductsQuery->get();
        $reviews = $this->emptyReviewPaginator();
        $reviewGroup = collect();
        $totalReviews = 0;
        $avgRating = 0;

        if ($this->reviewFeaturesReady()) {
            $reviews = ProductReview::where('product_id', $product->id)->paginate(10);
            $reviewGroup = ProductReview::select('rating', \DB::raw('count(*) as count'))
                ->where('product_id', $product->id)
                ->groupBy('rating')
                ->pluck('count', 'rating');
            $totalReviews = $reviewGroup->sum();
            $avgRating = ProductReview::where('product_id', $product->id)->avg('rating') ?? 0;
        }

        return view('frontend.pages.product-show', compact('product', 'relatedProducts', 'reviews', 'reviewGroup', 'totalReviews', 'avgRating'));
    }

    protected function reviewFeaturesReady(): bool
    {
        return class_exists(ProductReview::class)
            && tableHasColumns('product_reviews', ['product_id', 'user_id', 'rating', 'review']);
    }

    protected function productImagesReady(): bool
    {
        return Schema::hasTable('product_images')
            && Schema::hasColumn('product_images', 'product_id')
            && (
                Schema::hasColumn('product_images', 'path')
                || Schema::hasColumn('product_images', 'image')
            );
    }

    protected function categoryFeaturesReady(): bool
    {
        return method_exists(Product::class, 'categories')
            && tableHasColumns('categories', ['id', 'slug'])
            && tableHasColumns('category_product', ['category_id', 'product_id']);
    }

    protected function tagFeaturesReady(): bool
    {
        return class_exists(Tag::class)
            && tableHasColumns('tags', ['id'])
            && Schema::hasTable('product_tag');
    }

    protected function brandFeaturesReady(): bool
    {
        return class_exists(Brand::class)
            && tableHasColumns('brands', ['id', 'name']);
    }

    protected function variantFeaturesReady(): bool
    {
        return class_exists(\App\Models\ProductVariant::class)
            && tableHasColumns('product_variants', ['product_id', 'price']);
    }

    protected function productPriceExpression(): string
    {
        $columns = [];

        if (Schema::hasColumn('products', 'special_price')) {
            $columns[] = 'NULLIF(special_price, 0)';
        }

        if (Schema::hasColumn('products', 'offer_price')) {
            $columns[] = 'NULLIF(offer_price, 0)';
        }

        $columns[] = 'price';

        return 'COALESCE('.implode(', ', $columns).')';
    }

    protected function variantPriceExpression(): string
    {
        $columns = [];

        if (Schema::hasColumn('product_variants', 'special_price')) {
            $columns[] = 'NULLIF(special_price, 0)';
        }

        $columns[] = 'price';

        return 'COALESCE('.implode(', ', $columns).')';
    }

    protected function emptyReviewPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, 10, 1, [
            'path' => request()->url(),
            'pageName' => 'page',
        ]);
    }
}
