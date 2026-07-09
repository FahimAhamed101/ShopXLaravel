<div class="col-lg-3 col-xxl-2 primary-sidebar sticky-sidebar">

    <div class="sidebar_filter d-lg-none">filter</div>

    <div class="sidebar_wraper">
        <div class="sidebar-widget widget-category-2 shopx-category-widget mb-30">
            <h5 class="section-title style-1 mb-20">Categories</h5>

            <ul class="main_category">
                {{-- @dd($categories) --}}
                @foreach ($categories as $category)
                    @php
                        $parentActive = request()->category == $category->slug
                            || $category->children_nested->contains(fn ($child) => request()->category == $child->slug || $child->children_nested->contains('slug', request()->category));
                    @endphp
                    <li class="{{ $parentActive ? 'active open' : '' }}">
                        <a href="{{ route('products.index', array_merge(request()->except(['page', 'category']), ['category' => $category->slug])) }}">{{ $category->name }}
                        </a>
                        @if ($category->children_nested->count() > 0)
                            <ul class="sub_category">
                                @foreach ($category->children_nested as $child)
                                    <li class="{{ request()->category == $child->slug ? 'active' : '' }}">
                                        <a
                                            href="{{ route('products.index', array_merge(request()->except(['page', 'category']), ['category' => $child->slug])) }}">{{ $child->name }}</a>
                                        @if ($child->children_nested->count() > 0)
                                            <ul class="child_category">
                                                @foreach ($child->children_nested as $subchild)
                                                    <li
                                                        class="{{ request()->category == $subchild->slug ? 'active' : '' }}">
                                                        <a
                                                            href="{{ route('products.index', array_merge(request()->except(['page', 'category']), ['category' => $subchild->slug])) }}">{{ $subchild->name }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
        <!-- Fillter By Price -->
        <div class="sidebar-widget price_range range shopx-filter-widget mb-30">
            <div class="shopx-filter-heading">
                <h5 class="section-title style-1 mb-0">Price filter</h5>
                @if (request()->except('page'))
                    <a href="{{ route('products.index') }}">Reset</a>
                @endif
            </div>
            <form action="{{ route('products.index') }}" method="get" class="shopx-filter-form">
                @foreach (request()->only(['category', 'search', 'sort']) as $key => $value)
                    @if (filled($value))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <div class="price-filter">
                    <div class="price-filter-inner">
                        <div id="slider-range" class="mb-20"></div>
                        <div class="d-flex justify-content-between">
                            <div class="caption">From: <span class="shopx-price-prefix">$</span>
                                <input
                                    type="number"
                                    name="from"
                                    id="price_from"
                                    class="shopx-price-input"
                                    min="{{ (int) $priceMin }}"
                                    max="{{ (int) $priceMax }}"
                                    value="{{ (int) ($from ?? $priceMin) }}"
                                >
                            </div>
                            <div class="caption">To: <span class="shopx-price-prefix">$</span>
                                <input
                                    type="number"
                                    name="to"
                                    id="price_to"
                                    class="shopx-price-input"
                                    min="{{ (int) $priceMin }}"
                                    max="{{ (int) $priceMax }}"
                                    value="{{ (int) ($to ?? $priceMax) }}"
                                >
                            </div>
                        </div>
                    </div>
                </div>
                @if ($brands->isNotEmpty() || $tags->isNotEmpty())
                    <div class="list-group">
                        <div class="list-group-item mb-10 mt-10">
                            @if ($brands->isNotEmpty())
                                <label class="fw-900">Brands</label>
                                <div class="custome-checkbox">
                                    @foreach ($brands as $brand)
                                        @php
                                            $selectedBrands = collect(request('brands', []))->map(fn ($id) => (int) $id)->all();
                                        @endphp
                                        <div class="shopx-check-row">
                                            <input @checked(in_array($brand->id, $selectedBrands)) class="form-check-input" type="checkbox"
                                                name="brands[]" id="brand-{{ $brand->id }}"
                                                value="{{ $brand->id }}" />
                                            <label class="form-check-label"
                                                for="brand-{{ $brand->id }}"><span>{{ $brand->name }}
                                                    ({{ $brand->products_count }})
                                                </span></label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if ($tags->isNotEmpty())
                                <label class="fw-900 mt-15">Tags</label>
                                <div class="custome-checkbox">
                                    @foreach ($tags as $tag)
                                        @php
                                            $selectedTags = collect(request('tags', []))->map(fn ($id) => (int) $id)->all();
                                        @endphp
                                        <div class="shopx-check-row">
                                            <input @checked(in_array($tag->id, $selectedTags)) class="form-check-input" type="checkbox"
                                                name="tags[]" id="tag-{{ $tag->id }}"
                                                value="{{ $tag->id }}" />
                                            <label class="form-check-label"
                                                for="tag-{{ $tag->id }}"><span>{{ $tag->name }}
                                                    ({{ $tag->products_count }})
                                                </span></label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                <button type="submit" class="btn btn-sm btn-default"><i
                        class="fi-rs-filter mr-5"></i>
                    Apply filter</button>
            </form>
        </div>

        <a href="{{ data_get($ads, 'side_banner_three.0.url', '') }}"
            class="banner-img wow fadeIn d-block">
            <img src="{{ imageUrl(data_get($ads, 'side_banner_three.0.image'), 'assets/frontend/dist/imgs/banner/banner-8.png') }}" alt="" />
        </a>
    </div>

</div>


@push('scripts')
    <script>
        $(function() {
            // Slider Range JS
            if ($("#slider-range").length) {
                var rangeSlider = document.getElementById("slider-range");
                var filterForm = document.querySelector(".shopx-filter-form");
                var moneyFormat = wNumb({
                    decimals: 0,
                    thousand: ",",
                    prefix: "$"
                });
                var minPrice = {{ (int) $priceMin }};
                var maxPrice = {{ (int) $priceMax }};
                var selectedFrom = {{ (int) ($from ?? $priceMin) }};
                var selectedTo = {{ (int) ($to ?? $priceMax) }};

                selectedFrom = Math.max(minPrice, Math.min(selectedFrom, maxPrice));
                selectedTo = Math.max(minPrice, Math.min(selectedTo, maxPrice));

                if (selectedFrom > selectedTo) {
                    var swapPrice = selectedFrom;
                    selectedFrom = selectedTo;
                    selectedTo = swapPrice;
                }

                function syncPriceInputs(values) {
                    var fromValue = Math.round(Number(values[0]));
                    var toValue = Math.round(Number(values[1]));

                    document.getElementById("price_from").value = fromValue;
                    document.getElementById("price_to").value = toValue;
                }

                if (typeof noUiSlider !== "undefined") {
                    noUiSlider.create(rangeSlider, {
                        start: [selectedFrom, selectedTo],
                        step: 1,
                        range: {
                            min: [minPrice],
                            max: [maxPrice]
                        },
                        connect: true
                    });

                    rangeSlider.noUiSlider.on("update", function(values) {
                        syncPriceInputs(values);
                    });

                    $("#price_from, #price_to").on("change", function() {
                        var inputFrom = Number(document.getElementById("price_from").value || minPrice);
                        var inputTo = Number(document.getElementById("price_to").value || maxPrice);

                        inputFrom = Math.max(minPrice, Math.min(inputFrom, maxPrice));
                        inputTo = Math.max(minPrice, Math.min(inputTo, maxPrice));

                        if (inputFrom > inputTo) {
                            var swapInput = inputFrom;
                            inputFrom = inputTo;
                            inputTo = swapInput;
                        }

                        rangeSlider.noUiSlider.set([inputFrom, inputTo]);
                    });
                }

                if (filterForm) {
                    filterForm.addEventListener("submit", function() {
                        var submitFrom = Number(document.getElementById("price_from").value || minPrice);
                        var submitTo = Number(document.getElementById("price_to").value || maxPrice);

                        submitFrom = Math.max(minPrice, Math.min(submitFrom, maxPrice));
                        submitTo = Math.max(minPrice, Math.min(submitTo, maxPrice));

                        if (submitFrom > submitTo) {
                            var swapSubmit = submitFrom;
                            submitFrom = submitTo;
                            submitTo = swapSubmit;
                        }

                        syncPriceInputs([submitFrom, submitTo]);
                    });
                }
            }
        })
    </script>
@endpush
