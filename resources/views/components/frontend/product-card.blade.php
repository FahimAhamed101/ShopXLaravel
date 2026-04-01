@props(['class' => 'col-6 col-xxl-3 col-lg-4 col-md-4 col-sm-6'])

<div {{ $attributes }} class="{{ $class }}">
    <div class="product-cart-wrap mb-30">
        <div class="product-img-action-wrap">
            <div class="product-img product-img-zoom">
                <a href="{{ route('products.show', $product->slug) }}">
                    @foreach ($product->images as $key => $image)
                        <img class="{{ $key == 0 ? 'default-img' : 'hover-img' }}" src="{{ asset($image->path) }}"
                            alt="" />
                    @endforeach
                    {{-- <img class="hover-img" src="assets/imgs/shop/product-1-2.jpg" alt="" /> --}}
                </a>
            </div>
            <div class="product-action-1">

                <a aria-label="Add To Wishlist" class="action-btn wishlist-btn" data-id="{{ $product->id }}" href="">
                    @if(in_array($product->id, $wishlistsProductIds))
                    <i class="fi fi-ss-heart"></i>
                    @else
                    <i class="fi-rs-heart"></i>
                    @endif
                    </a>
                <a href="{{ route('products.show', $product->slug) }}" aria-label="Quick view" class="action-btn" ><i
                        class="fi-rs-eye"></i></a>
            </div>
            <div class="product-badges product-badges-position product-badges-mrg">
                @if ($product->is_hot == 1)
                    <span class="hot">Hot</span>
                @endif
                @if ($product->is_new == 1)
                    <span class="hot ms-1">New</span>
                @endif
            </div>
        </div>
        <div class="product-content-wrap">
            <div class="product-category">
                {{-- <a href="shop-grid-right.html">{{ $product->category->name }}</a> --}}
            </div>
            <h2><a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a></h2>
            <div class="product-rate-cover">
                <div class="product-rate d-inline-block">
                    <div class="product-rating" style="width: {{ ratingPercent($product->reviews_avg_rating) }}%"></div>
                </div>
                <span class="font-small ml-5 text-muted"> ({{ round($product->reviews_avg_rating, 2) ?? 0 }})</span>
            </div>
            <div>
                <span class="font-small text-muted">By <a
                        href="vendor-details-1.html">{{ $product->store->name }}</a></span>
            </div>
            <div class="product-card-bottom">
                <div class="product-price">
                    @php
                        $price = $product->getEffectivePriceAndStock();
                    @endphp

                    @if ($price['in_stock'])

                        @if ($price['old_price'] > 0)
                            <span>${{ $price['price'] }}</span>
                            <span class="old-price">${{ $price['old_price'] }}</span>
                        @else
                            <span>${{ $price['price'] }}</span>
                        @endif
                    @else
                        <span class="text-danger">Out of stock</span>
                    @endif



                </div>
                @if ($price['in_stock'])
                <div class="add-cart">
                    <a class="add add_to_cart" data-id="{{ $product->id }}"
                        data-modal="{{ $product->primaryVariant ? 'true' : 'false' }}" href=""><i
                            class="fi-rs-shopping-cart mr-5"></i>Add to cart</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!--end product card-->
