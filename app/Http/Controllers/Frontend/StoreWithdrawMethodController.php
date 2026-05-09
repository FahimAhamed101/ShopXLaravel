<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\VendorWithdrawMethod;
use App\Models\WithdrawMethod;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class StoreWithdrawMethodController extends Controller
{
    public function index(): View
    {
        $withdrawMethods = $this->vendorMethodsQuery()->with('withdrawMethod')->latest('id')->get();

        return view('vendor-dashboard.withdraw-method.index', compact('withdrawMethods'));
    }

    public function create(): View
    {
        $withdrawMethods = $this->availableMethods();

        return view('vendor-dashboard.withdraw-method.create', compact('withdrawMethods'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->methodsTableReady(), 404);

        $data = $request->validate([
            'gateway' => ['required', 'integer'],
            'description' => ['required', 'string'],
        ]);

        $withdrawMethod = new VendorWithdrawMethod();
        $this->assignOwner($withdrawMethod);
        $withdrawMethod->fill(array_filter([
            'withdraw_method_id' => $data['gateway'],
            'description' => $data['description'],
        ], fn ($value, $key) => Schema::hasColumn($withdrawMethod->getTable(), $key), ARRAY_FILTER_USE_BOTH));
        $withdrawMethod->save();

        AlertService::created('Withdraw method created successfully.');

        return redirect()->route('vendor.withdraw-methods.index');
    }

    public function show(string $id): RedirectResponse
    {
        return redirect()->route('vendor.withdraw-methods.edit', $id);
    }

    public function edit(int $withdraw_method): View
    {
        $withdraw_method = $this->vendorMethodsQuery()->with('withdrawMethod')->findOrFail($withdraw_method);
        $withdrawMethods = $this->availableMethods();

        return view('vendor-dashboard.withdraw-method.edit', compact('withdraw_method', 'withdrawMethods'));
    }

    public function update(Request $request, int $withdraw_method): RedirectResponse
    {
        $data = $request->validate([
            'gateway' => ['required', 'integer'],
            'description' => ['required', 'string'],
        ]);

        $withdrawMethod = $this->vendorMethodsQuery()->findOrFail($withdraw_method);
        $withdrawMethod->fill(array_filter([
            'withdraw_method_id' => $data['gateway'],
            'description' => $data['description'],
        ], fn ($value, $key) => Schema::hasColumn($withdrawMethod->getTable(), $key), ARRAY_FILTER_USE_BOTH));
        $withdrawMethod->save();

        AlertService::updated('Withdraw method updated successfully.');

        return redirect()->route('vendor.withdraw-methods.index');
    }

    public function destroy(int $withdraw_method): RedirectResponse
    {
        $withdrawMethod = $this->vendorMethodsQuery()->findOrFail($withdraw_method);
        $withdrawMethod->delete();

        AlertService::updated('Withdraw method deleted successfully.');

        return back();
    }

    protected function availableMethods(): Collection
    {
        if (! Schema::hasTable('withdraw_methods')) {
            return collect();
        }

        return WithdrawMethod::query()->get();
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

    protected function assignOwner(VendorWithdrawMethod $withdrawMethod): void
    {
        $table = $withdrawMethod->getTable();

        foreach (['user_id', 'vendor_id', 'seller_id'] as $column) {
            if (Schema::hasColumn($table, $column)) {
                $withdrawMethod->{$column} = auth('web')->id();
            }
        }

        if (Schema::hasColumn($table, 'store_id')) {
            $withdrawMethod->store_id = $this->vendorStoreId();
        }
    }

    protected function vendorStoreId(): ?int
    {
        if (! Schema::hasTable('stores') || ! Schema::hasColumn('stores', 'seller_id')) {
            return null;
        }

        return Store::query()->where('seller_id', auth('web')->id())->value('id');
    }

    protected function methodsTableReady(): bool
    {
        return Schema::hasTable((new VendorWithdrawMethod())->getTable());
    }
}
