<div class="modal-dialog">
    <div class="modal-content">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6 col-sm-12 col-xs-12 mb-md-0 mb-sm-5">
                    <div class="detail-gallery">
                        <span class="zoom-icon"><i class="fi-rs-search"></i></span>
                        <!-- MAIN SLIDES -->
                        <div class="product-image-slider">
                            @foreach ($product->images as $image)
                                <figure class="border-radius-10">
                                    <img src="{{ asset($image->path) }}" alt=" product image" />
                                </figure>
                            @endforeach

                        </div>
                        <!-- THUMBNAILS -->
                        <div class="slider-nav-thumbnails">
                            @foreach ($product->images as $image)
                                <div><img src="{{ asset($image->path) }}" alt="product image" /></div>
                            @endforeach
                        </div>
                    </div>
                    <!-- End Gallery -->
                </div>
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="detail-info pr-30 pl-30">
                        <span class="stock-status out-stock"> Sale Off </span>
                        <h2 class="title-detail">{{ $product->name }}</h2>
                        <div class="product-detail-rating">
                            <div class="product-rate-cover text-end">
                                <div class="product-rate d-inline-block">
                                    <div class="product-rating" style="width: 90%"></div>
                                </div>
                                <span class="font-small ml-5 text-muted"> (32 reviews)</span>
                            </div>
                        </div>
                        <div class="clearfix product-price-cover">
                            <div class="product-price modal-price primary-color float-left">
                                @if ($product->primaryVariant)
                                    @if ($product->primaryVariant?->special_price > 0)
                                        <span
                                            class="current-price text-brand">${{ $product->primaryVariant?->special_price }}</span>
                                        <span
                                            class="old-price font-md ml-15">${{ $product->primaryVariant?->price }}</span>
                                    @else
                                        <span
                                            class="current-price text-brand">${{ $product->primaryVariant?->price }}</span>
                                    @endif
                                @else
                                    @if ($product->special_price > 0)
                                        <span class="current-price text-brand">
                                            ${{ $product->special_price }}
                                        </span>
                                        <span class="old-price font-md ml-15">
                                            ${{ $product->price }}
                                        </span>
                                    @else
                                        <span class="current-price text-brand">${{ $product->price }}</span>
                                    @endif
                                @endif

                            </div>
                        </div>

                        @foreach ($product->attributeWithValues as $attribute)
                            <div class="attr-detail attr-size mb-20">
                                <strong class="mr-10">{{ $attribute->name }}: </strong>
                                <ul class=" attribute-group color_filter list-filter size-filter font-small"
                                    data-attribute="{{ $attribute->id }}">
                                    @foreach ($attribute->values as $value)
                                        @if ($attribute->type == 'color')
                                            <li class="attribute-badge" data-value="{{ $value->id }}"><a
                                                    href="#" style="background: {{ $value->color }};"></a>
                                            </li>
                                        @else
                                            <li class="attribute-badge" data-value="{{ $value->id }}"><a
                                                    href="#">{{ $value->value }}</a></li>
                                        @endif
                                    @endforeach

                                </ul>
                            </div>
                        @endforeach
                        <input type="hidden" id="variants-data"
                            value="{{ json_encode(
                                $product->variants->map(function ($variant) {
                                    return [
                                        'id' => $variant->id,
                                        'price' => $variant->price,
                                        'special_price' => $variant->special_price,
                                        'manage_stock' => $variant->manage_stock,
                                        'quantity' => $variant->qty,
                                        'sku' => $variant->sku,
                                        'in_stock' => $variant->in_stock,
                                        'is_default' => $variant->is_default,
                                        'attribute_values' => $variant->attributeValues->pluck('id'),
                                    ];
                                }),
                            ) }}">

                        <div class="detail-extralink mb-50">
                            <div class="detail-qty border radius">
                                <a href="#" class="qty-down"><i class="fi-rs-angle-small-down"></i></a>
                                <input type="text" name="quantity" class="qty-val" value="1" min="1">
                                <a href="#" class="qty-up"><i class="fi-rs-angle-small-up"></i></a>
                            </div>
                            <div class="product-extra-link2">
                                <button type="submit" class="button button-add-to-cart add_to_cart" data-variant="" data-id="{{ $product->id }}" data-modal="false" ><i
                                        class="fi-rs-shopping-cart" ></i>Add to cart</button>

                            </div>
                        </div>
                        <div class="font-xs">

                            <ul class="float-start">
                                <li class="mb-5">SKU: <a href="javascript:;" class="sku">
                                        @if ($product?->primaryVariant)
                                            {{ $product?->primaryVariant?->sku }}
                                        @else
                                            {{ $product->sku }}
                                        @endif
                                    </a></li>
                                <li class="mb-5">Tags:
                                    @foreach ($product->tags as $tag)
                                        <a href="#" rel="tag">{{ $tag->name }}</a>
                                        {{ $loop->last ? '' : ', ' }}
                                    @endforeach
                                </li>
                                <li>Stock:<span class="in-stock text-brand ml-5"><span class="stock-qty">
                                            @if ($product->manage_stock == 1)
                                                {{ $product->qty }}
                                            @else
                                                Unlimited
                                            @endif
                                        </span>
                                        Items In Stock</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- Detail Info -->
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $('.product-image-slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: false,
        asNavFor: '.slider-nav-thumbnails',
    });

    $('.slider-nav-thumbnails').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.product-image-slider',
        dots: false,
        focusOnSelect: true,

        prevArrow: '<button type="button" class="slick-prev"><i class="fi-rs-arrow-small-left"></i></button>',
        nextArrow: '<button type="button" class="slick-next"><i class="fi-rs-arrow-small-right"></i></button>'
    });



</script>
