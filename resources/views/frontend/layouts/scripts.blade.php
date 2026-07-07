<script>
    // notyf init
    var notyf = new Notyf({
        duration: 3000
    });


        $(function() {

            function handleErrors(xhr) {
                const errors = xhr.responseJSON;

                if (xhr.status === 401 || errors?.message === 'Unauthenticated.') {
                    notyf.error('Please login to continue.');
                    window.location.href = "{{ route('login') }}";
                } else if (errors?.message) {
                    notyf.error(errors.message);
                } else if (errors?.errors) {
                    Object.values(errors.errors).forEach((err) => notyf.error(err[0]));
                } else {
                    notyf.error('Something went wrong');
                }
            }


            $(document).on('click', '.add_to_cart', function(e) {
                e.preventDefault();
                var self = $(this);
                if (self.prop('disabled') || self.hasClass('disabled')) {
                    return;
                }
                const productId = $(this).data('id');
                const quantity = self.closest('.detail-extralink').find('.qty-val').val() || $('.qty-val').first().val() || 1;
                const variantId = $(this).attr('data-variant');
                const modal = $(this).data('modal');


                $.ajax({
                    url: "{{ route('cart.add') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_id: productId,
                        quantity: quantity ?? 1,
                        variant_id: variantId,
                        modal: modal
                    },
                    beforeSend: function() {
                        self.html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                        );
                    },
                    success: function(response) {
                        if (response.show_modal) {
                            $('#quickViewModal').html(response.modal);
                            initVariantJs();

                            $('#quickViewModal').modal('show');
                        }

                        if (response.status == 'success' && !response.show_modal) {
                            $('.cart-count').html(response.cart_count);
                            notyf.success(response.message);
                        }
                    },
                    error: (xhr) => handleErrors(xhr),
                    complete: function() {
                        self.html('<i class="fi-rs-shopping-cart mr-5"></i>Add to cart');
                    }
                })
            })


            function initVariantJs() {

                const $variantsData = $('#variants-data');

                if (! $variantsData.length) {
                    return;
                }

                const variantsData = JSON.parse($variantsData.val() || '[]');
                let selectedValues = new Set();


                $('.list-filter').each(function() {
                    $(this).find('a').on('click', function(event) {
                        event.preventDefault();
                        $(this).parent().siblings().removeClass('active');
                        $(this).parent().addClass('active');
                        $(this).parents('.attr-detail').find('.current-size').text($(this).text());
                        $(this).parents('.attr-detail').find('.current-color').text($(this).attr(
                            'data-color'));
                    });
                });

                $('.detail-qty').each(function() {
                    var qtyval = parseInt($(this).find(".qty-val").val(), 10);
                    var $qtyInput = $(this).find(".qty-val");

                    $(this).find('.qty-up').on('click', function(event) {
                        event.preventDefault();
                        qtyval = qtyval + 1;
                        $qtyInput.val(qtyval);
                    });

                    $(this).find(".qty-down").on("click", function(event) {
                        event.preventDefault(); /*  */
                        qtyval = Math.max(1, qtyval - 1);
                        $qtyInput.val(qtyval);
                    });
                });

                function selectDefaultVariant() {
                    if (variantsData.length > 0) {
                        const defaultVariant = variantsData.find(variant => parseInt(variant.is_default) === 1) || variantsData[0];

                        (defaultVariant.attribute_values || []).forEach(valueId => {
                            const $badge = $(`.attribute-badge[data-value="${valueId}"]`);
                            $badge.addClass('active');
                            selectedValues.add(parseInt(valueId));
                        })
                    }

                    updatePrice();
                }

                //  $('.attribute-badge').on('click', function() {

                //  })

                $(document).on('click', '.attribute-badge', function() {
                    const $attributeGroup = $(this).closest('.attribute-group');
                    $attributeGroup.find('.attribute-badge').removeClass('active');
                    $(this).addClass('active');

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
                        const variantValues = new Set((variant.attribute_values || []).map((value) => parseInt(value)));
                        return selectedValuesArray.length === variantValues.size && selectedValuesArray
                            .every(
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
                            html = `<div class="product-price modal-price primary-color float-left">
                            <span class="current-price text-brand">Out Of Stock</span>
                        </div>`

                            $('.modal-price').replaceWith(html);
                            $('.button-add-to-cart').prop('disabled', true).addClass('disabled');

                            return;
                        }

                        $('.button-add-to-cart').prop('disabled', false).removeClass('disabled');

                        if (matchingVariant.special_price > 0) {
                            var html = `
                        <div class="product-price modal-price primary-color float-left">
                                <span class="current-price text-brand">$${matchingVariant.special_price}</span>
                                    <span>
                                        <span class="old-price font-md ml-15">$${matchingVariant.price}</span>
                                    </span>
                        </div>
                        `
                        } else {
                            var html = `
                        <div class="product-price modal-price primary-color float-left">
                            <span class="current-price text-brand">$${matchingVariant.price}</span>
                        </div>
                        `
                        }

                        $('.modal-price').replaceWith(html);
                    } else {
                        $('.button-add-to-cart').attr('data-variant', '');
                        if (variantsData.length > 0) {
                            $('.button-add-to-cart').prop('disabled', true).addClass('disabled');
                        }
                    }

                }

                selectDefaultVariant();
            }
        })

    $(function() {
        $('.wishlist-btn').on('click', function(e) {
            e.preventDefault();
            let productId = $(this).data('id');
            let element = $(this);

            $.ajax({
                url: "{{ route('wishlist.store') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: productId
                },
                beforeSend: function() {
                    // Optionally, show a loading indicator
                    element.html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`
                    );
                },
                success: function(response) {
                    if (response.type && response.type === 'add') {
                        element.html(`<i class="fi fi-ss-heart"></i>`);
                    } else {
                        element.html(`<i class="fi-rs-heart"></i>`);
                    }

                    notyf.success(response.message);
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON;

                    if (xhr.status === 401 || errors?.message === 'Unauthenticated.') {
                        notyf.error('Please login to continue.');
                        window.location.href = "{{ route('login') }}";
                    } else if (errors?.message) {
                        notyf.error(errors.message);
                    } else if (errors?.errors) {
                        Object.values(errors.errors).forEach(function(message) {
                            notyf.error(message[0]);
                        });
                    } else {
                        notyf.error("An error occurred. Please try again.");
                    }
                    element.html(`<i class="fi fi-rs-heart"></i>`);
                }
            })
        })


        // subscribe form
        $('.form-subcriber').on('submit', function(e) {
            e.preventDefault();
            let data = $(this).serialize();

            $.ajax({
                url: "{{ route('newsletter.subscribe') }}",
                method: "POST",
                data: data,
                beforeSend: function() {
                    // Optionally, show a loading indicator
                },
                success: function(response) {
                    notyf.success(response.message);
                    $('.form-subcriber')[0].reset();
                },
                error: function(xhr, status, error) {
                    let errors = xhr.responseJSON.errors;
                    if (errors) {
                        Object.values(errors).forEach(function(message) {

                            notyf.error(message[0]);
                        });
                    } else {
                        notyf.error("An error occurred. Please try again.");
                    }
                }
            })
        })
    })
</script>
