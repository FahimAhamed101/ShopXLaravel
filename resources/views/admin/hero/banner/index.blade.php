@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create Slider</h3>
                <div class="card-actions">
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.hero-banners.store') }}" method="POST" class="coupon-form" enctype="multipart/form-data">
                    @csrf
                    <h3>Banner One</h3>
                    <div class="row">

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label required">Banner</label>
                                <x-input-image imageUploadId="image-upload" imagePreviewId="image-preview"
                                    imageLabelId="image-label" name="banner_one" :image="asset($heroBanner?->banner_one)" />
                                <x-input-error :messages="$errors->get('banner_one')" class="mt-2" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label required">Title</label>
                                <input type="text" class="form-control" name="title_one" placeholder="" value="{{ $heroBanner?->title_one }}">
                                <x-input-error :messages="$errors->get('title_one')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label required">Action Button Url</label>
                                <input type="text" class="form-control" name="btn_url_one" placeholder="" value="{{ $heroBanner?->btn_url_one }}">
                                <x-input-error :messages="$errors->get('btn_url_one')" class="mt-2" />
                            </div>
                        </div>

                    </div>

                    <h3>Banner Two</h3>
                    <div class="row">

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label required">Banner</label>
                                <x-input-image imageUploadId="image-upload-two" imagePreviewId="image-preview-two"
                                    imageLabelId="image-label-two" name="banner_two" :image="asset($heroBanner?->banner_two)" />
                                <x-input-error :messages="$errors->get('banner_two')" class="mt-2" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label required">Title</label>
                                <input type="text" class="form-control" name="title_two" placeholder="" value="{{ $heroBanner?->title_two }}">
                                <x-input-error :messages="$errors->get('title_two')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label required">Action Button Url</label>
                                <input type="text" class="form-control" name="btn_url_two" placeholder="" value="{{ $heroBanner?->btn_url_two }}">
                                <x-input-error :messages="$errors->get('btn_url_two')" class="mt-2" />
                            </div>
                        </div>

                    </div>


                </form>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary mt-3" onclick="$('.coupon-form').submit()">update</button>
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
    </script>
@endpush
