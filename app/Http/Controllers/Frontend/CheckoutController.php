<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\ShippingRule;
use App\Models\Store;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (cartTotal() === 0) {
            AlertService::error('Your cart is empty.');

            return redirect()->route('cart.index');
        }

        $groupedCartItems = $this->groupedCartItems();
        $shippingMethods = Schema::hasTable('shipping_rules')
            ? ShippingRule::query()->get()
            : collect();

        return view('frontend.pages.checkout', compact('groupedCartItems', 'shippingMethods'));
    }

    public function shippingMethod(int $id): JsonResponse
    {
        $charge = 0;

        if (Schema::hasTable('shipping_rules')) {
            $charge = (float) (ShippingRule::find($id)?->charge ?? 0);
        }

        return response()->json([
            'charge' => $charge,
            'total' => round(cartTotal() + $charge - cartDiscount(), 2),
        ]);
    }

    public function billingInfo(Request $request): JsonResponse
    {
        $request->validate([
            'shipping_method_id' => ['nullable', 'integer'],
            'billing_address_id' => ['nullable', 'integer'],
            'shipping_address_id' => ['nullable', 'integer'],
        ]);

        $billingAddress = $this->ownedAddress($request->integer('billing_address_id'));
        $shippingAddress = $this->ownedAddress($request->integer('shipping_address_id'));

        Session::put('billing_info', [
            'shipping_method_id' => $request->integer('shipping_method_id'),
            'billing_address' => $billingAddress ? $this->addressPayload($billingAddress) : null,
            'shipping_address' => $shippingAddress ? $this->addressPayload($shippingAddress) : null,
        ]);

        return response()->json([
            'redirect_url' => route('payment.index'),
        ]);
    }

    protected function groupedCartItems(): Collection
    {
        $query = Cart::query()->with('product');

        if (Schema::hasTable('stores')) {
            $query->with('product.store');
        }

        return $query
            ->where('user_id', auth('web')->id())
            ->get()
            ->groupBy(fn ($cartItem) => data_get($cartItem, 'product.store.id', 'default'))
            ->map(function ($items) {
                return [
                    'store' => data_get($items->first(), 'product.store') ?: Store::query()->make(['name' => 'ShopX']),
                    'items' => $items,
                ];
            });
    }

    protected function ownedAddress(?int $id): ?Address
    {
        if (! $id || ! Schema::hasTable('addresses')) {
            return null;
        }

        return Address::query()
            ->where('id', $id)
            ->where('user_id', auth('web')->id())
            ->first();
    }

    protected function addressPayload(Address $address): array
    {
        return [
            'first_name' => $address->first_name,
            'last_name' => $address->last_name,
            'address' => $address->address,
            'city' => $address->city,
            'state' => $address->state,
            'country' => $address->country,
            'email' => $address->email,
            'phone' => $address->phone,
        ];
    }
}
