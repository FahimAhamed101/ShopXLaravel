@extends('admin.layouts.app')
@push('styles')
    <style>
        .dd-item.custom-cat-item {
            border: none;
            padding: 0;
            margin-bottom: 0;
            background: none;
            border-radius: 0;
        }

        .dd-item-row.custom-cat-row {
            user-select: text;
            background: none;
            gap: 4px;
            border: 1px solid #e9ecef;
            min-height: 38px;
            display: flex;
            align-items: center;
            padding-left: 0.75rem;
            /* px-2 */
            padding-right: 0.75rem;
            padding-top: 0.25rem;
            /* py-1 */
            padding-bottom: 0.25rem;
        }

        .dd-handle.custom-cat-handle {
            cursor: move;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
            /* me-2 */
        }

        .cat-folder-icon {
            font-size: 16px;
            color: #6c757d;
        }

        .cat-label.custom-cat-label {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 2px;
            flex: 1 1 auto;
        }

        .dd-list .dd-list {
            padding-left: 50px;
        }

        .dd-item-row {
            margin-bottom: 5px;
        }

        .dd-item>button {
            height: 44px;
            margin: 0;
            border: 1px solid #e9ecef;
            background-color: #ededed;
        }
    </style>
@endpush
@section('contents')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Categories</span>
                        <button class="btn btn-primary" id="btn-new">New</button>
                    </div>
                    <div class="card-body">
                        <div id="category-tree" class="dd">

                        </div>
                        <div id="tree-loading" class="text-center my-2 d-none">
                            <div class="spinner-border"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><span id="category-title">Create Category</span></div>
                    <div class="card-body">
                        <form action="" id="category-form">
                            <input type="hidden" id="category-id">
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="mb-2">
                                        <label for="" class="form-label">Icon <span
                                                class="text-danger"></span></label>
                                        <x-input-image imageUploadId="image-upload" imagePreviewId="image-preview"
                                            imageLabelId="image-label" name="icon" />
                                    </div>
                                </div>
                                <div class="col-md-4">

                                    <div class="mb-2">
                                        <label for="" class="form-label">Image <span
                                                class="text-danger"></span></label>
                                        <x-input-image imageUploadId="image-upload-two" imagePreviewId="image-preview-two"
                                            imageLabelId="image-label-two" name="image" />
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required id="name">
                            </div>
                            <div class="mb-2">
                                <label for="" class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control" required id="slug">
                            </div>
                            <div class="mb-2">
                                <label for="" class="form-label">Parent Category <span
                                        class="text-danger">*</span></label>
                                <select name="parent_id" class="form-select" id="parent_id">
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label class="form-check form-switch form-switch-3">
                                            <input class="form-check-input" type="checkbox" name="is_featured"
                                                id="is_featured">
                                            <span class="form-check-label">is featured</span>
                                        </label>
                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label class="form-check form-switch form-switch-3">
                                            <input class="form-check-input" type="checkbox" checked="" name="is_active"
                                                id="is_active">
                                            <span class="form-check-label">Active</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" id="btn-save">Save</button>
                                <button type="button" class="btn btn-danger d-none" id="btn-delete">Delete</button>
                                <button type="button" class="btn btn-secondary" id="btn-cancel">Cancel</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $.uploadPreview({
                input_field: "#image-upload", // Default: .image-upload
                preview_box: "#image-preview", // Default: .image-preview
                label_field: "#image-label", // Default: .image-label
                label_default: "Choose File", // Default: Choose File
                label_selected: "Change File", // Default: Change File
                no_label: false // Default: false
            });

            $.uploadPreview({
                input_field: "#image-upload-two", // Default: .image-upload
                preview_box: "#image-preview-two", // Default: .image-preview
                label_field: "#image-label-two", // Default: .image-label
                label_default: "Choose File", // Default: Choose File
                label_selected: "Change File", // Default: Change File
                no_label: false // Default: false
            });
        });


        $(function() {

            function loadTree() {
                $('#tree-loading').removeClass('d-none');
                $.get("{{ route('admin.categories.nested') }}", function(data) {
                    $('#category-tree').empty();
                    var html = '<div class="dd" id="nestable-tree">' + renderTree(data) + '</div>';
                    $('#category-tree').html(html);
                    $('#nestable-tree').nestable({
                        maxDepth: 3
                    }).off('change').on('change', function(e) {
                        if (!$(e.target).hasClass('no-drag')) {
                            console.log(e);
                            updateOrder();
                        }
                    });
                    $('#tree-loading').addClass('d-none');
                })
            }

            function renderTree(categories) {
                if (!categories.length) return;
                let html = '<ol class="dd-list" style="margin-bottom: 0">';

                categories.forEach(function(cat) {
                    html += `<li class="dd-item custom-cat-item" data-id="${cat.id}">
                                    <div class="dd-item-row custom-cat-row">
                                        <div class="dd-handle custom-cat-handle" title="Drag to reorder">
                                            <i class="ti ti-grip-horizontal"></i>
                                        </div>
                                        <i class="ti ti-folder cat-folder-icon"></i>
                                        <div class="cat-label custom-cat-label" data-id="${cat.id}">
                                            <span>${cat.name}</span>
                                            ${cat.is_active ? '<span class="text-success ms-2" style="font-size: 10px">&#9679</span>' : '<span class="text-danger ms-2" style="font-size: 10px">&#9679</span>'}
                                        </div>
                                    </div>`;
                    if (cat.children_nested && cat.children_nested.length) {
                        html += renderTree(cat.children_nested);
                    }
                    html += `</li>`
                })

                html += '</ol>'

                return html;
            }

            function updateOrder() {
                let tree = $('#nestable-tree').nestable('serialize');
                $.post({
                    url: "{{ route('admin.categories.update-order') }}",
                    data: {
                        tree: tree,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            notyf.success(response.message);
                        }
                    },
                    error: function(xhr, status, error) {

                    }
                })
            }


            $('#category-form').submit(function(e) {
                e.preventDefault();
                let id = $('#category-id').val();
                let method = id ? 'PUT' : 'POST';
                let url = id ? "{{ route('admin.categories.update', ':id') }}".replace(':id', id) :
                    "{{ route('admin.categories.store') }}";

                let formData = new FormData();
                formData.append('name', $('#name').val());
                formData.append('slug', $('#slug').val());
                formData.append('parent_id', $('#parent_id').val());
                formData.append('is_active', $('#is_active').is(':checked') ? 1 : 0);
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('is_featured', $('#is_featured').is(':checked') ? 1 : 0);

                let iconFile = $('#image-upload')[0].files[0];
                if (iconFile) {
                    formData.append('icon', iconFile);
                }

                let imageFile = $('#image-upload-two')[0].files[0];
                if (imageFile) {
                    formData.append('image', imageFile);
                }

                if(id) {
                    formData.append('_method', 'PUT');
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        loadTree();
                        if(response.type != 'update') clearForm();

                        notyf.success(response.message);

                    },
                    error: function(xhr, status, error) {
                        console.log(xhr);
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            notyf.error(errors[key][0]);
                        })
                    }
                })
            });



            // load parent dropdown
            function loadParentDropdown(selectedId, excludeId) {
                $.get("{{ route('admin.categories.nested') }}", function(data) {
                    let options = '<option value="">None (Root)</option>';

                    function addOptions(cats, prefix, depth) {
                        cats.forEach(function(cat) {
                            if (cat.id == excludeId) return;
                            options +=
                                `<option value="${cat.id}" ${selectedId == cat.id ? 'selected' : '' } > ${prefix}${cat.name}</option>`;
                            if (cat.children_nested && cat.children_nested.length) {
                                addOptions(cat.children_nested, prefix + '--', depth + 1);
                            }
                        })
                    }

                    addOptions(data, '', 0);

                    $('#parent_id').html(options);

                })
            }


            // delete category

            $('#btn-delete').on('click', function() {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        let id = $('#category-id').val();
                        $.ajax({
                            url: "{{ route('admin.categories.destroy', ':id') }}".replace(
                                ':id', id),
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                if (response.success) {
                                    clearForm();
                                    loadTree();
                                    notyf.success(response.message);
                                }
                            },
                            error: function(xhr, status, error) {

                                if (xhr.responseJSON.error) {
                                    notyf.error(xhr.responseJSON.message);
                                }
                            }
                        })
                    }
                });
            })

            $(document).off('click', '.cat-label').on('click', '.cat-label', function(e) {
                e.stopPropagation();
                clearForm();
                let id = $(this).data('id');
                $.get("{{ route('admin.categories.show', ':id') }}".replace(':id', id), function(cat) {
                    fillForm(cat);
                });
            })

            // slug auto-generate
            $('#name').on('input', function() {
                if (!$('#category-id').val()) {
                    $('#slug').val(slugify($(this).val()));
                }
            })


            function slugify(text) {
                return text.toString().toLowerCase().replace(/\s+/g, '-')
                    .replace(/[^a-z0-9\-]/g, '')
                    .replace(/\-+/g, '-')
                    .replace(/^\-+|\-+$/g, '');
            }



            function fillForm(cat) {
                let domain = "{{ url('/') }}";
                $('#category-title').text('Edit Category');
                $('#name').val(cat.name);
                $('#slug').val(cat.slug);
                $('#is_active').prop('checked', cat.is_active);
                loadParentDropdown(cat.parent_id, cat.id);
                $('#category-id').val(cat.id);
                $('#btn-delete').removeClass('d-none');
                $('#is_featured').prop('checked', cat.is_featured);
                $('#image-preview').css('background-image', cat.icon ? `url(${domain}/${cat.icon})` : '');
                $('#image-preview-two').css('background-image', cat.image ? `url(${domain}/${cat.image})` : '');
            }

            // clear form
            function clearForm() {
                $('#category-form')[0].reset();
                $('#category-title').text('Create Category');
                $('#name').val('');
                $('#slug').val('');
                $('#parent_id').val('');
                $('#is_active').prop('checked', true);
                loadParentDropdown(null, null);
                $('#category-id').val('');
                $('#btn-delete').addClass('d-none');
                $('#is_featured').prop('checked', false);
                $('.image-preview').css('background-image', 'none');
            }

            $('#btn-new').on('click', function() {
                clearForm();
            })

            $('#btn-cancel').on('click', function() {
                clearForm();
            })

            // Initial Load
            clearForm();
            loadTree();
        })
    </script>
@endpush
