<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\Wishlist;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class UserDashboardController extends Controller
{
    function index() : View
    {
        $userId = auth('web')->id();

        return view('frontend.dashboard.main.index', [
            'totalOrders' => $this->countRows(Order::query(), $userId, ['user_id']),
            'totalPendingOrders' => $this->countWhereIn(Order::query(), $userId, ['user_id'], ['pending'], ['order_status', 'status']),
            'totalCanceledOrders' => $this->countWhereIn(Order::query(), $userId, ['user_id'], ['canceled', 'cancelled', 'rejected'], ['order_status', 'status']),
            'totalReviews' => $this->countRows(ProductReview::query(), $userId, ['user_id']),
            'totalAddresses' => $this->countRows(Address::query(), $userId, ['user_id']),
            'totalWishlists' => $this->countRows(Wishlist::query(), $userId, ['user_id']),
        ]);
    }

    public function reviews(): View
    {
        $reviews = new LengthAwarePaginator([], 0, 15);

        if (class_exists(ProductReview::class) && Schema::hasTable('product_reviews')) {
            $reviews = ProductReview::query()
                ->with('product')
                ->where('user_id', auth('web')->id())
                ->latest()
                ->paginate(15);
        }

        return view('frontend.dashboard.review.index', compact('reviews'));
    }

    protected function countRows(Builder $query, ?int $userId, array $ownerColumns = []): int
    {
        $scopedQuery = $this->scopedQuery($query, $userId, $ownerColumns);

        return $scopedQuery ? $scopedQuery->count() : 0;
    }

    protected function countWhereIn(
        Builder $query,
        ?int $userId,
        array $ownerColumns,
        array $values,
        array $filterColumns = ['status']
    ): int {
        $scopedQuery = $this->scopedQuery($query, $userId, $ownerColumns);
        $filterColumn = $this->firstExistingColumn($query->getModel()->getTable(), $filterColumns);

        if (! $scopedQuery || ! $filterColumn) {
            return 0;
        }

        return $scopedQuery->whereIn($filterColumn, $values)->count();
    }

    protected function scopedQuery(Builder $query, ?int $userId, array $ownerColumns = []): ?Builder
    {
        $table = $query->getModel()->getTable();

        if (! Schema::hasTable($table)) {
            return null;
        }

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
