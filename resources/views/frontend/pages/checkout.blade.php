@extends('frontend.layouts.app')

@push('styles')
    <style>
        .checkout-address-card {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            height: 100%;
            min-height: 150px;
            padding: 18px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #fff;
            cursor: pointer;
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .checkout-address-card:hover,
        .checkout-address-card.is-selected {
            border-color: #ff8a00;
            background: #fffaf3;
            box-shadow: 0 5px 18px rgba(15, 23, 42, .07);
        }

        .checkout-address-card .form-check-input {
            flex: 0 0 auto;
            margin: 3px 0 0;
        }

        .checkout-address-card__content {
            min-width: 0;
            color: #475569;
            line-height: 1.55;
        }

        .checkout-address-card__title {
            display: block;
            margin-bottom: 5px;
            color: #172b4d;
            font-weight: 700;
        }

        .checkout-address-card__meta {
            display: block;
            margin-top: 8px;
            color: #64748b;
            font-size: 14px;
        }

        .checkout-address-card--new {
            align-items: center;
            border-style: dashed;
        }

        #checkout-address-form {
            scroll-margin-top: 24px;
        }
    </style>
@endpush

@section('contents')
    @php
        $showNewAddressForm = $addresses->isEmpty() || $errors->any();
        $createdAddressId = session('address_created');
    @endphp

    <div class="container mb-60 mt-60">
        <div class="row">
            <div class="col-lg-8 mb-40">
                <h1 class="heading-2 mb-10">Checkout</h1>
                <div class="d-flex justify-content-between">
                    <h6 class="text-body">There are <span class="text-brand">{{ cartCount() }}</span> products in your cart</h6>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8">

                <div class="wsus__shipping_address mb_40">
                    <h4>Billing Address</h4>

                    @if ($addresses->isEmpty())
                        <div class="alert alert-warning mt-20">You don't have a saved address. Complete the form below to continue.</div>
                    @endif


                    <p class="text-muted mb-3">Select a saved address or add a new one.</p>

                    <div class="row g-3">
                        @foreach ($addresses as $address)
                            @php
                                $selectedAddress = !$showNewAddressForm
                                    && ($createdAddressId
                                        ? $createdAddressId == $address->id
                                        : $address->is_default);
                            @endphp
                            <div class="col-md-6">
                                <label class="checkout-address-card {{ $selectedAddress ? 'is-selected' : '' }}"
                                    for="billing-{{ $address->id }}">
                                    <input class="form-check-input billing_address" type="radio"
                                        name="billing_address" id="billing-{{ $address->id }}"
                                        value="{{ $address->id }}" @checked($selectedAddress)>
                                    <span class="checkout-address-card__content">
                                        <span class="checkout-address-card__title">
                                            {{ $address->first_name }} {{ $address->last_name }}
                                            @if ($address->is_default)
                                                <span class="badge bg-success ms-1">Default</span>
                                            @endif
                                        </span>
                                        {{ $address->address }}, {{ $address->city }}, {{ $address->state }},
                                        {{ $address->zip }}, {{ $address->country }}
                                        <span class="checkout-address-card__meta">
                                            {{ $address->email }}<br>{{ $address->phone }}
                                        </span>
                                    </span>
                                </label>
                            </div>
                        @endforeach

                        <div class="col-md-6">
                            <label class="checkout-address-card checkout-address-card--new {{ $showNewAddressForm ? 'is-selected' : '' }}"
                                for="new-address-choice">
                                <input class="form-check-input new_address_choice" type="radio" name="address_choice"
                                    id="new-address-choice" value="new" @checked($showNewAddressForm)>
                                <span class="checkout-address-card__content">
                                    <span class="checkout-address-card__title">Add a new address</span>
                                    Enter a different billing or delivery address.
                                </span>
                            </label>
                        </div>
                    </div>

                    <div id="checkout-address-form" class="mt-4 {{ $showNewAddressForm ? '' : 'd-none' }}">
                        <div class="card border p-4">
                            <h4 class="mb-1">Enter a New Address</h4>
                            <p class="text-muted mb-4">Save this address and use it immediately for this order.</p>
                            @include('frontend.dashboard.address._form', ['returnTo' => 'checkout', 'address' => null])
                        </div>
                    </div>
                </div>

                <div class="row mt-30">
                    <form method="post">
                        <div class="ship_detail">
                            <div class="form-group">
                                <div class="chek-form">
                                    <div class="custome-checkbox">
                                        <input class="form-check-input ship_to_different_address" type="checkbox"
                                            name="checkbox" id="differentaddress">
                                        <label class="form-check-label label_info" data-bs-toggle="collapse"
                                            data-bs-target="#collapseAddress"
                                            aria-controls="collapseAddress" for="differentaddress"><span>Ship to a
                                                different address?</span></label>
                                    </div>
                                </div>
                            </div>
                            <div id="collapseAddress" class="different_address collapse">
                                <h4>Shipping Details</h4>
                                <div class="row mb-50">
                                    @foreach ($addresses as $address)
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <div class="wsus__shipping_address_item">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input shipping_address" type="radio"
                                                        name="shipping_address" id="shipping-{{ $address->id }}"
                                                        value="{{ $address->id }}">
                                                    <label class="form-check-label"
                                                        for="shipping-{{ $address->id }}">{{ $address->address }},
                                                        {{ $address->city }}, {{ $address->state }}, {{ $address->zip }},
                                                        {{ $address->country }}</label>
                                                </div>
                                                <div class="wsus__shipping_mail_address">
                                                    <a href="javascript:;">{{ $address->email }}</a>
                                                    <a href="javascript:;">{{ $address->phone }}</a>
                                                    @if ($address->is_default == 1)
                                                        <span class="text-success">(Default)</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="col-xl-4">
                <div class="wsus__billing_summary">
                    <h4>Billing Summery</h4>
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

                        <h4>Shipping Method</h4>
                        <div>
                            @foreach ($shippingMethods as $shippingMethod)
                                <div class="card mb-1">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input shipping_method" type="radio"
                                                name="shipping_method" id="{{ $shippingMethod->id }}"
                                                value="{{ $shippingMethod->id }}">
                                            <label class="form-check-label" for="{{ $shippingMethod->id }}">
                                                {{ $shippingMethod->name }} ( ${{ $shippingMethod->charge }} )
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <hr>
                        {{-- <form method="post" class="apply-coupon mb-10">
                                <input type="text" placeholder="Enter Coupon Code...">
                                <button class="btn  btn-md" name="login">Apply Coupon</button>
                            </form> --}}
                        {{-- <div class="show_coupon">
                                <p>Coupon code
                                    <span>#154HGJ</span>
                                    <a href="#"><i class="fi fi-rs-trash"></i></a>
                                </p>
                            </div> --}}
                        @php
                            $cartSubTotal = cartTotal();
                            $cartDiscount = cartDiscount();
                        @endphp
                        <h3>Sub Total <span>$ {{ $cartSubTotal }}</span></h3>
                        <p>Shipping Charge <span class="">$ <span class="shipping_charge">00.00</span></span></p>
                        <p>Discount <span>$ {{ $cartDiscount }}</span></p>
                    </div>
                    <h5>Sub Total <span>$ <span class="grand_total">{{ $cartSubTotal - $cartDiscount }}</span></span></h5>
                    <div class="my-4">
                        <button class="btn w-100 hover-up make-payment-button">Payment</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        $(function() {
            $('.shipping_method').prop('checked', false);
            $('.ship_to_different_address').prop('checked', false);

            function selectAddressCard(input) {
                $('.checkout-address-card').removeClass('is-selected');
                input.closest('.checkout-address-card').addClass('is-selected');
            }

            $('.billing_address').on('change', function() {
                $('.new_address_choice').prop('checked', false);
                $('#checkout-address-form').addClass('d-none');
                selectAddressCard($(this));
            });

            $('.new_address_choice').on('change', function() {
                $('.billing_address').prop('checked', false);
                $('#checkout-address-form').removeClass('d-none');
                selectAddressCard($(this));
                document.getElementById('checkout-address-form')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });

            $('.shipping_method').on('change', function() {
                let id = $(this).val();
                $.ajax({
                    url: "{{ route('checkout.shipping', ':id') }}".replace(':id', id),
                    method: "GET",
                    success: function(response) {
                        $('.shipping_charge').text(response.charge);
                        $('.grand_total').text(response.total);
                    }
                })
            })

            $('.make-payment-button').on('click', function() {

                let hasError = false;

                // check shipping method is selected
                if (!$('.shipping_method:checked').length > 0) {
                    notyf.error('Please select a shipping method');
                    hasError = true;
                }

                // check shipping address is selected
                if (!$('.billing_address:checked').length > 0) {
                    notyf.error('Please select a billing address');
                    hasError = true;
                }

                if ($('.ship_to_different_address').is(':checked') && (!$('.shipping_address:checked')
                        .length > 0)) {
                    notyf.error('Please select a shipping address');
                    hasError = true;
                }

                if (hasError) {
                    return;
                }


                $.ajax({
                    url: "{{ route('checkout.billinginfo.store') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        shipping_method_id: $('.shipping_method:checked').val(),
                        billing_address_id: $('.billing_address:checked').val(),
                        shipping_address_id: $('.ship_to_different_address').is(':checked') ? $(
                            '.shipping_address:checked').val() : null
                    },
                    beforeSend: function() {
                         $('.make-payment-button').html('<i class="fa fa-spinner fa-spin"></i>');
                    },
                    success: function(response) {
                        window.location.href = response.redirect_url;
                    },
                    error: function(response) {
                         $('.make-payment-button').html('Payment');
                    }

                })


            })
        });
    </script>
@endpush
