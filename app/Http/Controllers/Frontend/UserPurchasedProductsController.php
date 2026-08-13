<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserPurchasedProductsController extends Controller
{
    public function index(Request $request): View
    {
        $purchasedProducts = $this->purchasedProducts();

        return view('frontend.dashboard.purchased-product.index', compact('purchasedProducts'));
    }

    public function show(int $id): View
    {
        abort_unless(Schema::hasTable('products'), 404);
        $this->ensureProductWasPurchased($id);

        $query = Product::query();

        if (Schema::hasTable('product_files')) {
            $query->with('files');
        }

        if (Schema::hasTable('stores')) {
            $query->with('store');
        }

        $product = $query->findOrFail($id);
        abort_unless($product->product_type === 'digital', 404);

        return view('frontend.dashboard.purchased-product.show', compact('product'));
    }

    public function download(int $product, int $file): StreamedResponse|RedirectResponse
    {
        $this->ensureProductWasPurchased($product);
        $query = Product::query();

        if (Schema::hasTable('product_files')) {
            $query->with('files');
        }

        $product = $query->findOrFail($product);
        abort_unless($product->product_type === 'digital', 404);
        $file = $product->files->firstWhere('id', $file);

        abort_unless($file, 404);

        if (Storage::disk('local')->exists($file->path)) {
            return Storage::disk('local')->download($file->path, $file->filename);
        }

        abort(404);
    }

    protected function purchasedProducts(): LengthAwarePaginator
    {
        if (! Schema::hasTable('orders') || ! Schema::hasTable('products')) {
            return new LengthAwarePaginator([], 0, 15);
        }

        $items = $this->orderItemPurchases()
            ->concat($this->legacyOrderPurchases())
            ->unique(fn (object $item) => $item->order_id.'-'.$item->id)
            ->sortByDesc('created_at')
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

    protected function orderItemPurchases(): Collection
    {
        if (! Schema::hasTable('order_products')) {
            return collect();
        }

        return DB::table('order_products')
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'products.id', '=', 'order_products.product_id')
            ->where('orders.user_id', auth('web')->id())
            ->whereIn('orders.payment_status', $this->paidStatuses())
            ->select([
                'products.id',
                'products.name as product_name',
                'products.slug',
                'products.product_type',
                'orders.id as order_id',
                'orders.invoice_id',
                'orders.created_at',
            ])
            ->get();
    }

    protected function legacyOrderPurchases(): Collection
    {
        if (! Schema::hasColumn('orders', 'product_id')) {
            return collect();
        }

        return DB::table('orders')
            ->join('products', 'products.id', '=', 'orders.product_id')
            ->where('orders.user_id', auth('web')->id())
            ->whereIn('orders.payment_status', $this->paidStatuses())
            ->select([
                'products.id',
                'products.name as product_name',
                'products.slug',
                'products.product_type',
                'orders.id as order_id',
                'orders.invoice_id',
                'orders.created_at',
            ])
            ->get();
    }

    protected function ensureProductWasPurchased(int $productId): void
    {
        $query = Order::query()
            ->where('user_id', auth('web')->id())
            ->whereIn('payment_status', $this->paidStatuses())
            ->where(function ($query) use ($productId) {
                if (Schema::hasColumn('orders', 'product_id')) {
                    $query->where('product_id', $productId);
                }

                if (Schema::hasTable('order_products')) {
                    $method = Schema::hasColumn('orders', 'product_id') ? 'orWhereExists' : 'whereExists';
                    $query->{$method}(function ($subQuery) use ($productId) {
                        $subQuery->selectRaw('1')
                            ->from('order_products')
                            ->whereColumn('order_products.order_id', 'orders.id')
                            ->where('order_products.product_id', $productId);
                    });
                }
            });

        abort_unless($query->exists(), 404);
    }

    protected function paidStatuses(): array
    {
        return ['paid', 'completed', 'complete', 'succeeded'];
    }
}
