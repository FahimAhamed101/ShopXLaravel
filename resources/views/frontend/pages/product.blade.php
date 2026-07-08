@extends('frontend.layouts.app')

@php
    $activeFilters = collect(request()->except('page'))
        ->filter(fn ($value) => is_array($value) ? count(array_filter($value)) : filled($value))
        ->count();
@endphp

@section('contents')
    <x-frontend.breadcrumb :items="[['label' => 'Home', 'url' => '/'], ['label' => 'Products']]" />
    <div class="container product-index-page mt-50 mb-60">
        <div class="row">

            @include('frontend.pages.partials.product-page-sidebar')

            <div class="col-lg-9 col-xxl-10">
                <div class="shop-product-fillter">
                    <div class="totall-product">
                        <p>We found <strong class="text-brand">{{ $products->total() }}</strong> items for you!</p>
                        @if ($activeFilters)
                            <a href="{{ route('products.index') }}" class="shopx-clear-filters">Clear {{ $activeFilters }} {{ \Illuminate\Support\Str::plural('filter', $activeFilters) }}</a>
                        @endif
                    </div>
                    <div class="sort-by-product-area">
                        <form action="{{ route('products.index') }}" method="get" class="shopx-sort-form">
                            @foreach (request()->except(['sort', 'page']) as $key => $value)
                                @if (is_array($value))
                                    @foreach ($value as $item)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <label for="sort" class="mb-0">Sort by</label>
                            <select name="sort" id="sort" onchange="this.form.submit()">
                                <option value="">Latest</option>
                                <option value="oldest" @selected(request('sort') === 'oldest')>Oldest</option>
                                <option value="price_low" @selected(request('sort') === 'price_low')>Price: Low to High</option>
                                <option value="price_high" @selected(request('sort') === 'price_high')>Price: High to Low</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="row product-grid">
                    @forelse($products as $product)
                        <x-frontend.product-card :product="$product" />
                    @empty
                        <p>No product found</p>
                    @endforelse
                </div>
                <!--product grid-->
                <div class="pagination-area">
                  {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <style>
        .product-index-page .shop-product-fillter {
            align-items: center;
            border: 1px solid #ececec;
            padding: 14px 18px;
            margin-bottom: 26px;
            background: #fff;
        }

        .product-index-page .totall-product {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .product-index-page .totall-product p {
            margin-bottom: 0;
        }

        .shopx-clear-filters {
            color: var(--colorSecondary);
            font-weight: 700;
            font-size: 13px;
        }

        .shopx-sort-form {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #253d4e;
            font-weight: 700;
        }

        .shopx-sort-form select {
            min-width: 180px;
            height: 38px;
            border: 1px solid #ececec;
            background: #fff;
            padding: 0 12px;
            color: #253d4e;
        }

        .product-index-page .product-grid .product-cart-wrap {
            height: calc(100% - 30px);
        }

        .product-index-page .pagination-area {
            margin-top: 10px;
        }

        .product-index-page .primary-sidebar .sidebar-widget {
            border: 1px solid #ececec;
            padding: 22px;
            background: #fff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
        }

        .product-index-page .shopx-category-widget .section-title,
        .product-index-page .shopx-filter-widget .section-title {
            font-size: 22px;
            line-height: 1.2;
        }

        .product-index-page .shopx-category-widget .main_category,
        .product-index-page .shopx-category-widget .sub_category,
        .product-index-page .shopx-category-widget .child_category {
            margin: 0;
            padding: 0;
        }

        .product-index-page .shopx-category-widget .main_category li {
            list-style: none;
            border: 0;
            margin: 0;
            background: transparent;
        }

        .product-index-page .shopx-category-widget .main_category>li {
            padding: 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-index-page .shopx-category-widget .main_category>li:last-child {
            border-bottom: 0;
        }

        .product-index-page .shopx-category-widget .main_category a {
            color: #253d4e;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
            font-size: 14px;
            line-height: 1.2;
            padding: 10px 8px;
            border-radius: 6px;
            width: 100%;
            transition: color .15s ease, background-color .15s ease;
        }

        .product-index-page .shopx-category-widget .main_category>li>a::after {
            content: "\f054";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 10px;
            color: #b6b6b6;
            margin-left: 10px;
        }

        .product-index-page .shopx-category-widget .main_category li.active>a,
        .product-index-page .shopx-category-widget .main_category a:hover {
            color: var(--colorSecondary);
            background: rgba(255, 128, 0, 0.08);
        }

        .product-index-page .shopx-category-widget .main_category li.active>a::after {
            color: var(--colorSecondary);
        }

        .product-index-page .shopx-category-widget .sub_category,
        .product-index-page .shopx-category-widget .child_category {
            padding-left: 14px;
            margin: 0 0 8px 0;
        }

        .product-index-page .shopx-category-widget .sub_category li,
        .product-index-page .shopx-category-widget .child_category li {
            padding: 0;
        }

        .product-index-page .shopx-category-widget .sub_category a,
        .product-index-page .shopx-category-widget .child_category a {
            color: #5f6f7a;
            font-size: 13px;
            font-weight: 500;
            padding: 7px 8px;
        }

        .product-index-page .shopx-category-widget .child_category {
            padding-left: 12px;
        }

        .shopx-filter-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .shopx-filter-heading a {
            color: var(--colorSecondary);
            font-weight: 700;
            font-size: 13px;
        }

        .shopx-filter-form .list-group-item {
            padding: 0;
            border: 0;
        }

        .shopx-check-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }

        .shopx-check-row .form-check-input {
            margin: 0;
        }

        .shopx-check-row label {
            margin: 0;
            color: #253d4e;
            cursor: pointer;
        }

        .shopx-empty-filter {
            margin: 8px 0 0;
            color: #9b9b9b;
            font-size: 13px;
        }

        .product-index-page .price-filter {
            margin-bottom: 18px;
        }

        .product-index-page .range #slider-range {
            height: 4px;
            border: 0;
            border-radius: 999px;
            background: #d9d9d9;
            margin: 22px 4px 22px;
            box-shadow: none;
        }

        .product-index-page .range #slider-range .noUi-base,
        .product-index-page .range #slider-range .noUi-connects {
            height: 4px;
            border-radius: 999px;
        }

        .product-index-page .range #slider-range .noUi-connect {
            background: var(--colorSecondary);
        }

        .product-index-page .range #slider-range .noUi-handle,
        .product-index-page .range #slider-range .noUi-horizontal .noUi-handle {
            width: 16px !important;
            height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            right: -8px !important;
            left: auto !important;
            top: -6px !important;
            border: 3px solid #fff !important;
            border-radius: 50% !important;
            background: var(--colorSecondary) !important;
            box-shadow: 0 2px 8px rgba(255, 128, 0, 0.35) !important;
            cursor: pointer;
            box-sizing: border-box !important;
        }

        .product-index-page .range #slider-range .noUi-handle::before,
        .product-index-page .range #slider-range .noUi-handle::after {
            display: none !important;
        }

        .product-index-page .range #slider-range .noUi-handle:active {
            width: 18px !important;
            height: 18px !important;
            top: -7px !important;
            right: -9px !important;
            border-width: 3px !important;
        }

        .product-index-page .price-filter .caption {
            color: #7e7e7e;
            font-size: 14px;
            display: flex;
            gap: 4px;
            white-space: nowrap;
        }

        .product-index-page .price-filter .text-brand {
            color: var(--colorSecondary) !important;
        }

        .product-index-page .price_range .btn {
            border-radius: 0;
            padding: 10px 18px;
            margin-top: 6px;
            background: var(--colorSecondary);
            border-color: var(--colorSecondary);
            font-weight: 700;
        }

        .product-index-page .sidebar_wraper>.banner-img {
            border: 1px solid #ececec;
        }

        @media (max-width: 767.98px) {
            .product-index-page .shop-product-fillter {
                align-items: flex-start;
                gap: 14px;
            }

            .shopx-sort-form {
                width: 100%;
                justify-content: space-between;
            }

            .shopx-sort-form select {
                flex: 1;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script>
        // // notyf init
        // var notyf = new Notyf({
        //     duration: 3000
        // });

        // $(function() {

        //     function handleErrors(errors) {
        //         if (errors?.message) {
        //             notyf.error(errors.message);
        //         } else if (errors?.error) {
        //             Object.values(errors.errors).forEach((err) => notyf.error(err[0]));
        //         } else {
        //             notyf.error('Something went wrong');
        //         }
        //     }


        //     $(document).on('click', '.add_to_cart', function(e) {
        //         e.preventDefault();
        //         var self = $(this);
        //         const productId = $(this).data('id');
        //         const quantity = $('.qty-val').val();
        //         const variantId = $(this).attr('data-variant');
        //         const modal = $(this).data('modal');


        //         $.ajax({
        //             url: "{{ route('cart.add') }}",
        //             method: "POST",
        //             data: {
        //                 _token: "{{ csrf_token() }}",
        //                 product_id: productId,
        //                 quantity: quantity ?? 1,
        //                 variant_id: variantId,
        //                 modal: modal
        //             },
        //             beforeSend: function() {
        //                 self.html(
        //                     '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
        //                 );
        //             },
        //             success: function(response) {
        //                 if (response.show_modal) {
        //                     $('#quickViewModal').html(response.modal);
        //                     initVariantJs();

        //                     $('#quickViewModal').modal('show');
        //                 }

        //                 if (response.status == 'success' && !response.show_modal) {
        //                     $('.cart-count').html(response.cart_count);
        //                     notyf.success(response.message);
        //                 }
        //             },
        //             error: (errors) => handleErrors(errors.responseJSON),
        //             complete: function() {
        //                 self.html('<i class="fi-rs-shopping-cart mr-5"></i>Add to cart');
        //             }
        //         })
        //     })


        //     function initVariantJs() {

        //         const variantsData = JSON.parse($('#variants-data').val());
        //         let selectedValues = new Set();


        //         $('.list-filter').each(function() {
        //             $(this).find('a').on('click', function(event) {
        //                 event.preventDefault();
        //                 $(this).parent().siblings().removeClass('active');
        //                 $(this).parent().addClass('active');
        //                 $(this).parents('.attr-detail').find('.current-size').text($(this).text());
        //                 $(this).parents('.attr-detail').find('.current-color').text($(this).attr(
        //                     'data-color'));
        //             });
        //         });

        //         $('.detail-qty').each(function() {
        //             var qtyval = parseInt($(this).find(".qty-val").val(), 10);
        //             var $qtyInput = $(this).find(".qty-val");

        //             $(this).find('.qty-up').on('click', function(event) {
        //                 event.preventDefault();
        //                 qtyval = qtyval + 1;
        //                 $qtyInput.val(qtyval);
        //             });

        //             $(this).find(".qty-down").on("click", function(event) {
        //                 event.preventDefault(); /*  */
        //                 qtyval = Math.max(1, qtyval - 1);
        //                 $qtyInput.val(qtyval);
        //             });
        //         });

        //         function selectDefaultVariant() {
        //             if (variantsData.length > 0) {
        //                 const defaultVariant = variantsData[0];

        //                 defaultVariant.attribute_values.forEach(valueId => {
        //                     const $badge = $(`.attribute-badge[data-value="${valueId}"]`);
        //                     $badge.addClass('active');
        //                     selectedValues.add(valueId);
        //                 })
        //             }

        //             updatePrice();
        //         }

        //         //  $('.attribute-badge').on('click', function() {

        //         //  })

        //         $(document).on('click', '.attribute-badge', function() {
        //             console.log('working');
        //             const $attributeGroup = $(this).closest('.attribute-group');

        //             selectedValues = new Set(
        //                 $('.attribute-badge.active').map(function() {
        //                     return parseInt($(this).attr('data-value'));
        //                 }).get()
        //             );

        //             updatePrice();
        //         })

        //         function updatePrice() {
        //             const selectedValuesArray = Array.from(selectedValues);

        //             const matchingVariant = variantsData.find(variant => {
        //                 const variantValues = new Set(variant.attribute_values);
        //                 return selectedValuesArray.length === variantValues.size && selectedValuesArray
        //                     .every(
        //                         value => variantValues.has(value));
        //             })

        //             if (matchingVariant) {

        //                 $('.button-add-to-cart').attr('data-variant', matchingVariant.id);


        //                 if (matchingVariant.quantity > 0 && matchingVariant.manage_stock == 1) {
        //                     $('.stock-qty').text(matchingVariant.quantity);
        //                 } else if (matchingVariant.manage_stock == 0 && matchingVariant.in_stock == 1) {
        //                     $('.stock-qty').text('Unlimited');
        //                 } else {
        //                     $('.stock-qty').text('0');
        //                 }

        //                 $('.sku').text(matchingVariant.sku);


        //                 if (matchingVariant.in_stock == 0 || matchingVariant.in_stock == null || matchingVariant
        //                     .quantity < 1 && matchingVariant.manage_stock == 1) {
        //                     html = `<div class="product-price modal-price primary-color float-left">
        //                     <span class="current-price text-brand">Out Of Stock</span>
        //                 </div>`

        //                     $('.modal-price').replaceWith(html);

        //                     return;
        //                 }

        //                 if (matchingVariant.special_price > 0) {
        //                     var html = `
        //                 <div class="product-price modal-price primary-color float-left">
        //                         <span class="current-price text-brand">$${matchingVariant.special_price}</span>
        //                             <span>
        //                                 <span class="old-price font-md ml-15">$${matchingVariant.price}</span>
        //                             </span>
        //                 </div>
        //                 `
        //                 } else {
        //                     var html = `
        //                 <div class="product-price modal-price primary-color float-left">
        //                     <span class="current-price text-brand">$${matchingVariant.price}</span>
        //                 </div>
        //                 `
        //                 }

        //                 $('.modal-price').replaceWith(html);
        //             }

        //         }

        //         selectDefaultVariant();
        //     }
        // })
    </script>
@endpush
