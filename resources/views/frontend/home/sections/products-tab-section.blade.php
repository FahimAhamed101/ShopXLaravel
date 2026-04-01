<section class="product-tabs section-padding position-relative mt-30">
    <div class="container">
        <div class="section-title style-2 wow animate__animated animate__fadeIn">
            <h3>Popular Products</h3>
            <ul class="nav nav-tabs links" id="myTab" role="tablist">
                {{-- <li class="nav-item" role="presentation">
                      <button class="nav-link active" id="nav-tab-one" data-bs-toggle="tab"
                          data-bs-target="#tab-one" type="button" role="tab" aria-controls="tab-one"
                          aria-selected="true">All</button>
                  </li> --}}
                @foreach ($popularCategories as $category)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'first-category-tab' : '' }}" id="nav-tab-two"
                            data-bs-toggle="tab" data-bs-target="#tab-{{ $category->id }}" type="button" role="tab"
                            aria-controls="tab-{{ $category->id }}" aria-selected="false">{{ $category->name }}</button>
                    </li>
                @endforeach

            </ul>
        </div>
        <!--End nav-tabs-->
        <div class="tab-content wow animate__animated animate__fadeIn" id="myTabContent">
            @foreach ($popularProducts as $key => $popularProduct)
                <div class="tab-pane fade" id="tab-{{ $key }}" role="tabpanel" aria-labelledby="tab-one">
                    <div class="row product-grid-4">
                        @foreach ($popularProduct as $product)
                            {{-- <div class="product-cart-wrap mb-30" data-wow-delay=".1s">
                                    <div class="product-img-action-wrap">
                                        <div class="product-img product-img-zoom">
                                            <a href="#" tabindex="-1">
                                                <img class="default-img" src="assets/imgs/shop/product-1-1.jpg"
                                                    alt="">
                                                <img class="hover-img" src="assets/imgs/shop/product-1-2.jpg"
                                                    alt="">
                                            </a>
                                        </div>
                                        <div class="product-action-1">
                                            <a aria-label="Add To Wishlist" class="action-btn"
                                                href="shop-wishlist.html"><i class="fi-rs-heart"></i></a>
                                            <a aria-label="Compare" class="action-btn" href="shop-compare.html"><i
                                                    class="fi-rs-shuffle"></i></a>
                                            <a aria-label="Quick view" class="action-btn" data-bs-toggle="modal"
                                                data-bs-target="#quickViewModal"><i class="fi-rs-eye"></i></a>
                                        </div>
                                        <div class="product-badges product-badges-position product-badges-mrg">
                                            <span class="hot">Hot</span>
                                        </div>
                                    </div>
                                    <div class="product-content-wrap">
                                        <div class="product-category">
                                            <a href="shop-grid-right.html">Clothing</a>
                                        </div>
                                        <h2><a href="#">Seeds of Change Organic Quinoa, Brown, &
                                                Red Rice</a></h2>
                                        <div class="product-rate-cover">
                                            <div class="product-rate d-inline-block">
                                                <div class="product-rating" style="width: 90%"></div>
                                            </div>
                                            <span class="font-small ml-5 text-muted"> (4.0)</span>
                                        </div>
                                        <div>
                                            <span class="font-small text-muted">By <a
                                                    href="vendor-details-1.html">ShopX</a></span>
                                        </div>
                                        <div class="product-card-bottom">
                                            <div class="product-price">
                                                <span>$28.85</span>
                                                <span class="old-price">$32.8</span>
                                            </div>
                                            <div class="add-cart">
                                                <a class="add" href="shop-cart.html"><i
                                                        class="fi-rs-shopping-cart mr-5"></i>Add </a>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                            <x-frontend.product-card :product="$product"
                                class="col-6 col-md-4 col-lg-3 col-xl-3 col-xxl-2" />
                        @endforeach
                    </div>
                    <!--End product-grid-4-->
                </div>
            @endforeach

        </div>
        <!--End tab-content-->
    </div>
</section>


@push('scripts')
    <script>
        $(function() {
            $('.first-category-tab').click();
        })
    </script>
@endpush
