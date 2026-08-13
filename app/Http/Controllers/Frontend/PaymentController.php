<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ShippingRule;
use App\Models\Store;
use App\Services\AlertService;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Razorpay\Api\Api as RazorpayApi;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;
use Throwable;
use UnexpectedValueException;

class PaymentController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (cartTotal() == 0) {
            AlertService::error('Your cart is empty please add some products.');

            return redirect()->route('products.index');
        }

        $cartQuery = Cart::query()->with('product');

        if (Schema::hasTable('stores')) {
            $cartQuery->with('product.store');
        }

        $cartItems = $cartQuery
            ->where('user_id', user()->id)
            ->get()
            ->groupBy(function ($cartItem) {
                return data_get($cartItem, 'product.store.id', 'default');
            });

        $groupedCartItems = $cartItems->map(function ($items) {
            $store = data_get($items->first(), 'product.store') ?: Store::query()->make([
                'name' => 'ShopX',
            ]);

            return [
                'store' => $store,
                'items' => $items,
            ];
        });

        $shippingCharge = 0;

        if (
            Session::has('billing_info') &&
            class_exists(ShippingRule::class) &&
            Schema::hasTable('shipping_rules') &&
            filled(Session::get('billing_info')['shipping_method_id'] ?? null)
        ) {
            $shippingCharge = (float) (ShippingRule::query()
                ->availableFor(cartTotal())
                ->find(Session::get('billing_info')['shipping_method_id'])?->charge ?? 0);
        }

        $paymentMethods = collect([
            [
                'name' => 'PayPal',
                'route' => route('paypal.payment'),
                'logo' => asset('assets/frontend/dist/imgs/payment_4.png'),
                'enabled' => $this->gatewayIsConfigured('paypal'),
            ],
            [
                'name' => 'Stripe',
                'route' => route('stripe.payment'),
                'logo' => asset('assets/frontend/dist/imgs/payment_1.png'),
                'enabled' => $this->gatewayIsConfigured('stripe'),
            ],
            [
                'name' => 'Razorpay',
                'route' => route('razorpay.redirect'),
                'logo' => asset('assets/frontend/dist/imgs/payment_12.png'),
                'enabled' => $this->gatewayIsConfigured('razorpay'),
            ],
        ])->where('enabled', true)->values();

        return view('frontend.pages.payment', compact('groupedCartItems', 'shippingCharge', 'paymentMethods'));
    }

    public function paymentSuccess(): View
    {
        return view('frontend.pages.payment-success');
    }

    public function paymentCancel(): View
    {
        return view('frontend.pages.payment-cancel');
    }

    public function setPaypalConfig(): array
    {
        return [
            'mode' => setting('paypal_mode', 'sandbox'),
            'sandbox' => [
                'client_id' => setting('paypal_client_id'),
                'client_secret' => setting('paypal_secret'),
                'app_id' => 'APP-80W284485P519543T',
            ],
            'live' => [
                'client_id' => setting('paypal_client_id'),
                'client_secret' => setting('paypal_secret'),
                'app_id' => '',
            ],

            'payment_action' => 'Sale',
            'currency' => strtoupper(setting('paypal_currency', 'USD')),
            'notify_url' => '',
            'locale' => 'en_US',
            'validate_ssl' => true,
        ];
    }

    public function paypalPayment(): RedirectResponse
    {
        if (! $this->gatewayIsConfigured('paypal')) {
            AlertService::error('PayPal payment is not configured.');

            return redirect()->route('payment.index');
        }

        $payableAmount = round(getPayableAmount() * (float) setting('paypal_rate', 1), 2);

        $config = $this->setPaypalConfig();
        try {
            $provider = new PayPalClient($config);
            $provider->getAccessToken();
            $response = $provider->createOrder([
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url' => route('paypal.success'),
                    'cancel_url' => route('paypal.cancel'),
                ],
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => $config['currency'],
                            'value' => number_format($payableAmount, 2, '.', ''),
                        ],
                    ],
                ],
            ]);
        } catch (Throwable $exception) {
            report($exception);
            AlertService::error('PayPal could not start the payment. Please try again.');

            return redirect()->route('payment.index');
        }

        if (isset($response['id']) && $response['status'] == 'CREATED') {
            Session::put('paypal_order_id', $response['id']);

            foreach ($response['links'] as $link) {
                if ($link['rel'] == 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        }

        return redirect()->route('payment.cancel');
    }

    public function paypalSuccess(Request $request): RedirectResponse
    {
        if (! $this->gatewayIsConfigured('paypal') || ! filled($request->token) ||
            ! hash_equals((string) Session::get('paypal_order_id'), (string) $request->token)) {
            return redirect()->route('payment.cancel');
        }

        $config = $this->setPaypalConfig();
        try {
            $provider = new PayPalClient($config);
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request->token);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('payment.cancel');
        }

        if (($response['status'] ?? null) == 'COMPLETED') {
            $order = $response['purchase_units'][0]['payments']['captures'][0];
            $expectedAmount = round(getPayableAmount() * (float) setting('paypal_rate', 1), 2);

            if (strtoupper($order['amount']['currency_code']) !== $config['currency'] ||
                abs((float) $order['amount']['value'] - $expectedAmount) > 0.001) {
                return redirect()->route('payment.cancel');
            }

            OrderService::storeOrder(
                paymentId: $order['id'],
                paidAmount: $order['amount']['value'],
                paymentMethod: 'PayPal',
                currency: $order['amount']['currency_code'],
                currencyRate: (float) setting('paypal_rate', 1),
                paymentStatus: 'paid'
            );

            return redirect()->route('payment.success');
        }

        return redirect()->route('payment.cancel');
    }

    public function paypalCancel(): RedirectResponse
    {
        return redirect()->route('payment.cancel');
    }

    public function stripePayment(): RedirectResponse
    {
        if (! $this->gatewayIsConfigured('stripe')) {
            AlertService::error('Stripe payment is not configured.');

            return redirect()->route('payment.index');
        }

        $currencyRate = (float) setting('stripe_rate', 1);
        $currency = strtolower(setting('stripe_currency', 'USD'));
        $payableAmount = (int) round(getPayableAmount() * $currencyRate * 100);
        $orderId = null;

        try {
            $orderId = OrderService::createPendingStripeOrder($currency, $currencyRate);
            $stripe = new StripeClient(setting('stripe_secret'));
            $response = $stripe->checkout->sessions->create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $currency,
                            'product_data' => [
                                'name' => 'Product Purchase',
                            ],
                            'unit_amount' => $payableAmount,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'client_reference_id' => (string) auth('web')->id(),
                'customer_email' => auth('web')->user()->email,
                'metadata' => [
                    'order_id' => (string) $orderId,
                    'user_id' => (string) auth('web')->id(),
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'order_id' => (string) $orderId,
                    ],
                ],
                'success_url' => route('stripe.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel', ['order_id' => $orderId]),
            ]);
            OrderService::attachStripeSession($orderId, $response->id);
        } catch (Throwable $exception) {
            if ($orderId) {
                OrderService::discardPendingStripeOrder($orderId, auth('web')->id());
            }

            report($exception);
            AlertService::error('Stripe could not start the payment. Please try again.');

            return redirect()->route('payment.index');
        }

        return redirect()->away($response->url);
    }

    public function stripeSuccess(Request $request): RedirectResponse
    {
        abort_if(empty($request->session_id), 404);

        if (! $this->gatewayIsConfigured('stripe')) {
            return redirect()->route('payment.cancel');
        }

        try {
            $stripe = new StripeClient(setting('stripe_secret'));
            $response = $stripe->checkout->sessions->retrieve($request->session_id);
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('payment.cancel');
        }

        if (! hash_equals((string) auth('web')->id(), (string) $response->client_reference_id) ||
            ! OrderService::completeStripeOrder($response)) {
            return redirect()->route('payment.cancel');
        }

        Session::forget(['billing_info', 'coupon']);

        return redirect()->route('payment.success');
    }

    public function stripeWebhook(Request $request): Response
    {
        $webhookSecret = setting('stripe_webhook_secret');

        if (! filled($webhookSecret)) {
            return response('Stripe webhook is not configured.', 503);
        }

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                $webhookSecret,
            );
        } catch (UnexpectedValueException|SignatureVerificationException) {
            return response('Invalid Stripe webhook.', 400);
        }

        if (in_array($event->type, [
            'checkout.session.completed',
            'checkout.session.async_payment_succeeded',
        ], true) && $event->data->object->payment_status === 'paid' &&
            ! OrderService::completeStripeOrder($event->data->object)) {
            return response('Stripe order could not be fulfilled.', 422);
        }

        return response('Webhook received.');
    }

    public function stripeCancel(Request $request): RedirectResponse
    {
        if ($request->filled('order_id')) {
            OrderService::discardPendingStripeOrder($request->integer('order_id'), auth('web')->id());
        }

        return redirect()->route('payment.cancel');
    }

    public function razorpayRedirect(): View|RedirectResponse
    {
        if (! $this->gatewayIsConfigured('razorpay')) {
            AlertService::error('Razorpay payment is not configured.');

            return redirect()->route('payment.index');
        }

        $amount = (int) round(getPayableAmount() * (float) setting('razorpay_rate', 1) * 100);
        $currency = strtoupper(setting('razorpay_currency', 'INR'));

        try {
            $api = new RazorpayApi(setting('razorpay_client_id'), setting('razorpay_secret'));
            $order = $api->order->create([
                'receipt' => 'shopx_'.Str::uuid(),
                'amount' => $amount,
                'currency' => $currency,
            ]);
        } catch (Throwable $exception) {
            report($exception);
            AlertService::error('Razorpay could not start the payment. Please try again.');

            return redirect()->route('payment.index');
        }

        Session::put('razorpay_order', [
            'id' => $order->id,
            'amount' => $amount,
            'currency' => $currency,
        ]);

        return view('frontend.pages.razorpay', [
            'razorpayOrder' => $order,
            'key' => setting('razorpay_client_id'),
            'amount' => $amount,
            'currency' => $currency,
        ]);
    }

    public function razorpayPayment(Request $request): RedirectResponse
    {
        if (! $this->gatewayIsConfigured('razorpay')) {
            return redirect()->route('payment.cancel');
        }

        $validated = $request->validate([
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);
        $pendingOrder = Session::get('razorpay_order');

        if (! is_array($pendingOrder) ||
            ! hash_equals((string) ($pendingOrder['id'] ?? ''), $validated['razorpay_order_id'])) {
            return redirect()->route('payment.cancel');
        }

        try {
            $api = new RazorpayApi(setting('razorpay_client_id'), setting('razorpay_secret'));
            $api->utility->verifyPaymentSignature($validated);
            $response = $api->payment->fetch($validated['razorpay_payment_id']);

            if ($response->status === 'authorized') {
                $response = $response->capture(['amount' => $pendingOrder['amount']]);
            }

            if ($response->status === 'captured' &&
                $response->order_id === $pendingOrder['id'] &&
                (int) $response->amount === (int) $pendingOrder['amount'] &&
                strtoupper($response->currency) === $pendingOrder['currency']) {
                OrderService::storeOrder(
                    paymentId: $response->id,
                    paidAmount: $response->amount / 100,
                    paymentMethod: 'Razorpay',
                    currency: $response->currency,
                    currencyRate: (float) setting('razorpay_rate', 1),
                    paymentStatus: 'paid'
                );

                return redirect()->route('payment.success');
            }
        } catch (Throwable $exception) {
            report($exception);
        }

        return redirect()->route('payment.cancel');
    }

    private function gatewayIsConfigured(string $gateway): bool
    {
        $sdkIsAvailable = match ($gateway) {
            'paypal' => class_exists(PayPalClient::class),
            'stripe' => class_exists(StripeClient::class) && class_exists(Webhook::class),
            'razorpay' => class_exists(RazorpayApi::class),
            default => false,
        };

        $credentialsArePresent = $gateway === 'stripe'
            ? filled(setting('stripe_key')) && filled(setting('stripe_secret'))
            : filled(setting("{$gateway}_client_id")) && filled(setting("{$gateway}_secret"));

        return $sdkIsAvailable &&
            setting("{$gateway}_status", 'inactive') === 'active' &&
            $credentialsArePresent &&
            (float) setting("{$gateway}_rate", 0) > 0;
    }
}
