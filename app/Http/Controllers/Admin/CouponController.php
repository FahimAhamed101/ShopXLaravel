<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class CouponController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:Ecommerce Management')];
    }

    public function index(): View
    {
        $coupons = Coupon::query()->latest()->paginate(20);

        return view('admin.coupon.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('admin.coupon.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Coupon::query()->create($this->validatedPayload($request));
        AlertService::created('Coupon created successfully.');

        return to_route('admin.coupons.index');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupon.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($this->validatedPayload($request, $coupon));
        AlertService::updated('Coupon updated successfully.');

        return to_route('admin.coupons.index');
    }

    public function destroy(Coupon $coupon): JsonResponse
    {
        $coupon->delete();
        AlertService::deleted('Coupon deleted successfully.');

        return response()->json(['status' => 'success', 'message' => 'Coupon deleted successfully.']);
    }

    private function validatedPayload(Request $request, ?Coupon $coupon = null): array
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', Rule::unique('coupons', 'code')->ignore($coupon)],
            'value' => [
                'required',
                'numeric',
                'gt:0',
                Rule::when($request->boolean('is_percent'), ['lte:100']),
            ],
            'is_percent' => ['required', 'boolean'],
            'minimum_spend' => ['required', 'numeric', 'min:0'],
            'maximum_spend' => ['required', 'numeric', 'gte:minimum_spend'],
            'usage_limit_per_coupon' => ['required', 'integer', 'min:1'],
            'usage_limit_per_customer' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
