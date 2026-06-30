<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReviewController extends Controller implements HasMiddleware
{
    public static function Middleware(): array
    {
        return [
            new Middleware('permission:Brand Management'),
        ];
    }

    public function index(): View
    {
        $reviews = ProductReview::with([
            'product.primaryImage',
            'user',
        ])->orderByDesc('id')->paginate(20);

        return view('admin.review.index', compact('reviews'));
    }

    public function destroy(ProductReview $review)
    {
        $review->delete();

        return response()->json(['status' => 'success', 'message' => 'Review deleted successfully']);
    }
}
