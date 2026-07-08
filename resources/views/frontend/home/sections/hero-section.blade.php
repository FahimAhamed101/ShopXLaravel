<section class="home-slider position-relative mb-30">
    <div class="container">
        <div class="shopx-hero-grid">
            <aside class="shopx-hero-categories d-none d-lg-block">
                <div class="categories-dropdown-wrap style-2 font-heading">
                    <div class="d-flex categori-dropdown-inner">
                        <ul>
                            {{-- @dd(getNestedCategories()) --}}

                            @foreach (getNestedCategories() as $category)
                                @if ($loop->iteration <= 11)
                                    <li>
                                        <a href="{{ route('products.index', ['category' => $category->slug]) }}">
                                            <img src="{{ imageUrl($category->icon, 'assets/frontend/dist/imgs/theme/icons/category-1.svg') }}" alt="" />
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
            </aside>
            <div class="shopx-hero-slider">
                <div class="home-slide-cover">
                    <div class="hero-slider-1 style-5 dot-style-1 dot-style-1-position-2">
                        @forelse ($sliders as $slider)
                            <div class="single-hero-slider single-animation-wrap"
                                style="background-image: url({{ imageUrl($slider->image, 'assets/frontend/dist/imgs/slider/slider-1.png') }})">
                                <div class="slider-content">
                                    <h1 class="display-2 mb-15">{{ $slider->title ?: 'Smartwatch with Heart Rate Monitor' }}</h1>
                                    <p>{{ $slider->sub_title ?: 'Track your fitness and monitor your health with this stylish smartwatch.' }}</p>
                                    <a href="{{ $slider->btn_url ?: route('products.index') }}" class="btn">Shop Now <i
                                            class="fi-rs-arrow-small-right"></i></a>
                                </div>
                            </div>
                        @empty
                            <div class="single-hero-slider single-animation-wrap"
                                style="background-image: url({{ imageUrl(null, 'assets/frontend/dist/imgs/slider/slider-1.png') }})">
                                <div class="slider-content">
                                    <h1 class="display-2 mb-15">Smartwatch with Heart Rate Monitor</h1>
                                    <p>Track your fitness and monitor your health with this stylish smartwatch.</p>
                                    <a href="{{ route('products.index') }}" class="btn">Shop Now <i
                                            class="fi-rs-arrow-small-right"></i></a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="slider-arrow hero-slider-1-arrow"></div>
                </div>
            </div>
            <aside class="shopx-hero-banners d-none d-xl-grid">
                <div>
                    <div>
                        <div class="banner-img style-4">
                            <img src="{{ imageUrl($heroBanner?->banner_one, 'assets/frontend/dist/imgs/banner/banner-1.png') }}" alt="" />
                            <div class="banner-text">
                                <h4 class="mb-30">{{ $heroBanner?->title_one ?: 'Hi-Res Audio Headphones' }}</h4>
                                <a href="{{ $heroBanner?->btn_url_one ?: route('products.index') }}" class="btn btn-xs mb-50">Shop Now <i
                                        class="fi-rs-arrow-small-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="banner-img style-5">
                            <img src="{{ imageUrl($heroBanner?->banner_two, 'assets/frontend/dist/imgs/banner/banner-2.png') }}" alt="" />
                            <div class="banner-text">
                                <h5 class="mb-20">{{ $heroBanner?->title_two ?: 'Mens Leather Waterproof Boots' }}</h5>
                                <a href="{{ $heroBanner?->btn_url_two ?: route('products.index') }}" class="btn btn-xs">Shop Now <i
                                        class="fi-rs-arrow-small-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
