@extends('frontend.layouts.app')

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
                <img src="{{ asset(data_get($ads, 'side_banner_two.0.image', '')) }}" alt="cta" class="img-fluid w-100" />
            </a>
        </div>
    </section>

    <!-- special products end -->
    @include('frontend.home.sections.four-col-products-section')
    <!--End 4 columns-->
@endsection
