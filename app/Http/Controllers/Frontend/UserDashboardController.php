<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class UserDashboardController extends Controller
{
    function index() : View
    {
        return view('frontend.dashboard.main.index');
    }

    public function reviews(): View
    {
        $reviews = new LengthAwarePaginator([], 0, 15);

        if (class_exists(ProductReview::class) && Schema::hasTable('product_reviews')) {
            $reviews = ProductReview::query()
                ->with('product')
                ->where('user_id', auth('web')->id())
                ->latest()
                ->paginate(15);
        }

        return view('frontend.dashboard.review.index', compact('reviews'));
    }
}
