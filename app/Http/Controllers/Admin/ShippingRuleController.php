<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRule;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ShippingRuleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:Ecommerce Management')];
    }

    public function index(): View
    {
        $shippingRules = ShippingRule::query()->latest()->paginate(20);

        return view('admin.shipping-rule.index', compact('shippingRules'));
    }

    public function create(): View
    {
        return view('admin.shipping-rule.create');
    }

    public function store(Request $request): RedirectResponse
    {
        ShippingRule::query()->create($this->validatedPayload($request));
        AlertService::created('Shipping rule created successfully.');

        return to_route('admin.shipping-rules.index');
    }

    public function edit(ShippingRule $shippingRule): View
    {
        return view('admin.shipping-rule.edit', compact('shippingRule'));
    }

    public function update(Request $request, ShippingRule $shippingRule): RedirectResponse
    {
        $shippingRule->update($this->validatedPayload($request));
        AlertService::updated('Shipping rule updated successfully.');

        return to_route('admin.shipping-rules.index');
    }

    public function destroy(ShippingRule $shippingRule): JsonResponse
    {
        $shippingRule->delete();
        AlertService::deleted('Shipping rule deleted successfully.');

        return response()->json(['status' => 'success', 'message' => 'Shipping rule deleted successfully.']);
    }

    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:flat_amount,minimum_order_amount'],
            'minimum_amount' => ['nullable', 'required_if:type,minimum_order_amount', 'numeric', 'min:0'],
            'charge' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['minimum_amount'] = $validated['type'] === 'minimum_order_amount'
            ? $validated['minimum_amount']
            : 0;
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
