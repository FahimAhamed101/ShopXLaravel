@extends('frontend.layouts.app')

@push('styles')
    <style>
        .shopx-hero-grid {
            display: grid;
            grid-template-columns: 243px minmax(0, 1fr) 255px;
            gap: 20px;
            align-items: stretch;
            margin-top: 30px;
        }

        .shopx-hero-grid .categories-dropdown-wrap.style-2 {
            width: 100%;
            height: 360px;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .shopx-hero-grid .categories-dropdown-wrap.style-2 .categori-dropdown-inner {
            flex: 1;
            overflow: hidden;
        }

        .shopx-hero-grid .categories-dropdown-wrap.style-2>div>ul {
            padding-top: 12px;
        }

        .shopx-hero-grid .categories-dropdown-wrap.style-2 ul li {
            height: 22px;
            line-height: 22px;
            margin-bottom: 10px;
            padding: 0 20px;
        }

        .shopx-hero-grid .categories-dropdown-wrap.style-2 ul li a {
            font-size: 13px;
        }

        .shopx-hero-grid .categories-dropdown-wrap.style-2 ul li img {
            width: 18px;
            height: 18px;
            object-fit: contain;
        }

        .shopx-hero-grid .categories-dropdown-wrap.style-2 .more_categories {
            padding: 9px 20px;
            font-size: 14px;
        }

        .shopx-hero-grid .home-slide-cover,
        .shopx-hero-grid .hero-slider-1,
        .shopx-hero-grid .hero-slider-1 .slick-list,
        .shopx-hero-grid .hero-slider-1 .slick-track,
        .shopx-hero-grid .hero-slider-1 .single-hero-slider {
            height: 360px;
        }

        .shopx-hero-grid .hero-slider-1 .single-hero-slider {
            border-radius: 0;
            background-size: cover;
            background-position: center;
        }

        .shopx-hero-grid .hero-slider-1 .slider-content {
            left: 6%;
            max-width: 48%;
        }

        .shopx-hero-grid .hero-slider-1 .slider-content .display-2 {
            font-size: 34px;
            line-height: 1.05;
            margin-bottom: 12px !important;
        }

        .shopx-hero-grid .hero-slider-1 .slider-content p {
            font-size: 14px;
            line-height: 1.45;
            margin-bottom: 18px;
        }

        .shopx-hero-banners {
            grid-template-rows: 1fr 1fr;
            gap: 20px;
        }

        .shopx-hero-banners>div {
            display: grid;
            grid-template-rows: 1fr 1fr;
            gap: 20px;
        }

        .shopx-hero-banners .banner-img {
            height: 170px;
            border-radius: 0;
            background: #f8f8f8;
        }

        .shopx-hero-banners .banner-img .banner-text {
            top: 28px;
            transform: none;
            padding: 0 18px;
        }

        .shopx-hero-banners .banner-img .banner-text h4,
        .shopx-hero-banners .banner-img .banner-text h5 {
            min-height: 0;
            max-width: 145px;
            margin-bottom: 18px !important;
            font-size: 18px;
            line-height: 1.2;
        }

        .shopx-hero-banners .banner-img .btn,
        .shopx-hero-grid .hero-slider-1 .btn {
            border-radius: 0;
            padding: 8px 14px;
            font-size: 12px;
        }

        @media (max-width: 1199.98px) {
            .shopx-hero-grid {
                grid-template-columns: 243px minmax(0, 1fr);
            }
        }

        @media (max-width: 991.98px) {
            .shopx-hero-grid {
                display: block;
                margin-top: 16px;
            }

            .shopx-hero-grid .home-slide-cover,
            .shopx-hero-grid .hero-slider-1,
            .shopx-hero-grid .hero-slider-1 .slick-list,
            .shopx-hero-grid .hero-slider-1 .slick-track,
            .shopx-hero-grid .hero-slider-1 .single-hero-slider {
                height: 320px;
            }

            .shopx-hero-grid .hero-slider-1 .slider-content {
                max-width: 62%;
            }
        }

        @media (max-width: 575.98px) {
            .shopx-hero-grid .hero-slider-1 .slider-content .display-2 {
                font-size: 26px;
            }

            .shopx-hero-grid .hero-slider-1 .slider-content {
                max-width: 76%;
            }
        }
    </style>
@endpush

@section('contents')
    @include('frontend.home.sections.hero-section')
    <!--End hero slider-->
    @include('frontend.home.sections.category-section')
    <!--End category slider-->
    @include('frontend.home.sections.banner-section')
    <!--End banners-->
    @include('frontend.home.sections.products-tab-section')
    <!--Products Tabs-->
    @include('frontend.home.sections.banner-section-two')
    <!--End 4 banners-->
    @include('frontend.home.sections.flash-sale-section')
    <!--End Best Sales-->
    @include('frontend.home.sections.new-arrival-section')
    <!-- new arrival end -->
    <section class="wsus__ctg mt-40">
        <div class="container">
            <a href="{{ data_get($ads, 'side_banner_two.0.url', '') }}" class="wsus__ctg_area">
                <img src="{{ imageUrl(data_get($ads, 'side_banner_two.0.image'), 'assets/frontend/dist/imgs/banner/banner-10.png') }}" alt="cta" class="img-fluid w-100" />
            </a>
        </div>
    </section>

    <!-- special products end -->
    @include('frontend.home.sections.four-col-products-section')
    <!--End 4 columns-->
@endsection
