<section class="home-slider position-relative mb-30">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 d-none d-lg-block">
                <div class="categories-dropdown-wrap style-2 font-heading mt-30">
                    <div class="d-flex categori-dropdown-inner">
                        <ul>
                            {{-- @dd(getNestedCategories()) --}}

                            @foreach (getNestedCategories() as $category)
                                @if ($loop->iteration <= 11)
                                    <li>
                                        <a href="{{ route('products.index', ['category' => $category->slug]) }}">
                                            <img src="{{ asset($category->icon) }}" alt="" />
                                            <span>{{ $category->name }}</span>
                                        </a>
                                        @if (count($category->children_nested) > 0)
                                            <ul>
                                                @foreach ($category->children_nested as $child)
                                                    <li
                                                        class="{{ count($child->children_nested) > 0 ? '' : 'no_child' }}">
                                                        <a
                                                            href="{{ route('products.index', ['category' => $child->slug]) }}">{{ $child->name }}</a>
                                                        @if (count($child->children_nested) > 0)
                                                            <ul>
                                                                @foreach ($child->children_nested as $subchild)
                                                                    <li class="no_child">
                                                                        <a
                                                                            href="{{ route('products.index', ['category' => $subchild->slug]) }}">{{ $subchild->name }}</a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <a href="{{ route('products.index') }}" class="more_categories">
                        view all
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-8 col-xl-9 col-xxl-7">
                <div class="home-slide-cover mt-30">
                    <div class="hero-slider-1 style-5 dot-style-1 dot-style-1-position-2">
                        @foreach ($sliders as $slider)
                            <div class="single-hero-slider single-animation-wrap"
                                style="background-image: url({{ asset($slider->image) }})">
                                <div class="slider-content">
                                    <h1 class="display-2 mb-15">{{ $slider->title }}</h1>
                                    <p>{{ $slider->sub_title }}</p>
                                    <a href="{{ $slider->btn_url }}" class="btn">Shop Now <i
                                            class="fi-rs-arrow-small-right"></i></a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="slider-arrow hero-slider-1-arrow"></div>
                </div>
            </div>
            <div class="col-lg-4 col-xl-3 col-xxl-3">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-12">
                        <div class="banner-img style-4 mt-30">
                            <img src="{{ asset($heroBanner?->banner_one) }}" alt="" />
                            <div class="banner-text">
                                <h4 class="mb-30">{{ $heroBanner?->title_one }}</h4>
                                <a href="{{ $heroBanner?->btn_url_one }}" class="btn btn-xs mb-50">Shop Now <i
                                        class="fi-rs-arrow-small-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-12">
                        <div class="banner-img style-5 mt-5 mt-md-30">
                            <img src="{{ asset($heroBanner?->banner_two) }}" alt="" />
                            <div class="banner-text">
                                <h5 class="mb-20">{{ $heroBanner?->title_two }}</h5>
                                <a href="{{ $heroBanner?->btn_url_two }}" class="btn btn-xs">Shop Now <i
                                        class="fi-rs-arrow-small-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
