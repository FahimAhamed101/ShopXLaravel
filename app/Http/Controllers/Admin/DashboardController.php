<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{

    function index(Request $request): View
    {
        $orderStatusColumn = $this->firstExistingColumn('orders', ['order_status', 'status']);
        $orderAmountColumn = $this->firstExistingColumn('orders', ['total', 'sub_total']);
        $productApprovalColumn = $this->firstExistingColumn('products', ['approved_status']);
        $kycStatusColumn = $this->firstExistingColumn('kycs', ['status']);

        $pendingOrders = $this->countWhereIn('orders', $orderStatusColumn, ['pending']);
        $completedOrders = $this->countWhereIn('orders', $orderStatusColumn, ['delivered', 'completed']);
        $totalOrders = $this->countRows('orders');
        $canceledOrders = $this->countWhereIn('orders', $orderStatusColumn, ['canceled', 'cancelled']);
        $totalProducts = $this->countRows('products');
        $totalPendingProducts = $this->countWhereIn('products', $productApprovalColumn, ['pending']);
        $totalApprovedProducts = $this->countWhereIn('products', $productApprovalColumn, ['approved']);
        $totalRejectedProducts = $this->countWhereIn('products', $productApprovalColumn, ['rejected']);
        $totalPendingKycRequests = $this->countWhereIn('kycs', $kycStatusColumn, ['pending']);
        $totalApprovedKycRequests = $this->countWhereIn('kycs', $kycStatusColumn, ['approved']);
        $totalRejectedKycRequests = $this->countWhereIn('kycs', $kycStatusColumn, ['rejected']);
        $totalKycRequests = $this->countRows('kycs');


        $month = $request->get('month', Carbon::now()->format('Y-m'));

        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Daily Orders + Total Amounts
        $orders = $this->dailyOrders($start, $end, $orderAmountColumn);

        $commissions = $this->dailySum('admin_commissions', 'commission_amount', 'total_commission', $start, $end);

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
        $totalSales = $this->sumBetween('orders', $orderAmountColumn, $yearStart, $yearEnd);

        $totalCommission = $this->sumBetween('admin_commissions', 'commission_amount', $yearStart, $yearEnd);

        $pendingKycs = $this->latestWhereIn(Kyc::class, 'kycs', $kycStatusColumn, ['pending'], 5, ['user']);
        $recentPendingOrders = Route::has('admin.orders.show')
            ? $this->latestWhereIn(Order::class, 'orders', $orderStatusColumn, ['pending'], 5)
            : collect();
        $pendingProducts = $this->latestWhereIn(Product::class, 'products', $productApprovalColumn, ['pending'], 5);


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

    protected function countRows(string $table): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return DB::table($table)->count();
    }

    protected function countWhereIn(string $table, ?string $column, array $values): int
    {
        if (! $column || ! Schema::hasTable($table)) {
            return 0;
        }

        return DB::table($table)->whereIn($column, $values)->count();
    }

    protected function dailyOrders(Carbon $start, Carbon $end, ?string $amountColumn): Collection
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'created_at')) {
            return collect();
        }

        $amountSelect = $amountColumn ? "SUM({$amountColumn})" : '0';

        return DB::table('orders')
            ->selectRaw("DATE(created_at) as date, COUNT(id) as orders, {$amountSelect} as total_amount")
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    protected function dailySum(string $table, string $column, string $alias, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'created_at') || ! Schema::hasColumn($table, $column)) {
            return collect();
        }

        return DB::table($table)
            ->selectRaw("DATE(created_at) as date, SUM({$column}) as {$alias}")
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    protected function sumBetween(string $table, ?string $column, Carbon $start, Carbon $end): float|int
    {
        if (! $column || ! Schema::hasTable($table) || ! Schema::hasColumn($table, 'created_at')) {
            return 0;
        }

        return DB::table($table)
            ->whereBetween('created_at', [$start, $end])
            ->sum($column);
    }

    protected function latestWhereIn(string $model, string $table, ?string $column, array $values, int $limit, array $with = []): Collection
    {
        if (! $column || ! Schema::hasTable($table)) {
            return collect();
        }

        return $model::query()
            ->with($with)
            ->whereIn($column, $values)
            ->latest()
            ->take($limit)
            ->get();
    }

    protected function firstExistingColumn(string $table, array $columns): ?string
    {
        if (! Schema::hasTable($table)) {
            return null;
        }

        foreach ($columns as $column) {
            if (Schema::hasColumn($table, $column)) {
                return $column;
            }
        }

        return null;
    }
}
