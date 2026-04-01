<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Admin\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Admin\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\BannerAdController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\ContactSectionSettingController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomPageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DatabaseClearController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\HeroBannerController;
use App\Http\Controllers\Admin\KycRequestController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\OfferSliderController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OurFeatureController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\Admin\PopularCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductSectionController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ShippingRuleController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Admin\WithdrawMethodController;
use App\Http\Controllers\Admin\WithdrawRequestController;
use App\Http\Controllers\SettingController;
use App\Models\ContactSectionSetting;
use App\Models\OfferSlider;
use App\Models\PopularCategory;
use App\Models\WithdrawMethod;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin')
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
            ->name('password.request');

        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
            ->name('password.email');

        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
            ->name('password.reset');

        Route::post('reset-password', [NewPasswordController::class, 'store'])
            ->name('password.store');
    });

Route::middleware('auth:admin','role:Admin|Super Admin')
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('verify-email', EmailVerificationPromptController::class)
            ->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
            ->name('password.confirm');

        Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');


        /** Profile Routes */
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [ProfileController::class, 'profileUpdate'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'passwordUpdate'])->name('password.update');

        /** Kyc routes */
        Route::get('/kyc-requests', [KycRequestController::class, 'index'])->name('kyc.index');
        Route::get('/kyc-requests/pending', [KycRequestController::class, 'pending'])->name('kyc.pending');
        Route::get('/kyc-requests/rejected', [KycRequestController::class, 'rejected'])->name('kyc.rejected');
        Route::get('/kyc-requests/{kyc_request}', [KycRequestController::class, 'show'])->name('kyc.show');
        Route::get('/kyc-requests/download/{kyc_request}', [KycRequestController::class, 'download'])->name('kyc.download');
        Route::put('/kyc-requests/{kyc_request}/update', [KycRequestController::class, 'update'])->name('kyc.update');


        /** Role Routes */
        Route::resource('/role', RoleController::class);
        Route::resource('/role-users', UserRoleController::class);

        /** Categories Routes */
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/nested', [CategoryController::class, 'getNestedCategories'])->name('categories.nested');
        Route::post('/categories/update-order', [CategoryController::class, 'updateOrder'])->name('categories.update-order');
        Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
        Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        /** Tags Routes */
        Route::resource('/tags', TagController::class);

        /** Brand Routes */
        Route::resource('/brands', BrandController::class);

        /** Product Routes */
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');

        Route::get('/products/{type}/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products/{type}/create', [ProductController::class, 'store'])->name('products.store');

        Route::get('/products/physical/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::post('/products/physical/{product}/update', [ProductController::class, 'update'])->name('products.update');
        Route::post('/products/images/upload/{product}', [ProductController::class, 'uploadImages'])->name('products.images.upload');
        Route::delete('/products/images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');
        Route::post('/products/images/reorder', [ProductController::class, 'imagesReorder'])->name('products.images.reorder');

        /** Product Attributes Routes */
        Route::post('/products/attributes/{product}/store', [ProductController::class, 'storeAttributes'])->name('products.attributes.store');
        Route::delete('/products/attributes/{product}/{attribute}', [ProductController::class, 'destroyAttribute'])->name('products.attributes.destroy');

        /** Product Variants Routes */
        Route::post('/products/variants/{product}/update', [ProductController::class, 'updateVariants'])->name('products.variants.update');

        /** Digital Product Routes */
        Route::get('/products/digital/{product}/edit', [ProductController::class, 'editDigitalProduct'])->name('digital-products.edit');
        Route::post('/products/digital/file-upload', [ProductController::class, 'uploadDigitalProductFile'])->name('digital-products.file.upload');
        Route::delete('/products/digital/{product}/{file}', [ProductController::class, 'destroyDigitalProductFile'])->name('digital-products.file.destroy');

        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');


        /** Coupons Routes */
        Route::resource('/coupons', CouponController::class);

        /**Shipping Routes */
        Route::resource('/shipping-rules', ShippingRuleController::class);

        /** Payment Setting Routes */
        Route::get('/payment-settings', [PaymentSettingController::class, 'index'])->name('payment-settings.index');
        Route::put('/paypal-settings', [PaymentSettingController::class, 'paypalSettings'])->name('paypal-settings.store');
        Route::get('/stripe-settings', [PaymentSettingController::class, 'stripe'])->name('stripe-settings.index');
        Route::put('/stripe-settings', [PaymentSettingController::class, 'stripeSettings'])->name('stripe-settings.store');
        Route::get('/razorpay-settings', [PaymentSettingController::class, 'razorpay'])->name('razorpay-settings.index');
        Route::put('/razorpay-settings', [PaymentSettingController::class, 'razorpaySettings'])->name('razorpay-settings.store');

        /** Order Routes */
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/update', [OrderController::class, 'update'])->name('orders.update');

        /** Withdraw Method Routes */
        Route::resource('withdraw-methods', WithdrawMethodController::class);
        Route::resource('withdraw-requests', WithdrawRequestController::class);


        /** Slider Routes */
        Route::resource('sliders', SliderController::class);
        Route::resource('hero-banners', HeroBannerController::class);
        Route::resource('popular-categories', PopularCategoryController::class);
        /** Flash Sale Routes */
        Route::get('get-products', [FlashSaleController::class, 'getProducts'])->name('flash-sales.get-products');
        Route::resource('flash-sales', FlashSaleController::class);

        /** Product Section Routes */
        Route::resource('product-sections', ProductSectionController::class);

        /** Reviews Routes */
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

        /** Newsletter Routes */
        Route::get('/subscribers', [NewsletterController::class, 'index'])->name('subscribers.index');
        Route::post('/newsletter', [NewsletterController::class, 'sendNewsletter'])->name('newsletter.send');

        /** Contact Routes */
        Route::resource('contact-settings', ContactSectionSettingController::class);
        Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::delete('/contact-messages/{contact_message}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

        /** Custom Page Routes */

        Route::resource('custom-pages', CustomPageController::class);

        /** Banner ad Routes */
        Route::get('/banners', [BannerAdController::class, 'index'])->name('banners.index');
        Route::post('/banners', [BannerAdController::class, 'store'])->name('banners.store');

        /** Our Features Routes */
        Route::resource('our-features', OurFeatureController::class);

        /** Social Link Routes */
        Route::resource('social-links', SocialLinkController::class);

        /** Social Link Routes */
        Route::resource('offer-sliders', OfferSliderController::class);

        /** Settings Routes */
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings/general-settings', [SettingController::class, 'generalSettings'])->name('settings.general');
        Route::get('/commission-settings', [SettingController::class, 'commissionSettingsIndex'])->name('commission-settings.index');
        Route::put('/commission-settings', [SettingController::class, 'commissionSettings'])->name('commission-settings.store');
        Route::get('/site-settings', [SettingController::class, 'siteSettingsIndex'])->name('site-settings.index');
        Route::put('/site-settings', [SettingController::class, 'siteSettings'])->name('site-settings.store');
        Route::get('/logo-settings', [SettingController::class, 'logoSettingsIndex'])->name('logo-settings.index');
        Route::put('/logo-settings', [SettingController::class, 'logoSettings'])->name('logo-settings.store');

        Route::get('/database-clear', [DatabaseClearController::class, 'index'])->name('database-clear.index');
        Route::post('/database-clear', [DatabaseClearController::class, 'clearDatabase'])->name('database-clear');
    });
