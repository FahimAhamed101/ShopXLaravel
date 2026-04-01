<section class="banners mt-25">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="banner-img wow animate__animated animate__fadeInUp" data-wow-delay="0">
                    <img src="{{ asset(data_get($ads, 'banner_one.0.image', '')) }}" alt="" />
                    <div class="banner-text">
                        <h4>
                            {{ data_get($ads, 'banner_one.0.title', '') }}
                        </h4>
                        <a href="{{ data_get($ads, 'banner_one.0.url', '') }}" class="btn btn-xs">Shop Now <i
                                class="fi-rs-arrow-small-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="banner-img wow animate__animated animate__fadeInUp" data-wow-delay=".2s">
                    <img src="{{ asset(data_get($ads, 'banner_two.0.image', '')) }}" alt="" />
                    <div class="banner-text">
                        <h4>
                            {{ data_get($ads, 'banner_two.0.title', '') }}
                        </h4>
                        <a href="{{ data_get($ads, 'banner_two.0.url', '') }}" class="btn btn-xs">Shop Now <i
                                class="fi-rs-arrow-small-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 d-md-none d-lg-flex">
                <div class="banner-img mb-sm-0 wow animate__animated animate__fadeInUp" data-wow-delay=".4s">
                    <img src="{{ asset(data_get($ads, 'banner_three.0.image', '')) }}" alt="" />
                    <div class="banner-text">
                        <h4>{{ data_get($ads, 'banner_three.0.title', '') }}</h4>
                        <a href="{{ data_get($ads, 'banner_three.0.url', '') }}" class="btn btn-xs">Shop Now <i
                                class="fi-rs-arrow-small-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
