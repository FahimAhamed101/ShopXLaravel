<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class UserTrackOrderController extends Controller
{
    public function index(Request $request): View
    {
        $order = null;
        $orderId = trim((string) $request->query('order-id', ''));

        if ($orderId !== '' && Schema::hasTable('orders')) {
            $query = Order::query();

            if (Schema::hasColumn('orders', 'user_id')) {
                $query->where('user_id', auth('web')->id());
            }

            $query->where(function ($builder) use ($orderId) {
                $builder->where('id', $orderId);

                if (Schema::hasColumn('orders', 'invoice_id')) {
                    $builder->orWhere('invoice_id', $orderId);
                }
            });

            $order = $query->first();
        }

        return view('frontend.dashboard.track-order.index', compact('order'));
    }
}
