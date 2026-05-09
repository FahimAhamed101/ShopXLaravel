<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserPurchasedProductsController extends Controller
{
    public function index(Request $request): View
    {
        $digitalProducts = $this->purchasedDigitalProducts();

        return view('frontend.dashboard.purchased-product.index', compact('digitalProducts'));
    }

    public function show(int $id): View
    {
        abort_unless(Schema::hasTable('products'), 404);

        $query = Product::query();

        if (Schema::hasTable('product_files')) {
            $query->with('files');
        }

        if (Schema::hasTable('stores')) {
            $query->with('store');
        }

        $product = $query->findOrFail($id);

        return view('frontend.dashboard.purchased-product.show', compact('product'));
    }

    public function download(int $product, int $file): StreamedResponse|RedirectResponse
    {
        $query = Product::query();

        if (Schema::hasTable('product_files')) {
            $query->with('files');
        }

        $product = $query->findOrFail($product);
        $file = $product->files->firstWhere('id', $file);

        abort_unless($file, 404);

        if (Storage::disk('local')->exists($file->path)) {
            return Storage::disk('local')->download($file->path, $file->filename);
        }

        abort(404);
    }

    protected function purchasedDigitalProducts(): LengthAwarePaginator
    {
        if (! Schema::hasTable('orders') || ! Schema::hasTable('products') || ! Schema::hasColumn('orders', 'product_id')) {
            return new LengthAwarePaginator([], 0, 15);
        }

        $orders = Order::query()
            ->where('user_id', auth('web')->id())
            ->whereNotNull('product_id')
            ->latest()
            ->get(['product_id', 'created_at']);

        $products = Product::query()
            ->whereIn('id', $orders->pluck('product_id'))
            ->when(Schema::hasColumn('products', 'product_type'), fn ($query) => $query->where('product_type', 'digital'))
            ->get()
            ->keyBy('id');

        $items = $orders
            ->map(function ($order) use ($products) {
                $product = $products->get($order->product_id);

                if (! $product) {
                    return null;
                }

                return (object) [
                    'id' => $product->id,
                    'product_name' => $product->name,
                    'created_at' => $order->created_at,
                ];
            })
            ->filter()
            ->unique('id')
            ->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;

        return new LengthAwarePaginator(
            $items->slice(($page - 1) * $perPage, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
    }
}
