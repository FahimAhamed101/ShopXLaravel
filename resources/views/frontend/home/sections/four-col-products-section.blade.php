        <section class="section-padding mt-10 mb-30">
            <div class="container">
                <div class="row">
                    <div class="col-6 col-xl-3 col-lg-4 col-md-6 mb-30 wow animate__animated animate__fadeInUp"
                        data-wow-delay="0">
                        <h4 class="section-title style-1 mb-30 animated animated">Hot Selling</h4>
                        <div class="product-list-small animated animated">
                            @foreach ($hotProducts as $product)
                                <x-frontend.product-card-sm :product="$product" />
                            @endforeach
                        </div>
                    </div>
                    <div class="col-6 col-xl-3 col-lg-4 col-md-6 mb-30 wow animate__animated animate__fadeInUp"
                        data-wow-delay=".1s">
                        <h4 class="section-title style-1 mb-30 animated animated">New Products</h4>
                        <div class="product-list-small animated animated">
                            @foreach ($newProducts as $product)
                                <x-frontend.product-card-sm :product="$product" />
                            @endforeach
                        </div>
                    </div>
                    <div class="col-6 col-xl-3 col-lg-4 col-md-6 mb-30 wow animate__animated animate__fadeInUp"
                        data-wow-delay=".2s">
                        <h4 class="section-title style-1 mb-30 animated animated">To Rated</h4>
                        <div class="product-list-small animated animated">
                            @foreach ($topRatedProducts as $product)
                                <x-frontend.product-card-sm :product="$product" />
                            @endforeach
                        </div>
                    </div>
                    <div class="col-6 col-xl-3 col-lg-4 col-md-6 mb-30 wow animate__animated animate__fadeInUp"
                        data-wow-delay=".3s">
                        <h4 class="section-title style-1 mb-30 animated animated">Featured Products</h4>
                        <div class="product-list-small animated animated">
                            @foreach ($featuredProducts as $product)
                                <x-frontend.product-card-sm :product="$product" />
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
