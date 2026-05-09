<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $this->vendorOrdersQuery($request->string('status')->toString())
            ->latest()
            ->paginate(20);

        return view('vendor-dashboard.order.index', compact('orders'));
    }

    public function show(int $order): View
    {
        $order = $this->vendorOrdersQuery()->findOrFail($order);

        return view('vendor-dashboard.order.show', compact('order'));
    }

    public function update(Request $request, int $order): RedirectResponse
    {
        $order = $this->vendorOrdersQuery()->findOrFail($order);
        $statusColumn = $this->firstExistingOrderColumn(['order_status', 'status']);

        if ($statusColumn && $request->filled('order_status')) {
            $order->{$statusColumn} = $request->string('order_status')->toString();
            $order->save();
        }

        return redirect()->route('vendor.orders.show', $order);
    }

    protected function vendorOrdersQuery(string $status = ''): \Illuminate\Database\Eloquent\Builder
    {
        $query = Order::query();
        $vendorId = auth('web')->id();

        if (! Schema::hasTable('orders') || ! $vendorId) {
            return $query->whereRaw('1 = 0');
        }

        $ownerColumn = $this->firstExistingOrderColumn(['vendor_id', 'seller_id', 'user_id']);

        if ($ownerColumn) {
            $query->where($ownerColumn, $vendorId);
        } elseif (Schema::hasColumn('orders', 'store_id')) {
            $storeId = $this->vendorStoreId();

            if (! $storeId) {
                return $query->whereRaw('1 = 0');
            }

            $query->where('store_id', $storeId);
        } else {
            return $query->whereRaw('1 = 0');
        }

        $statusColumn = $this->firstExistingOrderColumn(['order_status', 'status']);

        if ($status !== '' && $statusColumn) {
            $query->where($statusColumn, $status);
        }

        return $query->with('user');
    }

    protected function firstExistingOrderColumn(array $columns): ?string
    {
        foreach ($columns as $column) {
            if (Schema::hasColumn('orders', $column)) {
                return $column;
            }
        }

        return null;
    }

    protected function vendorStoreId(): ?int
    {
        $vendorId = auth('web')->id();

        if (! $vendorId || ! Schema::hasTable('stores') || ! Schema::hasColumn('stores', 'seller_id')) {
            return null;
        }

        return DB::table('stores')->where('seller_id', $vendorId)->value('id');
    }
}
