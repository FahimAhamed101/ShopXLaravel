<?php

use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\Frontend\AddressController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\KycController;
use App\Http\Controllers\Frontend\NewsLetterController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\PaymentController;
use App\Http\Controllers\Frontend\ProductPageController;
use App\Http\Controllers\Frontend\UserDashboardController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\StoreController;
use App\Http\Controllers\Frontend\StoreWithdrawMethodController;
use App\Http\Controllers\Frontend\StoreWithdrawRequestController;
use App\Http\Controllers\Frontend\UserOrderController;
use App\Http\Controllers\Frontend\UserPurchasedProductsController;
use App\Http\Controllers\Frontend\UserTrackOrderController;
use App\Http\Controllers\Frontend\VendorDahboardController;
use App\Http\Controllers\Frontend\VendorDashboardController;
use App\Http\Controllers\Frontend\VendorPageController;
use App\Http\Controllers\Frontend\VendorProductController;
use App\Http\Controllers\Frontend\VendorProfileController;
use App\Http\Controllers\Frontend\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home.index');


/** Products routes */
Route::get('/products', [ProductPageController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductPageController::class, 'show'])->name('products.show');


/** Contact Routes */
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

/** Custom Page Route */
Route::get('/page/{slug}', [HomeController::class, 'customPage'])->name('custom-page.index');

/** Vendor Routes */
Route::get('vendors', [VendorPageController::class, 'index'])->name('vendors.index');
Route::get('vendors/{vendor}', [VendorPageController::class, 'show'])->name('vendors.show');

/** Flash Sale Route */
Route::get('/flash-sales', [HomeController::class, 'flashSales'])->name('flash-sales.index');




Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    /** Profile Routes */
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'profileUpdate'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'passwordUpdate'])->name('password.update');

    Route::resource('/address', AddressController::class);

    Route::get('/orders', [UserOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [UserOrderController::class, 'show'])->name('orders.show');

    /** KYC Routes */
    Route::get('/kyc-verification', [KycController::class, 'index'])->name('kyc.index');
    Route::post('/kyc-verification', [KycController::class, 'store'])->name('kyc.store');

    /** Cart Routes */
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('cart.add');
    Route::put('/update-cart', [CartController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
    Route::delete('/cart/coupon/remove', [CartController::class, 'destroyCoupon'])->name('cart.coupon.destroy');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/shipping-method/{id}', [CheckoutController::class, 'shippingMethod'])->name('checkout.shipping');
    Route::post('/billing-info', [CheckoutController::class, 'billingInfo'])->name('checkout.billinginfo.store');

    /** Review Route */
    Route::get('/reviews', [UserDashboardController::class, 'reviews'])->name('reviews.index');
    Route::post('/product/review/{product}', [HomeController::class, 'storeReview'])->name('product.review.store');

    /** Wishlist Routes */
    Route::resource('/wishlist', WishlistController::class);

    /** Newsletter Routes */
    Route::post('/newsletter/subscribe', [NewsLetterController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');

    /** User purchased products */
    Route::get('/purchased-products', [UserPurchasedProductsController::class, 'index'])->name('purchased.products');
    Route::get('/purchased-products/{id}', [UserPurchasedProductsController::class, 'show'])->name('purchased.products.show');
    Route::get('/download-product/{product}/{file}', [UserPurchasedProductsController::class, 'download'])->name('purchased.products.download');

    /** Track order */
    Route::get('/track-order', [UserTrackOrderController::class, 'index'])->name('track.order.index');

    /** Payment Routes */
    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');

    Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');


    /** Paypal Routes */
    Route::get('/paypal/payment', [PaymentController::class, 'paypalPayment'])->name('paypal.payment');
    Route::get('/paypal/success', [PaymentController::class, 'paypalSuccess'])->name('paypal.success');
    Route::get('/paypal/cancel', [PaymentController::class, 'paypalCancel'])->name('paypal.cancel');

    /** Stripe Routes */
    Route::get('/stripe/payment', [PaymentController::class, 'stripePayment'])->name('stripe.payment');
    Route::get('/stripe/success', [PaymentController::class, 'stripeSuccess'])->name('stripe.success');
    Route::get('/stripe/cancel', [PaymentController::class, 'stripeCancel'])->name('stripe.cancel');

    /** Stripe Routes */
    Route::get('/razorpay/redirect', [PaymentController::class, 'razorpayRedirect'])->name('razorpay.redirect');
    Route::post('/razorpay/payment', [PaymentController::class, 'razorpayPayment'])->name('razorpay.payment');
});


/** Vendor Routes */

Route::group(['prefix' => 'vendor', 'as' => 'vendor.', 'middleware' => ['auth', 'verified', 'role:vendor']], function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');

    /** Profile Routes */
    Route::get('/profile', [VendorProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [VendorProfileController::class, 'update'])->name('profile.update');

    Route::put('/profile-password', [VendorProfileController::class, 'updatePassword'])->name('profile-password.update');

    /** Shop Profile Routes */
    Route::resource('store-profile', StoreController::class);

    /** Product Routes */
    Route::get('/products', [VendorProductController::class, 'index'])->name('products.index');

    Route::get('/products/{type}/create', [VendorProductController::class, 'create'])->name('products.create');
    Route::post('/products/{type}/create', [VendorProductController::class, 'store'])->name('products.store');

    Route::get('/products/physical/{product}/edit', [VendorProductController::class, 'edit'])->name('products.edit');
    Route::post('/products/physical/{product}/update', [VendorProductController::class, 'update'])->name('products.update');
    Route::post('/products/images/upload/{product}', [VendorProductController::class, 'uploadImages'])->name('products.images.upload');
    Route::delete('/products/images/{image}', [VendorProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::post('/products/images/reorder', [VendorProductController::class, 'imagesReorder'])->name('products.images.reorder');

    /** Product Attributes Routes */
    Route::post('/products/attributes/{product}/store', [VendorProductController::class, 'storeAttributes'])->name('products.attributes.store');
    Route::delete('/products/attributes/{product}/{attribute}', [VendorProductController::class, 'destroyAttribute'])->name('products.attributes.destroy');

    /** Product Variants Routes */
    Route::post('/products/variants/{product}/update', [VendorProductController::class, 'updateVariants'])->name('products.variants.update');

    /** Digital Product Routes */
    Route::get('/products/digital/{product}/edit', [VendorProductController::class, 'editDigitalProduct'])->name('digital-products.edit');
    Route::post('/products/digital/file-upload', [VendorProductController::class, 'uploadDigitalProductFile'])->name('digital-products.file.upload');
    Route::delete('/products/digital/{product}/{file}', [VendorProductController::class, 'destroyDigitalProductFile'])->name('digital-products.file.destroy');

    Route::delete('/products/{product}', [VendorProductController::class, 'destroy'])->name('products.destroy');


    /** Order Routes */
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/update', [OrderController::class, 'update'])->name('orders.update');

    /** payout method routes */
    Route::resource('withdraw-methods', StoreWithdrawMethodController::class);
    Route::get('withdraw-requests', [StoreWithdrawRequestController::class, 'index'])->name('withdraw-requests.index');
    Route::get('withdraw-requests/create', [StoreWithdrawRequestController::class, 'create'])->name('withdraw-requests.create');
    Route::post('withdraw-requests', [StoreWithdrawRequestController::class, 'store'])->name('withdraw-requests.store');
    Route::delete('withdraw-requests/{withdraw_request}', [StoreWithdrawRequestController::class, 'destroy'])->name('withdraw-requests.destroy');
});




require __DIR__ . '/auth.php';
