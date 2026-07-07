<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    function index(): View
    {
        $cartItems = $this->cartItems();

        if (Session::has('coupon') && Schema::hasTable('coupons')) {
            $coupon = Coupon::find(Session::get('coupon')['id']);
            $validateCoupon = $this->validateCoupon($coupon, $this->cartSubTotal());
            if(isset($validateCoupon['error'])) {
                Session::forget('coupon');
            }
        }
        return view('frontend.pages.cart', compact('cartItems'));
    }

    function productModal(Product $product): String
    {
        if (! view()->exists('components.frontend.product-quick-view-modal')) {
            return '';
        }

        if (method_exists($product, 'loadMissing')) {
            $product->loadMissing(['variants.attributeValues', 'images', 'tags']);
        }

        $modal = view('components.frontend.product-quick-view-modal', compact('product'))->render();

        return $modal;
    }

    function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'variant_id' => ['nullable', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = max((int) ($request->quantity ?? 1), 1);
        $variant = $this->resolveVariantForCart($product, $request->variant_id);
        $variantId = $variant?->id;
        // $productInfo = $product->getVariantOrProductPriceAndStock($variantId);
        // dd($productInfo);
        // if(!$productInfo['in_stock']) {
        //     throw ValidationException::withMessages(["Product out of stock"]);
        // }

        $showModal = $request->modal;

        if ($showModal === 'true') {
            return response()->json([
                'status' => 'success',
                'modal' => $this->productModal($product),
                'show_modal' => true
            ]);
        }

        // check stock
        $this->checkStock($product, $variant, $quantity);

        // Duplicate check
        if ($this->cartItemExists($product->id, $variantId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product already added to cart'
                ], 409);
            }

        $this->store($request, $product, $variantId, $quantity);

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart successfully',
            'cart_count' => cartCount(),
            'show_modal' => false
        ]);
    }

    function checkStock(Product $product, ?ProductVariant $variant, int $quantity)
    {
        if($variant) {
            if(!$variant->in_stock || !$variant->is_active || ($variant->manage_stock && $variant->qty < $quantity)) {
                abort(422, 'Product out of stock');
            }

            return;
        }

        $manageStock = $product->manage_stock === 'yes' || $product->manage_stock === 1 || $product->manage_stock === true;

        if(!$product->in_stock || ($manageStock && (int) $product->qty < $quantity)) {
            abort(422, 'Product out of stock');
        }
    }

    function store(Request $request, Product $product, mixed $variantId, int $quantity)
    {
        if (! user()) {
            $cart = Session::get('guest_cart', []);
            $key = $this->guestCartKey($product->id, $variantId);

            $cart[$key] = [
                'id' => $key,
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'name' => $product->name,
            ];

            Session::put('guest_cart', $cart);

            return;
        }

        $cart = new Cart();
        $cart->user_id = user()->id;
        $cart->product_id = $product->id;
        $cart->variant_id = $variantId;
        $cart->quantity = $quantity;
        $cart->name = $product->name;
        $cart->save();
    }


    function updateCart(Request $request)
    {
        if (! user()) {
            $cart = Session::get('guest_cart', []);
            $cartItem = $this->guestCartItems()->firstWhere('id', $request->id);

            if (! $cartItem || ! isset($cart[$request->id])) {
                return response()->json(['message' => 'Cart item not found'], 404);
            }

            $product = $cartItem->product;
            $productPriceAndQty = $product->getVariantOrProductPriceAndStock($cartItem->variant_id);

            if (!$productPriceAndQty['in_stock']) {
                return response()->json(['message' => 'Product out of stock'], 422);
            }

            if ($productPriceAndQty['qty'] > $request->qty || $productPriceAndQty['qty'] == 'Unlimited') {
                $cart[$request->id]['quantity'] = max((int) $request->qty, 1);
                Session::put('guest_cart', $cart);
                $cartItems = $this->guestCartItems();
                $cartHtml = view('components.frontend.cart-item', compact('cartItems'))->render();

                return response()->json([
                    'message' => 'Cart updated successfully',
                    'html' => $cartHtml,
                    'cart_sub_total' => $this->cartSubTotal(),
                ], 200);
            }

            return response()->json(['message' => 'Product out of stock'], 422);
        }

        $cartItem = Cart::findOrFail($request->id);
        $product = Product::findOrFail($cartItem->product_id);
        $productPriceAndQty = $product->getVariantOrProductPriceAndStock($cartItem->variant_id);


        if(!$productPriceAndQty['in_stock']){
            return response()->json([
                'message' => 'Product out of stock'
            ], 422);
        }

        if($productPriceAndQty['qty'] > $request->qty || $productPriceAndQty['qty'] == 'Unlimited') {
            $cartItem->quantity = $request->qty;
            $cartItem->save();

            $cartItems = Cart::with('product')->where('user_id', user()->id)->get();
            $cartHtml = view('components.frontend.cart-item', compact('cartItems'))->render();
            return response()->json([
                'message' => 'Cart updated successfully',
                'html' => $cartHtml,
                'cart_sub_total' => $this->cartSubTotal()
            ], 200);
        }

        return response()->json([
            'message' => 'Product out of stock'
        ], 422);

    }

    function cartSubTotal()
    {
        $cartTotal = 0;
        $cartItems = user()
            ? Cart::with('product')->where('user_id', user()->id)->get()
            : $this->guestCartItems();

        foreach ($cartItems as $cartItem) {
            $cartTotal += $cartItem->product->getVariantOrProductPriceAndStock($cartItem->variant_id)['price'] * $cartItem->quantity;
        }

        return $cartTotal;
    }


    function destroy(string $id) : JsonResponse
    {
        if (! user()) {
            $cart = Session::get('guest_cart', []);
            unset($cart[$id]);
            Session::put('guest_cart', $cart);

            return response()->json([
                'status' => 'success',
                'message' => 'Cart item deleted successfully',
            ], 200);
        }

        $cartItem = Cart::findOrFail($id);
        $cartItem->delete();
        AlertService::updated('Cart item deleted successfully');

        return response()->json([
            'status' => 'success',
            'message' => 'Cart item deleted successfully',
        ], 200);
    }


    function applyCoupon(Request $request)
    {
        if (! Schema::hasTable('coupons')) {
            return response()->json([
                'message' => 'Coupons are not available right now.',
            ], 422);
        }

        $coupon = Coupon::where('code', $request->coupon_code)->first();
        $cartTotal = $this->cartSubTotal();

        $validation = $this->validateCoupon($coupon, $cartTotal);
        if(isset($validation['error'])) {
            return response()->json([
                'message' => $validation['error'],
            ], 422);
        }
        $discount = $coupon->is_percent ? $cartTotal * ($coupon->value / 100) : $coupon->value;
        // cap discount so it doesnt exceed cart total
        $discount = min($discount, $cartTotal);

        $total = round($cartTotal - $discount, 2);

        Session::put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'coupon_type' => $coupon->is_percent ? '%' : 'fixed',
            'coupon_value' => $coupon->value,
        ]);

        return response()->json([
            'status' => 'success',
            'discount' => $discount,
            'coupon_type' => $coupon->is_percent ? '%' : 'fixed',
            'coupon_value' => $coupon->value,
            'total' => $total,
            'message' => 'Coupon code applied successfully',
        ], 200);
    }

    function validateCoupon($coupon, $cartTotal)
    {

        if(!$coupon) return ['error' => 'Invalid coupon code'];

        if(!$coupon->is_active) return ['error' => 'Coupon code is not active'];

        if(Carbon::now()->lt($coupon->start_date) || Carbon::now()->gt($coupon->end_date)) return ['error' => 'Coupon is expired or not yet valid.'];

        if($cartTotal < $coupon->minimum_spend) return ['error' => 'Minimum spend not reached.'];

        if($cartTotal > $coupon->maximum_spend) return ['error' => 'Maximum spend exceeded.'];

        if($coupon->used >= $coupon->usage_limit_per_coupon) return ['error' => 'Coupon usage limit exceeded.'];

        // check can user user the coupon

        return [];
    }

    function destroyCoupon()
    {
        Session::forget('coupon');
        return response()->json([
            'status' => 'success',
            'message' => 'Coupon code removed successfully',
        ], 200);
    }

    protected function cartItems(): LengthAwarePaginator
    {
        if (user() && Schema::hasTable('carts')) {
            return Cart::with('product')->where('user_id', user()->id)->paginate(30);
        }

        $items = $this->guestCartItems();

        return new LengthAwarePaginator($items, $items->count(), 30, 1, [
            'path' => request()->url(),
            'pageName' => 'page',
        ]);
    }

    protected function guestCartItems(): Collection
    {
        $cart = collect(Session::get('guest_cart', []));
        $productIds = $cart->pluck('product_id')->filter()->unique();
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');

        return $cart->map(function ($item) use ($products) {
            return (object) [
                'id' => $item['id'],
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
                'name' => $item['name'] ?? null,
                'product' => $products->get($item['product_id']),
            ];
        })->filter(fn ($item) => $item->product)->values();
    }

    protected function cartItemExists(int $productId, mixed $variantId): bool
    {
        if (user()) {
            return Cart::where('user_id', user()->id)
                ->where('product_id', $productId)
                ->when($variantId, fn ($q) => $q->where('variant_id', $variantId), fn ($q) => $q->whereNull('variant_id'))
                ->exists();
        }

        return $this->guestCartItems()
            ->contains(fn ($item) => (int) $item->product_id === $productId && (string) $item->variant_id === (string) $variantId);
    }

    protected function guestCartKey(int $productId, mixed $variantId): string
    {
        return $productId.'-'.($variantId ?: 'product');
    }

    protected function resolveVariantForCart(Product $product, mixed $variantId): ?ProductVariant
    {
        $activeVariants = $product->variants()
            ->when(Schema::hasColumn('product_variants', 'is_active'), fn ($query) => $query->where('is_active', 1));

        if (! $activeVariants->exists()) {
            return null;
        }

        if ($variantId) {
            $variant = (clone $activeVariants)->whereKey($variantId)->first();

            if (! $variant) {
                abort(422, 'Please select a valid product variant');
            }

            return $variant;
        }

        $variant = (clone $activeVariants)
            ->when(Schema::hasColumn('product_variants', 'is_default'), fn ($query) => $query->orderByDesc('is_default'))
            ->orderBy('id')
            ->first();

        if (! $variant) {
            abort(422, 'Please select a product variant');
        }

        return $variant;
    }
}
