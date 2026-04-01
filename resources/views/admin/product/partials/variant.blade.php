<div class="accordion-item">
    <div class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#variant-{{ $variant->id }}" aria-expanded="false">
            {{ $variant->name }} @if($variant->is_default == 1) <span class="badge bg-primary text-white">default</span> @endif  @if($variant->is_active == 1) <span class="badge bg-success text-white">Active</span> @endif
            <div class="accordion-button-toggle">
                <!-- Download SVG icon from http://tabler.io/icons/icon/chevron-down -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-1">
                    <path d="M6 9l6 6l6 -6"></path>
                </svg>
            </div>
        </button>

    </div>
    <div id="variant-{{ $variant->id }}" class="accordion-collapse collapse" data-bs-parent="#accordion-default"
        style="">
        <div class="accordion-body">
            <form action="" class="variant-form">
                @csrf
                <div class="row">
                    <input type="hidden" name="variant_id" value="{{ $variant->id }}">

                    <div class="col-md-12">
                        <label for="" class="form-label">Sku</label>
                        <input type="text" class="form-control" value="{{ $variant->sku }}" name="variant_sku">
                    </div>

                    <div class="col-md-6">
                        <label for="" class="form-label">Price</label>
                        <input type="text" class="form-control" value="{{ $variant->price }}" name="variant_price">
                    </div>

                    <div class="col-md-6">
                        <label for="" class="form-label">Special Price</label>
                        <input type="text" class="form-control" value="{{ $variant->special_price }}"
                            name="variant_special_price">
                    </div>


                    <div class="col-md-12">
                        <label class="form-check mt-3">
                            <input class="form-check-input variant-manage-stock" type="checkbox" @checked($variant->manage_stock == 1) value="1" name="variant_manage_stock">
                            <span class="form-check-label">Manage Stock</span>
                        </label>
                        <div class="variant-quantity" style="{{ $variant->manage_stock == 1 ? '' : 'display:none' }}">
                            <label for="" class="form-label">Quantity</label>
                            <input type="text" class="form-control" value="{{ $variant->qty }}" name="variant_quantity">
                        </div>
                    </div>


                    <div class="col-md-12">
                        <div class="card my-3">
                            <div class="card-body">
                                <label for="" class="form-label">Stock Status</label>

                                <div class="d-flex gap-2">
                                    <label class="form-check">
                                        <input class="form-check-input" checked type="radio" @checked($variant->in_stock == 1) name="variant_stock_status"
                                            value="in_stock">
                                        <span class="form-check-label">In Stock</span>
                                    </label>
                                    <label class="form-check">
                                        <input class="form-check-input" type="radio" @checked($variant->in_stock == 0) name="variant_stock_status"
                                            value="out_of_stock">
                                        <span class="form-check-label">Out Of Stock</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">

                        <div class="d-flex gap-2">
                            <label class="form-check form-switch form-switch-3">
                                <input class="form-check-input" type="checkbox" @checked($variant->is_default == 1) value="1" name="variant_is_default">
                                <span class="form-check-label">Is Default</span>
                            </label>

                            <label class="form-check form-switch form-switch-3">
                                <input class="form-check-input" type="checkbox" @checked($variant->is_active == 1) value="1" name="variant_is_active">
                                <span class="form-check-label">Is Active</span>
                            </label>
                        </div>

                    </div>

                </div>

                <div class="mt-2">
                    <button class="btn  btn-success variant-save-btn" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
