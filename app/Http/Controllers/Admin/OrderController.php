<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $orders = Order::query()
            ->with(['user', 'product.store'])
            ->when(
                $status !== '' && $this->firstExistingOrderColumn(['order_status', 'status']),
                function ($query) use ($status) {
                    $statusColumn = $this->firstExistingOrderColumn(['order_status', 'status']);

                    if ($statusColumn) {
                        $query->where($statusColumn, $status);
                    }
                }
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.order.index', compact('orders', 'status'));
    }

    public function show(Order $order): View
    {
        $order->loadMissing(['user', 'product.store', 'orderProducts.product', 'orderProducts.variant']);

        return view('admin.order.show', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'order_status' => ['required', 'string', 'in:' . implode(',', array_keys(config('order_status', [])))],
        ]);

        $statusColumn = $this->firstExistingOrderColumn(['order_status', 'status']);

        if ($statusColumn) {
            $order->{$statusColumn} = $validated['order_status'];
            $order->save();
        }

        return redirect()->route('admin.orders.show', $order)->with('status', 'Order status updated successfully.');
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
}
