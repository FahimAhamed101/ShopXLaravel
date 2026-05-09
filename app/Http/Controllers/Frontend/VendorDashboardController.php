<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VendorDashboardController extends Controller
{
    public function index(): View
    {
        $vendorId = auth('web')->id();

        $totalOrders = $this->countRows('orders', $vendorId, ['vendor_id', 'seller_id', 'user_id']);
        $totalProducts = $this->countRows('products', $vendorId, ['vendor_id', 'user_id']);
        $totalDigitalProducts = $this->countByStatuses('products', ['digital'], $vendorId, ['vendor_id', 'user_id'], ['product_type']);
        $totalPhysicalProducts = $this->firstExistingColumn('products', ['product_type'])
            ? $this->countByStatuses('products', ['physical'], $vendorId, ['vendor_id', 'user_id'], ['product_type'])
            : $totalProducts;

        return view('vendor-dashboard.dashboard.index', [
            'pendingOrders' => $this->countByStatuses('orders', ['pending', 'processed', 'packed', 'shipped'], $vendorId, ['vendor_id', 'seller_id', 'user_id'], ['order_status', 'status']),
            'completedOrders' => $this->countByStatuses('orders', ['completed', 'delivered'], $vendorId, ['vendor_id', 'seller_id', 'user_id'], ['order_status', 'status']),
            'canceledOrders' => $this->countByStatuses('orders', ['canceled', 'cancelled', 'rejected'], $vendorId, ['vendor_id', 'seller_id', 'user_id'], ['order_status', 'status']),
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalDigitalProducts' => $totalDigitalProducts,
            'totalPhysicalProducts' => $totalPhysicalProducts,
        ]);
    }

    protected function countRows(string $table, ?int $userId, array $ownerColumns = []): int
    {
        $query = $this->scopedQuery($table, $userId, $ownerColumns);

        return $query ? (clone $query)->count() : 0;
    }

    protected function countByStatuses(
        string $table,
        array $values,
        ?int $userId,
        array $ownerColumns = [],
        array $filterColumns = ['status']
    ): int {
        $query = $this->scopedQuery($table, $userId, $ownerColumns);
        $filterColumn = $this->firstExistingColumn($table, $filterColumns);

        if (! $query || ! $filterColumn) {
            return 0;
        }

        return (clone $query)
            ->whereIn($filterColumn, $values)
            ->count();
    }

    protected function scopedQuery(string $table, ?int $userId, array $ownerColumns = []): ?Builder
    {
        if (! Schema::hasTable($table)) {
            return null;
        }

        $query = DB::table($table);
        $ownerColumn = $this->firstExistingColumn($table, $ownerColumns);

        if ($ownerColumn && $userId) {
            $query->where($ownerColumn, $userId);
        }

        return $query;
    }

    protected function firstExistingColumn(string $table, array $columns): ?string
    {
        foreach ($columns as $column) {
            if (Schema::hasColumn($table, $column)) {
                return $column;
            }
        }

        return null;
    }
}
