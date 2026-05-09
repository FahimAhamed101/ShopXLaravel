<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class VendorPageController extends Controller
{
    function index() : View
    {
        $query = User::query()->where('user_type', 'vendor');

        if (Schema::hasTable('stores')) {
            $query->whereHas('store')->with('store');
        }

        if (Schema::hasTable('kycs') && Schema::hasColumn('kycs', 'status')) {
            $query->with(['kyc' => function ($builder) {
                $builder->where('status', 'approved');
            }]);
        }

        if (Schema::hasTable('products')) {
            $query->withCount('products');
        }

        $vendors = $query->paginate(16);

        if (Schema::hasTable('stores') && Schema::hasTable('products') && Schema::hasTable('product_reviews')) {
            $vendors->getCollection()->load([
                'store' => function ($builder) {
                    $builder->withAvg('reviews', 'rating');
                },
            ]);
        }

        return view('frontend.pages.vendor', compact('vendors'));
    }

    function show(int $id) : View
    {
        abort_unless(Schema::hasTable('stores'), 404);

        $query = Store::query()->where('seller_id', $id);

        if (Schema::hasTable('products')) {
            $query->with(['products' => function ($builder) {
                if (Schema::hasTable('product_images')) {
                    $builder->with('images');
                }

                if (Schema::hasTable('product_reviews')) {
                    $builder->withAvg('reviews', 'rating');
                }
            }]);
        }

        if (Schema::hasTable('products') && Schema::hasTable('product_reviews')) {
            $query->withAvg('reviews', 'rating');
        }

        $store = $query->firstOrFail();

        return view('frontend.pages.vendor-detail', compact('store'));
    }
}
