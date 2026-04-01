<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminCommission;
use App\Models\Kyc;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    function index(Request $request): View
    {
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $completedOrders = Order::where('order_status', 'delivered')->count();
        $totalOrders = Order::count();
        $canceledOrders = Order::where('order_status', 'canceled')->count();
        $totalProducts = Product::count();
        $totalPendingProducts = Product::where('approved_status', 'pending')->count();
        $totalApprovedProducts = Product::where('approved_status', 'approved')->count();
        $totalRejectedProducts = Product::where('approved_status', 'rejected')->count();
        $totalPendingKycRequests = Kyc::where('status', 'pending')->count();
        $totalApprovedKycRequests = Kyc::where('status', 'approved')->count();
        $totalRejectedKycRequests = Kyc::where('status', 'rejected')->count();
        $totalKycRequests = Kyc::count();


        $month = $request->get('month', Carbon::now()->format('Y-m'));

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Daily Orders + Total Amounts
        $orders = Order::selectRaw('DATE(created_at) as date, COUNT(id) as orders, SUM(total) as total_amount')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $commissions = AdminCommission::selectRaw('DATE(created_at) as date, SUM(commission_amount) as total_commission')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = [];
        $ordersData = [];
        $amountData = [];
        $commissionData = [];

        foreach ($start->daysUntil($end) as $day) {
            $date = $day->format('Y-m-d');
            $dates[] = $date;

            $order = $orders->firstWhere('date', $date);

            $commission = $commissions->firstWhere('date', $date);

            $ordersData[] = $order->orders ?? 0;
            $amountData[] = $order->total_amount ?? 0;
            $commissionData[] = $commission->total_commission ?? 0;
        }

        $months = collect(range(0, 11))->map(function($i) {
            $date = Carbon::now()->subMonths($i);
            return [
                'value' => $date->format('Y-m'),
                'label' => $date->format('F Y')
            ];
        })->reverse()->values();

        $yearStart = Carbon::now()->startOfYear();
        $yearEnd = Carbon::now()->endOfYear();
        $totalSales = Order::whereBetween('created_at', [$yearStart, $yearEnd])->sum('total');

        $totalCommission = AdminCommission::whereBetween('created_at', [$yearStart, $yearEnd])->sum('commission_amount');

        $pendingKycs = Kyc::where('status', 'pending')->latest()->take(5)->get();
        $recentPendingOrders = Order::where('order_status', 'pending')->latest()->take(5)->get();
        $pendingProducts = Product::where('approved_status', 'pending')->latest()->take(5)->get();


        return view('admin.dashboard.index', compact(
            'pendingOrders',
            'completedOrders',
            'totalOrders',
            'canceledOrders',
            'totalProducts',
            'totalPendingProducts',
            'totalApprovedProducts',
            'totalRejectedProducts',
            'totalPendingKycRequests',
            'totalApprovedKycRequests',
            'totalRejectedKycRequests',
            'totalKycRequests',
            'orders',
            'commissions',
            'dates',
            'ordersData',
            'amountData',
            'commissionData',
            'months',
            'month',
            'totalSales',
            'totalCommission',
            'pendingKycs',
            'recentPendingOrders',
            'pendingProducts'
        ));
    }
}
