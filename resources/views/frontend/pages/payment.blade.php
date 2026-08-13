@extends('frontend.layouts.app')

@section('contents')
    <div class="container mb-60 mt-65">
        <div class="wsus__payment_area">
            <div class="row">
                <div class="col-12 col-xl-8 wow fadeInUp">
                    <h4>Select Payment </h4>
                    <div class="row mt-10">
                        @forelse ($paymentMethods as $paymentMethod)
                            <div class="col-6 col-md-4 col-lg-3 col-xl-3 wow fadeInUp">
                                <a href="{{ $paymentMethod['route'] }}" class="wsus__payment_method"
                                    aria-label="Pay with {{ $paymentMethod['name'] }}">
                                    <img src="{{ $paymentMethod['logo'] }}"
                                        alt="{{ $paymentMethod['name'] }}" class="img-fluid w-100">
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning mb-0" role="alert">
                                    No payment method is available right now. Please contact support or try again later.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="wsus__billing_summary">
                        <h4>Billing Summary</h4>
                        @foreach ($groupedCartItems as $key => $cartItems)
                            <h5 class="vendor_name">{{ $cartItems['store']->name }}</h5>
                            <ul class="wsus__billing_product">
                                @foreach ($cartItems['items'] as $cartItem)
                                    @php
                                        $price = $cartItem->product->getVariantOrProductPriceAndStock(
                                            $cartItem->variant_id,
                                        );
                                    @endphp
                                    <li>
                                        <a href="{{ route('products.show', $cartItem->product->slug) }}" class="img">
                                            <img src="{{ imageUrl($cartItem->product?->primaryImage?->path) }}" alt="product"
                                                class="img-fluid w-100">
                                        </a>
                                        <div class="text cart-item-title">
                                            <a style="font-size: 16px; font-weight: 700;"
                                                href="{{ route('products.show', $cartItem->product->slug) }}">{{ truncate($cartItem->product->name) }}</a>

                                            <span>{{ $cartItem->product?->variants()->where('id', $cartItem->variant_id)->first()->name ?? '' }}</span>
                                            <h6>${{ $price['price'] }} x {{ $cartItem->quantity }}</h6>
                                        </div>
                                    </li>
                                @endforeach

                            </ul>
                        @endforeach

                        <div class="wsus__total_price">
                            @php
                                $cartSubTotal = cartTotal();
                                $cartDiscount = cartDiscount();
                            @endphp

                            <h3>Sub Total <span>$ {{ $cartSubTotal }}</span></h3>
                            <p>Shipping Charge <span class="">$ <span
                                        class="shipping_charge">{{ $shippingCharge }}</span></span>
                            </p>
                            <p>Discount <span>$ {{ $cartDiscount }}</span></p>
                        </div>
                        <h5>Total <span>$ <span
                                    class="grand_total">{{ $cartSubTotal + $shippingCharge - $cartDiscount }}</span></span>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
