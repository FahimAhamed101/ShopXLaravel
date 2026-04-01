<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CustomPage;
use App\Models\FlashSale;
use App\Models\HeroBanner;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PopularCategory;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductSection;
use App\Models\Slider;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{
    function index(): View
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
            ? Product::withAvg('reviews', 'rating')->whereIn('id', $flashSale?->products ?? [])->get()
            : collect();
        $productSections = $this->safeFirst(ProductSection::class, 'product_sections', ['category_one', 'category_two', 'category_three']);

        $productSectionsIds = [
            $productSections?->category_one,
            $productSections?->category_two,
            $productSections?->category_three
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

    function productsByCategory(array $categoryIds, $featured = true, $limit = 12)
    {
        if (!$this->catalogReady()) {
            return [];
        }

        $results = [];

        foreach ($categoryIds as $categoryId) {
            $category = Category::find($categoryId);
            if ($category) {
                $ids = [$category->id];
                $ids = array_merge($ids, $category->allChildrenIds());
                if ($featured)
                    $products = Product::withAvg('reviews', 'rating')->whereHas('categories', function ($query) use ($ids) {
                        $query->whereIn('categories.id', $ids);
                    })->whereIsFeatured(true)->take(12)->get();
                else {
                    $products = Product::withAvg('reviews', 'rating')->whereHas('categories', function ($query) use ($ids) {
                        $query->whereIn('categories.id', $ids);
                    })->latest()->take($limit)->get();
                }


                $results[$categoryId] = $products;
            }
        }


        return $results;
    }

    function storeReview(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'review' => ['required', 'string', 'max: 500'],
        ]);

        $productPurchasedByUser = Order::where('user_id', user()->id)->whereHas('orderProducts', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->exists();

        if (!$productPurchasedByUser) {
            throw ValidationException::withMessages([
                'review' => 'You have not purchased this product'
            ]);
        }
        if (ProductReview::where('product_id', $product->id)->where('user_id', user()->id)->exists()) {
            throw ValidationException::withMessages([
                'review' => 'You have already reviewed this product'
            ]);
        }

        $review = new ProductReview();
        $review->product_id = $product->id;
        $review->user_id = user()->id;
        $review->rating = $request->rating;
        $review->review = $request->review;
        $review->save();

        AlertService::created('Product Review Added Successfully');

        return response()->json(['status' => 'success', 'message' => 'Product Review Added Successfully']);
    }

    function customPage(string $slug): View
    {
        $page = CustomPage::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('frontend.pages.custom-page', compact('page'));
    }

    function flashSales(): View
    {
        $flashSale = $this->safeFirst(FlashSale::class, 'flash_sales', ['products', 'sale_start', 'sale_end', 'is_active']);
        $flashSaleProducts = $this->catalogReady()
            ? Product::withAvg('reviews', 'rating')->whereIn('id', $flashSale?->products ?? [])->paginate(20)
            : collect();

        return view('frontend.pages.flash-sale', compact('flashSale', 'flashSaleProducts'));
    }

    protected function safeCollection(string $modelClass, string $table, array $requiredColumns = [], ?callable $callback = null): Collection
    {
        if (!class_exists($modelClass) || !tableHasColumns($table, $requiredColumns)) {
            return collect();
        }

        $query = $modelClass::query();

        return $callback ? $callback($query) : $query->get();
    }

    protected function safeFirst(string $modelClass, string $table, array $requiredColumns = []): mixed
    {
        if (!class_exists($modelClass) || !tableHasColumns($table, $requiredColumns)) {
            return null;
        }

        return $modelClass::query()->first();
    }

    protected function featuredCategories(): Collection
    {
        if (!tableHasColumns('categories', ['is_featured']) || !Schema::hasTable('category_product')) {
            return collect();
        }

        return Category::withCount('products')
            ->where('is_featured', true)
            ->take(15)
            ->get();
    }

    protected function popularCategoryIds(): array
    {
        if (!tableHasColumns('popular_categories', ['categories'])) {
            return [];
        }

        return PopularCategory::first()?->categories ?? [];
    }

    protected function catalogReady(): bool
    {
        return class_exists(Product::class)
            && tableHasColumns('products', ['id'])
            && Schema::hasTable('category_product')
            && method_exists(Product::class, 'categories')
            && method_exists(Product::class, 'reviews')
            && method_exists(Product::class, 'primaryImage');
    }

    protected function productShowcase(string $column): Collection
    {
        if (!$this->catalogReady() || !Schema::hasColumn('products', $column)) {
            return collect();
        }

        return Product::with('primaryImage')
            ->withAvg('reviews', 'rating')
            ->where($column, true)
            ->latest()
            ->take(4)
            ->get();
    }

    protected function topRatedProducts(): Collection
    {
        if (!$this->catalogReady()) {
            return collect();
        }

        return Product::with('primaryImage')
            ->whereHas('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating', 'desc')
            ->take(4)
            ->get();
    }
}
