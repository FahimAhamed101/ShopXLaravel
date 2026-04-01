<div class="accordion-item">
    <div class="accordion-header">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse-{{ $attribute->id }}" aria-expanded="false">
            {{ $attribute->name }}
            <div class="accordion-button-toggle">
                <!-- Download SVG icon from http://tabler.io/icons/icon/chevron-down -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-1">
                    <path d="M6 9l6 6l6 -6"></path>
                </svg>
            </div>
        </button>
        <span class="delete-btn" data-product-id="{{ $product->id }}" data-attribute-id="{{ $attribute->id }}"
            style="padding: 5px; background: red; color: white; border-radius: 5px; margin-right: 10px;"><i
                class="ti ti-trash"></i></span>
    </div>
    <div id="collapse-{{ $attribute->id }}" class="accordion-collapse collapse" data-bs-parent="#accordion-default"
        style="">
        <div class="accordion-body">
            <form action="" class="attribute-form">
                @csrf
            <div class="row">
                <div class="col-md-6">
                    <label for="" class="form-label">Name</label>
                    <input type="text" class="form-control" value="{{ $attribute->name }}" name="attribute_name">
                    <input type="hidden" value="{{ $attribute->id }}" name="attribute_id">
                </div>
                <div class="col-md-6">
                    <label for="" class="form-label">Type</label>
                    <select name="attribute_type" class="form-control main-type" id="">
                        <option value="text" @selected($attribute->type == 'text')>Text</option>
                        <option value="color" @selected($attribute->type == 'color')>Color</option>
                    </select>
                </div>
            </div>
            <table class="table table-bordered section-table mt-3"
                style="{{ count($attribute->values) ? '' : 'display: none;' }}">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th class="value-header">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($attribute->type == 'color')
                    @foreach ($attribute->values as $value)
                    <tr>
                                <td>
                                    <input type="text" name="label[]" id="" class="form-control label-input"
                                        class="Label" value="{{ $value->value }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div id="pickr-{{ $value->id }}" class="color-preview" style="background-color: {{ $value->color }}"> </div>
                                        <input type="hidden" class="color-value" data-picker-id="pickr-{{ $value->id }}"
                                            name="color_value[]" value="{{ $value->color }}">
                                        <span class="review-row-btn ms-2"><i class="ti ti-trash"></i></span>
                                    </div>

                                </td>
                            </tr>
                            @endforeach

                    @else
                    @foreach ($attribute->values as $value)
                    <tr>
                        <td colspan="2">
                            <div class="d-flex justify-content-between align-items-center">
                                <input type="text" class="form-control label-input" name="label[]" placeholder="Label" value="{{ $value->value }}">
                                <span class="review-row-btn ms-2"><i class="ti ti-trash"></i></span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @endif

                </tbody>
            </table>
            <div class="mt-2">
                <button class="btn btn-sm btn-primary add-row-btn" type="button">Add Row</button>
                <button class="btn btn-sm btn-success save-btn" type="button">Save</button>
            </div>
        </form>
        </div>
    </div>
</div>
