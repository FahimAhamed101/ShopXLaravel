@extends('frontend.layouts.app')

@section('contents')
    <div class="container mb-30" style="transform: none;">
        <div class="archive-header-3 mt-70 mb-70" style="background-image: url({{ asset($store->banner) }})">
            <div class="archive-header-3-inner">
                <div class="vendor-logo mr-50">
                    <img src="{{ asset($store->logo) }}" alt="">
                </div>
                <div class="vendor-content">
                    <div class="product-category">
                        <span class="text-muted">Since {{ date('Y', strtotime($store->created_at)) }}</span>
                    </div>
                    <h3 class="mb-5 text-white"><a href="vendor-details-1.html" class="text-white">{{ $store->name }}</a>
                    </h3>
                    <div class="product-rate-cover mb-15">
                        @php
                            $ratingPercent = $store->reviews_avg_rating ? ($store->reviews_avg_rating / 5) * 100 : 0;
                        @endphp

                        <div class="product-rate d-inline-block">
                            <div class="product-rating" style="width: {{ $ratingPercent }}%"></div>
                        </div>
                        <span class="font-small ml-5 text-muted"> ({{ round($store->reviews_avg_rating, 2) }})</span>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="vendor-des mb-15">
                                <p class="font-sm text-white">{{ $store->short_description }}</p>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="vendor-info text-white mb-15">
                                <ul class="font-sm">
                                    <li><img class="mr-5"
                                            src="{{ asset('assets/frontend/dist/imgs/theme/icons/icon-location.svg') }}"
                                            alt=""><strong>Address: </strong> <span>{{ $store->address }}</span>
                                    </li>
                                    <li><img class="mr-5"
                                            src="{{ asset('assets/frontend/dist/imgs/theme/icons/icon-contact.svg') }}"
                                            alt=""><strong>Call Us:</strong><span> {{ $store->phone }}</span>
                                    </li>
                                    <li><img class="mr-5"
                                            src="{{ asset('assets/frontend/dist/imgs/theme/icons/icon-contact.svg') }}"
                                            alt=""><strong>Email:</strong><span> {{ $store->email }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        {{-- <div class="col-lg-4">
                            <div class="follow-social">
                                <h6 class="mb-15 text-white">Follow Us</h6>
                                <ul class="social-network">
                                    <li class="hover-up">
                                        <a href="#">
                                            <img src="assets/imgs/theme/icons/social-tw.svg" alt="">
                                        </a>
                                    </li>
                                    <li class="hover-up">
                                        <a href="#">
                                            <img src="assets/imgs/theme/icons/social-fb.svg" alt="">
                                        </a>
                                    </li>
                                    <li class="hover-up">
                                        <a href="#">
                                            <img src="assets/imgs/theme/icons/social-insta.svg" alt="">
                                        </a>
                                    </li>
                                    <li class="hover-up">
                                        <a href="#">
                                            <img src="assets/imgs/theme/icons/social-pin.svg" alt="">
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-60" style="transform: none;">

            @foreach($store->products as $product)
                <x-frontend.product-card :product="$product" class="col-6 col-lg-4 col-xl-3 col-xxl-2" />
            @endforeach

        </div>
    </div>
@endsection
