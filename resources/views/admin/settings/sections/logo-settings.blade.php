@extends('admin.settings.index')

@section('settings_contents')
    <div class="card-body">
        <h2 class="mb-4">Site Settings</h2>

        <form action="{{ route('admin.logo-settings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-label">Site Logo</div>
                    <x-input-image imageUploadId="image-upload" imagePreviewId="image-preview" imageLabelId="image-label"
                        name="site_logo" :image="asset(config('settings.site_logo'))" />
                    <x-input-error :messages="$errors->get('site_logo')" class="mt-2" />
                </div>

                <div class="col-md-6">
                    <div class="form-label">Site Favicon</div>
                    <x-input-image imageUploadId="image-upload-two" imagePreviewId="image-preview-two" imageLabelId="image-label-two"
                        name="site_favicon" :image="asset(config('settings.site_favicon'))" />
                    <x-input-error :messages="$errors->get('site_favicon')" class="mt-2" />
                </div>

            </div>
            <div class="btn-list justify-content-end mt-5">
                <button type="submit" class="btn btn-primary btn-2"> Submit </button>
            </div>
        </form>

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
