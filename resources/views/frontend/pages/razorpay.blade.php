@extends('frontend.layouts.app')

@section('contents')
    <div class="container mb-60 mt-65 text-center">
        <div class="alert alert-info" role="status">
            Opening Razorpay secure checkout…
        </div>
        <a href="{{ route('payment.index') }}" class="btn">Choose another payment method</a>

        <form id="razorpay-payment-form" action="{{ route('razorpay.payment') }}" method="POST">
            @csrf
            <input type="hidden" name="razorpay_payment_id">
            <input type="hidden" name="razorpay_order_id">
            <input type="hidden" name="razorpay_signature">
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('razorpay-payment-form');
            const checkout = new Razorpay({
                key: @json($key),
                amount: @json($amount),
                currency: @json($currency),
                name: @json(config('app.name', 'ShopX')),
                description: 'Order payment',
                order_id: @json($razorpayOrder->id),
                prefill: {
                    name: @json(auth('web')->user()->name),
                    email: @json(auth('web')->user()->email),
                },
                handler: function (response) {
                    form.elements.razorpay_payment_id.value = response.razorpay_payment_id;
                    form.elements.razorpay_order_id.value = response.razorpay_order_id;
                    form.elements.razorpay_signature.value = response.razorpay_signature;
                    form.submit();
                },
                modal: {
                    ondismiss: function () {
                        window.location.href = @json(route('payment.index'));
                    }
                }
            });

            checkout.on('payment.failed', function () {
                window.location.href = @json(route('payment.cancel'));
            });
            checkout.open();
        });
    </script>
@endpush
