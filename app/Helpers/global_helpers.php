<?php

/** check user has permission */

use App\Models\Cart;
use App\Models\Category;
use App\Models\ShippingRule;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use App\Models\Setting;

if (!function_exists('hasPermission')) {
    function hasPermission(array $permissions): bool
    {
        $user = Auth::guard('admin')->user();

        if (!$user) {
            return false;
        }

        // Super Admin bypass (optional)
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->hasAnyPermission($permissions);
    }
}


// if (!function_exists('hasPermission')) {
//     function hasPermission(array $permissions): bool
//     {
//         if (auth('admin')->user()->hasRole('Super Admin')) return true;

//         return auth('admin')->user()->hasAnyPermission($permissions);
//     }
// }


/** get user */
if (!function_exists('user')) {
    function user(): User | null
    {
        return Auth::guard('web')->user();
    }
}

if (!function_exists('tableHasColumns')) {
    function tableHasColumns(string $table, array $columns = []): bool
    {
        if (!Schema::hasTable($table)) {
            return false;
        }

        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }
}


/** get cart total */
if (!function_exists('cartCount')) {
    function cartCount(): int
    {
        if (!user()) {
            return count(Session::get('guest_cart', []));
        }

        if (!class_exists(Cart::class) || !tableHasColumns('carts', ['user_id'])) {
            return 0;
        }

        return Cart::where('user_id', user()?->id)->count();
    }
}
if (!function_exists('wishlistCount')) {
    function wishlistCount(): int
    {
        if (!user() || !class_exists(Wishlist::class) || !tableHasColumns('wishlists', ['user_id'])) {
            return 0;
        }

        return Wishlist::where('user_id', user()?->id)->count();
    }
}


/** get cart total */
if (!function_exists('cartTotal')) {
    function cartTotal(): float
    {
        if (!user()) {
            $cartTotal = 0;
            $cart = collect(Session::get('guest_cart', []));

            if ($cart->isEmpty() || !tableHasColumns('products', ['id'])) {
                return 0;
            }

            $products = Product::query()->whereIn('id', $cart->pluck('product_id')->filter()->unique())->get()->keyBy('id');

            foreach ($cart as $cartItem) {
                $product = $products->get($cartItem['product_id'] ?? null);

                if (!$product || !method_exists($product, 'getVariantOrProductPriceAndStock')) {
                    continue;
                }

                $priceData = $product->getVariantOrProductPriceAndStock($cartItem['variant_id'] ?? null);
                $cartTotal += $priceData['price'] * ($cartItem['quantity'] ?? 1);
            }

            return $cartTotal;
        }

        if (
            !class_exists(Cart::class) ||
            !tableHasColumns('products', ['id']) ||
            !tableHasColumns('carts', ['user_id', 'product_id', 'variant_id', 'quantity']) ||
            !method_exists(Cart::class, 'product')
        ) {
            return 0;
        }

        $cartTotal = 0;
        $cartItems = Cart::with('product')->where('user_id', user()->id)->get();

        foreach ($cartItems as $cartItem) {
            if (!$cartItem->product || !method_exists($cartItem->product, 'getVariantOrProductPriceAndStock')) {
                continue;
            }

            $priceData = $cartItem->product->getVariantOrProductPriceAndStock($cartItem->variant_id);

            if (!is_array($priceData) || !array_key_exists('price', $priceData)) {
                continue;
            }

            $cartTotal += $priceData['price'] * $cartItem->quantity;
        }

        return $cartTotal;
    }
}

/** get cart discount */
if (!function_exists('cartDiscount')) {
    function cartDiscount(): float
    {
        if (Session::has('coupon')) {
            $coupon =  Session::get('coupon');
            $cartTotal = cartTotal();
            $discount = $coupon['coupon_type'] == 'fixed' ? $coupon['coupon_value'] : $cartTotal * ($coupon['coupon_value'] / 100);
            $discount = min($discount, $cartTotal);

            return $discount;
        }
        return 0;
    }
}

/** get cart discount */
if (!function_exists('getPayableAmount')) {
    function getPayableAmount(): float
    {
        $cartTotal = cartTotal();
        $cartDiscount = cartDiscount();
        $shippingCharge = 0;
        if (Session::has('billing_info') && class_exists(ShippingRule::class) && tableHasColumns('shipping_rules', ['charge'])) {
            $shippingCharge = ShippingRule::find(Session::get('billing_info')['shipping_method_id'])?->charge ?? 0;
        }

        return round(($cartTotal + $shippingCharge) - $cartDiscount, 2);
    }
}
if (!function_exists('getShippingCharge')) {
    function getShippingCharge(): float
    {

        if (Session::has('billing_info') && class_exists(ShippingRule::class) && tableHasColumns('shipping_rules', ['charge'])) {
            return ShippingRule::find(Session::get('billing_info')['shipping_method_id'])?->charge ?? 0;
        }

        return 0;
    }
}

/** truncate text */
if (!function_exists('truncate')) {
    function truncate($text, int $length = 70): string|null
    {
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }
}

/** get nested categories */
if (!function_exists('getNestedCategories')) {
    function getNestedCategories()
    {
        if (!class_exists(Category::class) || !tableHasColumns('categories', ['name', 'slug', 'parent_id', 'position'])) {
            return collect();
        }

        $categories = Category::getNested();
        return $categories;
    }
}

if (!function_exists('ratingPercent')) {
    function ratingPercent($rating)
    {
        return $rating / 5 * 100;
    }
}

/** calculate file size from kb */
if (!function_exists('calculateFileSize')) {
    function calculateFileSize($bytes, $decimals = 2)
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . $units[$factor];
    }
}

/** set sidebar active */

if (!function_exists('setActive')) {
    function setActive(array $routes, $activeClass = 'active'): string
    {
        return request()->routeIs($routes) ? $activeClass : '';
    }
}


if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        if (!class_exists(Setting::class) || !tableHasColumns('settings', ['key', 'value'])) {
            return config("settings.$key", $default);
        }

        // Cache DB settings for 1 hour
        $settings = Cache::remember('site_settings', 3600, function () {
            return Setting::pluck('value', 'key')->all();
        });

        // Return DB value if exists, otherwise default from config or provided default
        return $settings[$key] ?? config("settings.$key", $default);
    }
}

// if (!function_exists('setting')) {
//     function setting($key, $default = null)
//     {
//         $settings = cache()->remember('settings', 3600, function () {
//             return \App\Models\Setting::pluck('value', 'key')->toArray();
//         });

//         return $settings[$key] ?? $default;
//     }
// }
