<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class UserOrderController extends Controller
{
    public function index(): View
    {
        $orders = new LengthAwarePaginator([], 0, 15);

        if (Schema::hasTable('orders')) {
            $orders = Order::query()
                ->where('user_id', auth('web')->id())
                ->latest()
                ->paginate(15);
        }

        return view('frontend.dashboard.order.index', compact('orders'));
    }

    public function show(int $order): View
    {
        abort_unless(Schema::hasTable('orders'), 404);

        $order = Order::query()
            ->where('id', $order)
            ->where('user_id', auth('web')->id())
            ->firstOrFail();

        return view('frontend.dashboard.order.show', compact('order'));
    }
}
