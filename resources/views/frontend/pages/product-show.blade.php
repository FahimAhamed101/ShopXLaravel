@extends('frontend.layouts.app')

@section('contents')
    <x-frontend.breadcrumb :items="[['label' => 'Home', 'url' => '/'], ['label' => 'Products']]" />
    <div class="container mb-30">
        <div class="row">
            <div class="col-xl-12">
                <div class="product-detail accordion-detail">
                    <div class="row mb-50 mt-70">
                        <div class="col-md-6 col-lg-4 col-sm-12 col-xs-12 mb-md-0 mb-sm-5">
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
                        <div class="col-md-6 col-lg-7 col-sm-12 col-xs-12">
                            <div class="detail-info pr-30 pl-30">
                                <h2 class="title-detail">{{ $product->name }}</h2>
                                <div class="product-detail-rating">
                                    <div class="product-rate-cover text-end">
                                        <div class="product-rate d-inline-block">
                                            <div class="product-rating" style="width: {{ ratingPercent($product->reviews_avg_rating) }}%"></div>
                                        </div>
                                        <span class="font-small ml-5 text-muted"> ({{ $product->reviews_count }} reviews)</span>
                                    </div>
                                </div>
                                <div class="clearfix product-price-cover">
                                    <div class="product-price primary-color float-left">
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
                                <div class="short-desc mb-30">
                                    <p class="font-lg">{!! $product->short_description !!}</p>
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

                                @if ($product->primaryVariant)
                                    <input type="hidden" name="variant_id" id="selected-variant" value="">
                                @endif

                                <div class="detail-extralink mb-50">
                                    <div class="detail-qty border radius">
                                        <a href="#" class="qty-down"><i class="fi-rs-angle-small-down"></i></a>
                                        <input type="text" name="quantity" class="qty-val" value="1" min="1">
                                        <a href="#" class="qty-up"><i class="fi-rs-angle-small-up"></i></a>
                                    </div>
                                    <div class="product-extra-link2">
                                        <button type="submit" data-variant="" data-id="{{ $product->id }}"
                                            data-modal="false" class="button button-add-to-cart add_to_cart"><i
                                                class="fi-rs-shopping-cart"></i>Add to cart</button>
                                        <a aria-label="Add To Wishlist" class="action-btn hover-up wishlist-btn" data-id="{{ $product->id }}"
                                            href="" ><i class="fi-rs-heart"></i></a>

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
                    <div class="product-info">
                        <div class="tab-style3">
                            <ul class="nav nav-tabs text-uppercase">
                                <li class="nav-item">
                                    <a class="nav-link active" id="Description-tab" data-bs-toggle="tab"
                                        href="#Description">Description</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="Vendor-info-tab" data-bs-toggle="tab"
                                        href="#Vendor-info">Vendor</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="Reviews-tab" data-bs-toggle="tab" href="#Reviews">Reviews
                                        ({{ $product->reviews_count }})</a>
                                </li>
                            </ul>
                            <div class="tab-content shop_info_tab entry-main-content">
                                <div class="tab-pane fade show active" id="Description">
                                    <div class="">
                                        {!! $product->description !!}
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="Vendor-info">
                                    <div class="vendor-logo d-flex mb-30 align-items-center">
                                        <img src="{{ asset($product->store->logo) }}" alt="" />
                                        <div class="vendor-name ml-15">
                                            <h6>
                                                <a href="vendor-details-2.html">{{ $product->store->name }}</a>
                                            </h6>
                                            <div class="product-rate-cover text-end">
                                                <div class="product-rate d-inline-block">
                                                    <div class="product-rating" style="width: 90%"></div>
                                                </div>
                                                <span class="font-small ml-5 text-muted"> (32 reviews)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="contact-infor mb-50">
                                        @if ($product->store->address)
                                            <li><img src="{{ asset('assets/frontend/dist/imgs/theme/icons/icon-location.svg') }}"
                                                    alt="" /><strong>Address: </strong>
                                                <span>{{ $product->store->address }}</span>
                                            </li>
                                        @endif
                                        @if ($product->store->phone)
                                            <li><img src="{{ asset('assets/frontend/dist/imgs/theme/icons/icon-contact.svg') }}"
                                                    alt="" /><strong>Phone:</strong><span>
                                                    {{ $product->store->phone }} </span></li>
                                        @endif
                                        @if ($product->store->email)
                                            <li><img src="{{ asset('assets/frontend/dist/imgs/theme/icons/icon-contact.svg') }}"
                                                    alt="" /><strong>Phone:</strong><span>
                                                    {{ $product->store->email }} </span></li>
                                        @endif
                                    </ul>

                                    <p>{{ $product->store->short_description }}</p>
                                </div>
                                <div class="tab-pane fade" id="Reviews">
                                    <!--Comments-->
                                    <div class="comments-area">
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <h4 class="mb-30">Customer Reviews</h4>
                                                <div class="comment-list">
                                                    @forelse($reviews as $review)
                                                    <div class="single-comment justify-content-between d-flex mb-30">
                                                        <div class="user justify-content-between d-flex">
                                                            <div class="thumb text-center">
                                                                <img src="{{ asset($review->user->avatar) }}" alt="" />
                                                                <a href="#"
                                                                    class="font-heading text-brand">{{ $review->user->name }}</a>
                                                            </div>
                                                            <div class="desc">
                                                                <div class="d-flex justify-content-between mb-10">
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="font-xs text-muted"> {{ date('F d Y', strtotime($review->created_at)) }}</span>
                                                                    </div>
                                                                    <div class="product-rate d-inline-block">
                                                                        <div class="product-rating" style="width: {{ $review->rating * 2 }}0%">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <p class="mb-10">{{ $review->review }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @empty
                                                    <div class="alert alert-warning">No reviews found</div>
                                                    @endforelse
                                                </div>
                                                {{ $reviews->links() }}
                                            </div>
                                            <div class="col-lg-4">
                                                <h4 class="mb-30">Customer reviews</h4>

                                                <div class="d-flex mb-30">
                                                    <div class="product-rate d-inline-block mr-15">
                                                        <div class="product-rating" style="width: 90%"></div>
                                                    </div>
                                                    <h6>{{ round($avgRating, 1) }} out of 5</h6>
                                                </div>
                                                @foreach(range(5, 1) as $star)
                                                @php
                                                    $count = $reviewGroup[$star] ?? 0;
                                                    $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                                                @endphp
                                                <div class="progress">
                                                    <span>{{ $star }} star</span>
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%"
                                                        aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">{{ $percent }}%
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <!--comment form-->
                                    <div class="comment-form">
                                        <h4 class="mb-15">Add a review</h4>
                                        <div class="product-rate d-inline-block mb-30"></div>
                                        <div class="row">
                                            @if(Auth::guard('web')->check())
                                            <div class="col-lg-8 col-md-12">
                                                <form class="form-contact comment_form" method="POST" action="{{ route('product.review.store', $product) }}" id="commentForm">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                               <select name="rating" id="" class="form-control">
                                                                @for($i = 5; $i > 0; $i--)
                                                                    <option value="{{ $i }}">{{ $i }} Star</option>
                                                                @endfor
                                                               </select>
                                                                <x-input-error :messages="$errors->get('rating')" class="mt-2" />
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <textarea class="form-control w-100" name="review" id="comment" cols="30" rows="9"
                                                                    placeholder="Write Comment"></textarea>
                                                                <x-input-error :messages="$errors->get('review')" class="mt-2" />
                                                            </div>
                                                        </div>


                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" class="button button-contactForm">Submit
                                                            Review</button>
                                                    </div>
                                                </form>
                                            </div>
                                            @else
                                            <div class="alert alert-warning col-lg-8 col-md-12">
                                                <p class="mb-0">Please <a href="{{ route('login') }}">login</a> to write a review.</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-70">
                        <div class="col-12">
                            <h2 class="section-title style-1 mb-30">Related products</h2>
                        </div>
                        <div class="col-12">
                            <div class="row related-products">

                                @foreach ($relatedProducts as $product)
                                    <x-frontend.product-card :product="$product" class="col-6 col-lg-4 col-xl-3 col-xxl-2" />
                                @endforeach

                                <!--end product card-->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script>
        // notyf init
        var notyf = new Notyf({
            duration: 3000
        });

        $(function() {
            const variantsData = JSON.parse($('#variants-data').val());
            let selectedValues = new Set();

            function selectDefaultVariant() {
                if (variantsData.length > 0) {
                    const defaultVariant = variantsData[0];

                    defaultVariant.attribute_values.forEach(valueId => {
                        const $badge = $(`.attribute-badge[data-value="${valueId}"]`);
                        $badge.addClass('active');
                        selectedValues.add(valueId);
                    })
                }

                updatePrice();
            }

            $('.attribute-badge').on('click', function() {
                const $attributeGroup = $(this).closest('.attribute-group');

                selectedValues = new Set(
                    $('.attribute-badge.active').map(function() {
                        return parseInt($(this).attr('data-value'));
                    }).get()
                );

                updatePrice();
            })

            function updatePrice() {
                const selectedValuesArray = Array.from(selectedValues);

                const matchingVariant = variantsData.find(variant => {
                    const variantValues = new Set(variant.attribute_values);
                    return selectedValuesArray.length === variantValues.size && selectedValuesArray.every(
                        value => variantValues.has(value));
                })

                if (matchingVariant) {
                    $('.button-add-to-cart').attr('data-variant', matchingVariant.id);


                    if (matchingVariant.quantity > 0 && matchingVariant.manage_stock == 1) {
                        $('.stock-qty').text(matchingVariant.quantity);
                    } else if (matchingVariant.manage_stock == 0 && matchingVariant.in_stock == 1) {
                        $('.stock-qty').text('Unlimited');
                    } else {
                        $('.stock-qty').text('0');
                    }

                    $('.sku').text(matchingVariant.sku);


                    if (matchingVariant.in_stock == 0 || matchingVariant.in_stock == null || matchingVariant
                        .quantity < 1 && matchingVariant.manage_stock == 1) {
                        html = `<div class="product-price primary-color float-left">
                            <span class="current-price text-brand">Out Of Stock</span>
                        </div>`

                        $('.product-price').replaceWith(html);

                        return;
                    }

                    if (matchingVariant.special_price > 0) {
                        var html = `
                        <div class="product-price primary-color float-left">
                                <span class="current-price text-brand">$${matchingVariant.special_price}</span>
                                    <span>
                                        <span class="old-price font-md ml-15">$${matchingVariant.price}</span>
                                    </span>
                        </div>
                        `
                    } else {
                        var html = `
                        <div class="product-price primary-color float-left">
                            <span class="current-price text-brand">$${matchingVariant.price}</span>
                        </div>
                        `
                    }

                    $('.product-price').replaceWith(html);
                }

            }

            selectDefaultVariant();


            function handleErrors(errors) {
                if (errors?.message) {
                    notyf.error(errors.message);
                } else if (errors?.error) {
                    Object.values(errors.errors).forEach((err) => notyf.error(err[0]));
                } else {
                    notyf.error('Something went wrong');
                }
            }



            $('.comment_form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('product.review.store', $product) }}",
                    method: "POST",
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('.button-contactForm').text('Submitting...');
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            notyf.success(response.message);
                            $('.comment_form')[0].reset();
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error ) {
                        console.log(xhr);
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            notyf.error(errors[key][0]);
                        });
                    },
                    complete: function() {
                        $('.button-contactForm').text('Submit Review');
                    }
                })
            });


        })
    </script>
@endpush
