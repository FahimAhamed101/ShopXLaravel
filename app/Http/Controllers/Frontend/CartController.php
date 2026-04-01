<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use App\Services\AlertService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    function index(): View
    {
        $cartItems = Cart::with('product')->where('user_id', user()->id)->paginate(30);
        if(Session::has('coupon')) {
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

        $modal = view('components.frontend.product-quick-view-modal', compact('product'))->render();

        return $modal;
    }

    function addToCart(Request $request)
    {
        // check user login
        if (!user()) {
            throw ValidationException::withMessages([
                'message' => 'Please login to add product to cart'
            ]);
        }

        $product = Product::findOrFail($request->product_id);
        $variantId = $request->variant_id;
        // $productInfo = $product->getVariantOrProductPriceAndStock($variantId);
        // dd($productInfo);
        // if(!$productInfo['in_stock']) {
        //     throw ValidationException::withMessages(["Product out of stock"]);
        // }


        $quantity = $request->quantity;
        $showModal = $request->modal;

        if ($showModal === 'true') {
            return response()->json([
                'status' => 'success',
                'modal' => $this->productModal($product),
                'show_modal' => true
            ]);
        }

        // check stock
        $this->checkStock($product, $variantId, $quantity);

        // Duplicate check
        if(Cart::where('user_id', user()->id)
            ->where('product_id', $product->id)
            ->when($variantId, fn($q) => $q->where('variant_id', $variantId))
            ->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product already added to cart'
                ], 409);
            }

        $this->store($request, $product);

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart successfully',
            'cart_count' => cartCount(),
            'show_modal' => false
        ]);
    }

    function checkStock(Product $product, $variantId, $quantity)
    {
        if($variantId) {
            $variant = $product->variants()->find($variantId);
            if(!$variant || !$variant->in_stock || ($variant->manage_stock && $variant->qty < $quantity)) {
                abort(422, 'Product out of stock');
            }

            if(!$product->primaryVariant) {
                if(!$product->in_stock || ($product->manage_stock && $product->qty < $quantity)) {
                    abort(422, 'Product out of stock');
                }
            }
        }
    }

    function store(Request $request, Product $product)
    {
        $cart = new Cart();
        $cart->user_id = user()->id;
        $cart->product_id = $product->id;
        $cart->variant_id = $request->variant_id;
        $cart->quantity = $request->quantity;
        $cart->name = $product->name;
        $cart->save();
    }


    function updateCart(Request $request)
    {

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
        $cartItems = Cart::with('product')->where('user_id', user()->id)->get();

        foreach ($cartItems as $cartItem) {
            $cartTotal += $cartItem->product->getVariantOrProductPriceAndStock($cartItem->variant_id)['price'] * $cartItem->quantity;
        }

        return $cartTotal;
    }


    function destroy(string $id) : JsonResponse
    {
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
}
