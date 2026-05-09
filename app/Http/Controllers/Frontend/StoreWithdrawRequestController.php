<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Store;
use App\Models\VendorWithdrawMethod;
use App\Models\WithdrawRequest;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class StoreWithdrawRequestController extends Controller
{
    public function index(): View
    {
        [$currentBalance, $pendingBalance, $totalWithdraw] = $this->balances();
        $withdrawRequests = $this->vendorRequestsQuery()->latest('id')->get();

        return view('vendor-dashboard.withdraw-request.index', compact(
            'currentBalance',
            'pendingBalance',
            'totalWithdraw',
            'withdrawRequests'
        ));
    }

    public function create(): View
    {
        [$currentBalance, $pendingBalance, $totalWithdraw] = $this->balances();
        $withdrawMethods = $this->vendorMethodsQuery()->with('withdrawMethod')->get();

        return view('vendor-dashboard.withdraw-request.create', compact(
            'currentBalance',
            'pendingBalance',
            'totalWithdraw',
            'withdrawMethods'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->requestsTableReady(), 404);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'integer'],
        ]);

        $method = $this->vendorMethodsQuery()->with('withdrawMethod')->findOrFail($data['method']);

        $withdrawRequest = new WithdrawRequest();
        $this->assignOwner($withdrawRequest);
        $withdrawRequest->fill(array_filter([
            'amount' => $data['amount'],
            'method_id' => $method->id,
            'withdraw_method_id' => $method->withdraw_method_id ?? null,
            'payment_method' => $method->withdrawMethod?->name ?? 'Manual',
            'payment_details' => $method->description ?? '',
            'status' => 'pending',
        ], fn ($value, $key) => Schema::hasColumn($withdrawRequest->getTable(), $key), ARRAY_FILTER_USE_BOTH));
        $withdrawRequest->save();

        AlertService::created('Withdraw request created successfully.');

        return redirect()->route('vendor.withdraw-requests.index');
    }

    public function destroy(int $withdraw_request): RedirectResponse
    {
        $withdrawRequest = $this->vendorRequestsQuery()->findOrFail($withdraw_request);

        if (($withdrawRequest->status ?? 'pending') === 'pending') {
            $withdrawRequest->delete();
        }

        AlertService::updated('Withdraw request updated successfully.');

        return back();
    }

    protected function balances(): array
    {
        $withdrawRequests = $this->vendorRequestsQuery()->get();
        $pendingBalance = (float) $withdrawRequests->where('status', 'pending')->sum('amount');
        $totalWithdraw = (float) $withdrawRequests->where('status', 'paid')->sum('amount');
        $amountColumn = Schema::hasColumn('orders', 'total')
            ? 'total'
            : (Schema::hasColumn('orders', 'sub_total') ? 'sub_total' : null);
        $grossSales = $amountColumn ? (float) $this->vendorOrdersQuery()->sum($amountColumn) : 0;

        return [
            max($grossSales - $pendingBalance - $totalWithdraw, 0),
            $pendingBalance,
            $totalWithdraw,
        ];
    }

    protected function vendorMethodsQuery()
    {
        $query = VendorWithdrawMethod::query();
        $table = $query->getModel()->getTable();

        if (! Schema::hasTable($table) || ! auth('web')->id()) {
            return $query->whereRaw('1 = 0');
        }

        if (Schema::hasColumn($table, 'user_id')) {
            return $query->where('user_id', auth('web')->id());
        }

        if (Schema::hasColumn($table, 'vendor_id')) {
            return $query->where('vendor_id', auth('web')->id());
        }

        if (Schema::hasColumn($table, 'seller_id')) {
            return $query->where('seller_id', auth('web')->id());
        }

        if (Schema::hasColumn($table, 'store_id')) {
            return $query->where('store_id', $this->vendorStoreId());
        }

        return $query->whereRaw('1 = 0');
    }

    protected function vendorRequestsQuery()
    {
        $query = WithdrawRequest::query();
        $table = $query->getModel()->getTable();

        if (! Schema::hasTable($table) || ! auth('web')->id()) {
            return $query->whereRaw('1 = 0');
        }

        if (Schema::hasColumn($table, 'user_id')) {
            return $query->where('user_id', auth('web')->id());
        }

        if (Schema::hasColumn($table, 'vendor_id')) {
            return $query->where('vendor_id', auth('web')->id());
        }

        if (Schema::hasColumn($table, 'seller_id')) {
            return $query->where('seller_id', auth('web')->id());
        }

        if (Schema::hasColumn($table, 'store_id')) {
            return $query->where('store_id', $this->vendorStoreId());
        }

        return $query->whereRaw('1 = 0');
    }

    protected function vendorOrdersQuery()
    {
        $query = Order::query();

        if (! Schema::hasTable('orders') || ! auth('web')->id()) {
            return $query->whereRaw('1 = 0');
        }

        foreach (['vendor_id', 'seller_id'] as $column) {
            if (Schema::hasColumn('orders', $column)) {
                return $query->where($column, auth('web')->id());
            }
        }

        if (Schema::hasColumn('orders', 'store_id')) {
            return $query->where('store_id', $this->vendorStoreId());
        }

        return $query->whereRaw('1 = 0');
    }

    protected function assignOwner(WithdrawRequest $withdrawRequest): void
    {
        $table = $withdrawRequest->getTable();

        foreach (['user_id', 'vendor_id', 'seller_id'] as $column) {
            if (Schema::hasColumn($table, $column)) {
                $withdrawRequest->{$column} = auth('web')->id();
            }
        }

        if (Schema::hasColumn($table, 'store_id')) {
            $withdrawRequest->store_id = $this->vendorStoreId();
        }
    }

    protected function vendorStoreId(): ?int
    {
        if (! Schema::hasTable('stores') || ! Schema::hasColumn('stores', 'seller_id')) {
            return null;
        }

        return Store::query()->where('seller_id', auth('web')->id())->value('id');
    }

    protected function requestsTableReady(): bool
    {
        return Schema::hasTable((new WithdrawRequest())->getTable());
    }
}
