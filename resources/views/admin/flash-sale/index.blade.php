@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Update Flash Sale Items</h3>
                <div class="card-actions">
                </div>
            </div>
            <div class="card-body flash_sell_tags">
                <form action="{{ route('admin.flash-sales.store') }}" method="POST" class="coupon-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Sale Start</label>
                                <input type="text" class="form-control datepicker" name="sale_start" placeholder=""
                                    value="{{ $flashSale?->sale_start }}">
                                <x-input-error :messages="$errors->get('sale_start')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Sale End</label>
                                <input type="text" class="form-control datepicker" name="sale_end" placeholder=""
                                    value="{{ $flashSale?->sale_end }}">
                                <x-input-error :messages="$errors->get('sale_end')" class="mt-2" />
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label required">Sale Products</label>
                                <select name="products[]" class="form-control product-select" id="" multiple>
                                    @foreach ($products as $product)
                                        <option selected value="{{ $product->id }}"
                                            data-image="{{ asset($product->primaryImage->path) }}"="">{{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('products')" class="mt-2" />
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="mb-2">
                                <label class="form-check form-switch form-switch-3">
                                    <input class="form-check-input" type="checkbox" checked="" name="status"
                                        id="status" value="1">
                                    <span class="form-check-label">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>


                </form>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary mt-3" onclick="$('.coupon-form').submit()">Update</button>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $(".product-select").select2({
                ajax: {
                    url: "{{ route('admin.flash-sales.get-products') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {

                        params.page = params.page || 1;

                        return {
                            results: data.results,
                            pagination: data.pagination
                        };
                    },
                    cache: true
                },
                placeholder: 'Search for a repository',
                minimumInputLength: 1,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection,
                escapeMarkup: function(markup) {
                    return markup;
                }
            });

            function formatRepo(repo) {
                if (repo.loading) {
                    return repo.text;
                }

                let image = repo.image;
                if (!image && repo.id) {
                    let option = $('.product-select option[value="' + repo.id + '"]');
                    image = option.data('image');
                }

                let markup = `
                    <div class="d-flex flex-wrap align-items-center p-t">
                        ${image ? `<img src="${image}" class="rounded me-2" style="width: 20px; height: 20px object-fit: cover">` : ''}
                        <p class="fw-semibold"> ${repo.text} </p>
                    </div>
                `;

                return markup;
            }

            function formatRepoSelection(repo) {
                let image = repo.image;
                if (!image && repo.id) {
                    let option = $('.product-select option[value="' + repo.id + '"]');
                    image = option.data('image');
                }

                let markup = `
                    <div class="d-flex flex-wrap align-items-center p-t">
                        ${image ? `<img src="${image}" class="rounded me-2" style="width: 20px; height: 20px object-fit: cover">` : ''}
                        <p class="fw-semibold"> ${repo.text} </p>
                    </div>
                `;

                return markup;
            }
        })
    </script>
@endpush
