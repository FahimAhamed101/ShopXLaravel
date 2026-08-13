<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomPage;
use App\Models\FlashSale;
use App\Models\HeroBanner;
use App\Models\Order;
use App\Models\PopularCategory;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductSection;
use App\Models\Slider;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = getNestedCategories();
        $featuredCategories = $this->featuredCategories();
        $sliders = $this->safeCollection(Slider::class, 'sliders', ['is_active', 'image', 'title', 'sub_title', 'btn_url'], fn ($query) => $query->where('is_active', true)->get());
        $heroBanner = $this->safeFirst(HeroBanner::class, 'hero_banners');
        $popularCategoriesIds = $this->popularCategoryIds();
        $popularCategories = Category::whereIn('id', $popularCategoriesIds)->get();
        $popularProducts = $this->productsByCategory($popularCategoriesIds);
        $flashSale = $this->safeFirst(FlashSale::class, 'flash_sales', ['products', 'sale_start', 'sale_end', 'is_active']);
        $flashSaleProducts = $this->catalogReady()
            ? $this->applyReviewAggregate(Product::query())->whereIn('id', $flashSale?->products ?? [])->get()
            : collect();
        $productSections = $this->safeFirst(ProductSection::class, 'product_sections', ['category_one', 'category_two', 'category_three']);

        $productSectionsIds = [
            $productSections?->category_one,
            $productSections?->category_two,
            $productSections?->category_three,
        ];

        $hotProducts = $this->productShowcase('is_hot');
        $newProducts = $this->productShowcase('is_new');
        $featuredProducts = $this->productShowcase('is_featured');
        $topRatedProducts = $this->topRatedProducts();

        $productSectionsProducts = $this->productsByCategory($productSectionsIds, false);

        return view('frontend.home.index', compact(
            'categories',
            'featuredCategories',
            'sliders',
            'heroBanner',
            'popularCategories',
            'popularProducts',
            'flashSale',
            'flashSaleProducts',
            'productSectionsProducts',
            'hotProducts',
            'newProducts',
            'featuredProducts',
            'topRatedProducts'
        ));
    }

    public function productsByCategory(array $categoryIds, $featured = true, $limit = 12)
    {
        if (! $this->catalogReady()) {
            return [];
        }

        $results = [];

        foreach ($categoryIds as $categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                $ids = [$category->id];
                $ids = array_merge($ids, $category->allChildrenIds());
                $query = $this->applyReviewAggregate(Product::query())
                    ->whereHas('categories', function ($query) use ($ids) {
                        $query->whereIn('categories.id', $ids);
                    });

                if ($featured && Schema::hasColumn('products', 'is_featured')) {
                    $query->whereIsFeatured(true)->take(12);
                } else {
                    $query->latest()->take($limit);
                }

                $products = $query->get();

                $results[$categoryId] = $products;
            }
        }

        return $results;
    }

    public function storeReview(Request $request, Product $product): JsonResponse
    {
        if (! $this->reviewFeaturesReady()) {
            throw ValidationException::withMessages([
                'review' => 'Product reviews are not available with the current database schema.',
            ]);
        }

        $request->validate([
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'review' => ['required', 'string', 'max: 500'],
        ]);

        $productPurchasedByUser = Order::where('user_id', user()->id)->whereHas('orderProducts', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->exists();

        if (! $productPurchasedByUser) {
            throw ValidationException::withMessages([
                'review' => 'You have not purchased this product',
            ]);
        }
        if (ProductReview::where('product_id', $product->id)->where('user_id', user()->id)->exists()) {
            throw ValidationException::withMessages([
                'review' => 'You have already reviewed this product',
            ]);
        }

        $review = new ProductReview;
        $review->product_id = $product->id;
        $review->user_id = user()->id;
        $review->rating = $request->rating;
        $review->review = $request->review;
        $review->save();

        AlertService::created('Product Review Added Successfully');

        return response()->json(['status' => 'success', 'message' => 'Product Review Added Successfully']);
    }

    public function customPage(string $slug): View
    {
        abort_unless(class_exists(CustomPage::class) && tableHasColumns('custom_pages', ['slug', 'is_active']), 404);

        $page = CustomPage::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('frontend.pages.custom-page', compact('page'));
    }

    public function flashSales(): View
    {
        $flashSale = $this->safeFirst(FlashSale::class, 'flash_sales', ['products', 'sale_start', 'sale_end', 'is_active']);
        $isRunning = $flashSale?->is_active && now()->between(
            $flashSale->sale_start,
            $flashSale->sale_end->copy()->endOfDay(),
        );
        $flashSaleProducts = $this->catalogReady() && $isRunning
            ? $this->applyReviewAggregate(Product::query())->whereIn('id', $flashSale?->products ?? [])->paginate(20)
            : Product::query()->whereRaw('1 = 0')->paginate(20);

        return view('frontend.pages.flash-sale', compact('flashSale', 'flashSaleProducts'));
    }

    protected function safeCollection(string $modelClass, string $table, array $requiredColumns = [], ?callable $callback = null): Collection
    {
        if (! class_exists($modelClass) || ! tableHasColumns($table, $requiredColumns)) {
            return collect();
        }

        $query = $modelClass::query();

        return $callback ? $callback($query) : $query->get();
    }

    protected function safeFirst(string $modelClass, string $table, array $requiredColumns = []): mixed
    {
        if (! class_exists($modelClass) || ! tableHasColumns($table, $requiredColumns)) {
            return null;
        }

        return $modelClass::query()->first();
    }

    protected function featuredCategories(): Collection
    {
        if (! tableHasColumns('categories', ['is_featured']) || ! Schema::hasTable('category_product')) {
            return collect();
        }

        return Category::withCount('products')
            ->where('is_featured', true)
            ->take(15)
            ->get();
    }

    protected function popularCategoryIds(): array
    {
        if (! tableHasColumns('popular_categories', ['categories'])) {
            return [];
        }

        return PopularCategory::first()?->categories ?? [];
    }

    protected function catalogReady(): bool
    {
        return class_exists(Product::class)
            && tableHasColumns('products', ['id'])
            && Schema::hasTable('category_product')
            && method_exists(Product::class, 'categories');
    }

    protected function productShowcase(string $column): Collection
    {
        if (! $this->catalogReady() || ! Schema::hasColumn('products', $column)) {
            return collect();
        }

        $query = Product::query()->where($column, true)->latest()->take(4);

        if ($this->productImagesReady()) {
            $query->with('primaryImage');
        }

        return $this->applyReviewAggregate($query)->get();
    }

    protected function topRatedProducts(): Collection
    {
        if (! $this->catalogReady()) {
            return collect();
        }

        $query = Product::query()->latest()->take(4);

        if ($this->productImagesReady()) {
            $query->with('primaryImage');
        }

        if ($this->reviewFeaturesReady()) {
            $query->whereHas('reviews')
                ->withAvg('reviews', 'rating')
                ->orderBy('reviews_avg_rating', 'desc');
        }

        return $query->get();
    }

    protected function applyReviewAggregate($query)
    {
        if ($this->reviewFeaturesReady()) {
            $query->withAvg('reviews', 'rating');
        }

        return $query;
    }

    protected function reviewFeaturesReady(): bool
    {
        return class_exists(ProductReview::class)
            && tableHasColumns('product_reviews', ['product_id', 'user_id', 'rating', 'review']);
    }

    protected function productImagesReady(): bool
    {
        return tableHasColumns('product_images', ['product_id', 'path']);
    }
}
