        <section class="four_column_banner mt-15 mb-30">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="banner-img style-6 wow animate__animated animate__fadeInUp" data-wow-delay="0">
                            
                            <img src="{{ asset(data_get($ads, 'banner_four.0.image', '')) }}" alt="" />
                            <div class="banner-text">
                                <h6 class="mb-10 mt-30">{{ data_get($ads, 'banner_four.0.title', '') }}</h6>
                                <a href="{{ data_get($ads, 'banner_four.0.url', '') }}">View All</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="banner-img style-6 wow animate__animated animate__fadeInUp" data-wow-delay="0.2s">
                            <img src="{{ asset(data_get($ads, 'banner_five.0.image', '')) }}" alt="" />
                            <div class="banner-text">
                                <h6 class="mb-10 mt-30">{{ data_get($ads, 'banner_five.0.title', '') }}</h6>
                                <a href="{{ data_get($ads, 'banner_five.0.url', '') }}">View All</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="banner-img style-6 wow animate__animated animate__fadeInUp" data-wow-delay="0.4s">
                            <img src="{{ asset(data_get($ads, 'banner_six.0.image', '')) }}" alt="" />
                            <div class="banner-text">
                                <h6 class="mb-10 mt-30">{{ data_get($ads, 'banner_six.0.title', '') }}</h6>
                                <a href="{{ data_get($ads, 'banner_six.0.url', '') }}">View All</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="banner-img style-6 wow animate__animated animate__fadeInUp" data-wow-delay="0.6s">
                            <img src="{{ asset(data_get($ads, 'banner_seven.0.image', '')) }}" alt="" />
                            <div class="banner-text">
                                <h6 class="mb-10 mt-30">{{ data_get($ads, 'banner_seven.0.title', '') }}</h6>
                                <a href="{{ data_get($ads, 'banner_seven.0.url', '') }}">View All</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
