<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AlertService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentSettingController extends Controller
{
    public function index(): View
    {
        return view('admin.payment-setting.sections.paypal-settings');
    }

    public function stripe(): View
    {
        return view('admin.payment-setting.sections.stripe-settings');
    }

    public function razorpay(): View
    {
        return view('admin.payment-setting.sections.razorpay-settings');
    }

    public function paypalSettings(Request $request): RedirectResponse
    {
        $settings = $request->validate([
            'paypal_status' => ['required', 'in:active,inactive'],
            'paypal_mode' => ['required', 'in:sandbox,live'],
            'paypal_currency' => ['required', 'string', 'size:3'],
            'paypal_rate' => ['required', 'numeric', 'gt:0'],
            'paypal_client_id' => ['nullable', Rule::requiredIf(fn () => $request->paypal_status === 'active' && ! setting('paypal_client_id')), 'string'],
            'paypal_secret' => ['nullable', Rule::requiredIf(fn () => $request->paypal_status === 'active' && ! setting('paypal_secret')), 'string'],
        ]);

        return $this->save($settings);
    }

    public function stripeSettings(Request $request): RedirectResponse
    {
        $modePrefix = $request->stripe_mode === 'live' ? 'live' : 'test';
        $settings = $request->validate([
            'stripe_status' => ['required', 'in:active,inactive'],
            'stripe_mode' => ['required', 'in:sandbox,live'],
            'stripe_currency' => ['required', 'string', 'size:3'],
            'stripe_rate' => ['required', 'numeric', 'gt:0'],
            'stripe_key' => [
                'nullable',
                Rule::requiredIf(fn () => $request->stripe_status === 'active' && ! setting('stripe_key')),
                'string',
                "regex:/^pk_{$modePrefix}_[A-Za-z0-9]+$/",
            ],
            'stripe_secret' => [
                'nullable',
                Rule::requiredIf(fn () => $request->stripe_status === 'active' && ! setting('stripe_secret')),
                'string',
                "regex:/^(sk|rk)_{$modePrefix}_[A-Za-z0-9]+$/",
            ],
            'stripe_webhook_secret' => [
                'nullable',
                'string',
                'regex:/^whsec_[A-Za-z0-9]+$/',
            ],
        ]);

        return $this->save($settings);
    }

    public function razorpaySettings(Request $request): RedirectResponse
    {
        $settings = $request->validate([
            'razorpay_status' => ['required', 'in:active,inactive'],
            'razorpay_currency' => ['required', 'string', 'size:3'],
            'razorpay_rate' => ['required', 'numeric', 'gt:0'],
            'razorpay_client_id' => ['nullable', Rule::requiredIf(fn () => $request->razorpay_status === 'active' && ! setting('razorpay_client_id')), 'string'],
            'razorpay_secret' => ['nullable', Rule::requiredIf(fn () => $request->razorpay_status === 'active' && ! setting('razorpay_secret')), 'string'],
        ]);

        return $this->save($settings);
    }

    private function save(array $settings): RedirectResponse
    {
        foreach ($settings as $key => $value) {
            if ($value === null) {
                continue;
            }

            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('site_settings');
        AlertService::updated('Payment settings updated successfully.');

        return back();
    }
}
