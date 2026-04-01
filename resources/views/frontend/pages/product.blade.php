@extends('frontend.layouts.app')

@section('contents')
    <x-frontend.breadcrumb :items="[['label' => 'Home', 'url' => '/'], ['label' => 'Products']]" />
    <div class="container mt-70 mb-60">
        <div class="row">

            @include('frontend.pages.partials.product-page-sidebar')

            <div class="col-lg-9 col-xxl-10">
                <div class="shop-product-fillter">
                    <div class="totall-product">
                        <p>We found <strong class="text-brand">{{ $products->total() }}</strong> items for you!</p>
                    </div>
                    <div class="sort-by-product-area">
                        {{-- <div class="sort-by-cover mr-10">
                            <div class="sort-by-product-wrap">
                                <div class="sort-by">
                                    <span><i class="fi-rs-apps"></i>Show:</span>
                                </div>
                                <div class="sort-by-dropdown-wrap">
                                    <span> 50 <i class="fi-rs-angle-small-down"></i></span>
                                </div>
                            </div>
                            <div class="sort-by-dropdown">
                                <ul>
                                    <li><a class="active" href="#">50</a></li>
                                    <li><a href="#">100</a></li>
                                    <li><a href="#">150</a></li>
                                    <li><a href="#">200</a></li>
                                    <li><a href="#">All</a></li>
                                </ul>
                            </div>
                        </div> --}}
                        {{-- <div class="sort-by-cover">
                            <div class="sort-by-product-wrap">
                                <div class="sort-by">
                                    <span><i class="fi-rs-apps-sort"></i>Sort by:</span>
                                </div>
                                <div class="sort-by-dropdown-wrap">
                                    <span> Featured <i class="fi-rs-angle-small-down"></i></span>
                                </div>
                            </div>
                            <div class="sort-by-dropdown">
                                <ul>
                                    <li><a class="active" href="#">Featured</a></li>
                                    <li><a href="#">Price: Low to High</a></li>
                                    <li><a href="#">Price: High to Low</a></li>
                                    <li><a href="#">Release Date</a></li>
                                    <li><a href="#">Avg. Rating</a></li>
                                </ul>
                            </div>
                        </div> --}}
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
