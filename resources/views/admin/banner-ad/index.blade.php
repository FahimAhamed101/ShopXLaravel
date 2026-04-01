@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Banner Ads</h3>

            </div>
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="v-pills-home-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-home" type="button" role="tab" aria-controls="v-pills-home"
                            aria-selected="true">Home banner section One</button>

                        <button class="nav-link" id="v-pills-tab" data-bs-toggle="pill" data-bs-target="#v-pills-home-two"
                            type="button" role="tab" aria-controls="v-pills-home" aria-selected="true">Home banner
                            section Two</button>

                        <button class="nav-link" id="v-pills-tab" data-bs-toggle="pill" data-bs-target="#v-pills-three"
                            type="button" role="tab" aria-controls="v-pills-home" aria-selected="true">Sidebar
                            Banners</button>

                    </div>
                    <div class="tab-content w-100" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel"
                            aria-labelledby="v-pills-home-tab" tabindex="0">
                            <div class="card mb-3">
                                <div class="card-header">
                                    Banner One
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.banners.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="banner_id" value="banner_one">
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Image</label>
                                                    <x-input-image imageUploadId="image-upload"
                                                        imagePreviewId="image-preview" imageLabelId="image-label"
                                                        name="image" :image="asset($banners['banner_one'][0]['image'])" />
                                                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="title"
                                                        placeholder="Title"
                                                        value="{{ $banners['banner_one'][0]['title'] ?? '' }}">
                                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Url</label>
                                                    <input type="text" class="form-control" name="url"
                                                        placeholder="url"
                                                        value="{{ $banners['banner_one'][0]['url'] ?? '' }}">
                                                    <x-input-error :messages="$errors->get('url')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header">
                                    Banner Two

                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.banners.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="banner_id" value="banner_two">

                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Image</label>
                                                    <x-input-image imageUploadId="image-upload-two"
                                                        imagePreviewId="image-preview-two" imageLabelId="image-label-two"
                                                        :image="asset($banners['banner_two'][0]['image'])" name="image" />
                                                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="title"
                                                        value="{{ $banners['banner_two'][0]['title'] ?? '' }}"
                                                        placeholder="Title">
                                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Url</label>
                                                    <input type="text" class="form-control" name="url"
                                                        value="{{ $banners['banner_two'][0]['url'] ?? '' }}"
                                                        placeholder="url">
                                                    <x-input-error :messages="$errors->get('url')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    Banner Three

                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.banners.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="banner_id" value="banner_three">
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Image</label>
                                                    <x-input-image imageUploadId="image-upload-three"
                                                        imagePreviewId="image-preview-three"
                                                        imageLabelId="image-label-three" name="image"
                                                        :image="asset($banners['banner_three'][0]['image'])" />
                                                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="title"
                                                        value="{{ $banners['banner_three'][0]['title'] ?? '' }}"
                                                        placeholder="Title">
                                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Url</label>
                                                    <input type="text" class="form-control" name="url"
                                                        value="{{ $banners['banner_three'][0]['url'] ?? '' }}"
                                                        placeholder="Url">
                                                    <x-input-error :messages="$errors->get('url')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="v-pills-home-two" role="tabpanel"
                            aria-labelledby="v-pills-home-two" tabindex="0">
                            <div class="row">
                                <div class="card mb-3 col-md-6">
                                    <div class="card-header">
                                        Banner One
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.banners.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <input type="hidden" name="banner_id" value="banner_four">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="" class="form-label">Image</label>
                                                        <x-input-image imageUploadId="image-upload-four"
                                                            imagePreviewId="image-preview-four"
                                                            imageLabelId="image-label-four" name="image"
                                                            :image="asset($banners['banner_four'][0]['image'])" />
                                                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="" class="form-label">Title</label>
                                                        <input type="text" class="form-control" name="title"
                                                            placeholder="Title"
                                                            value="{{ $banners['banner_four'][0]['title'] ?? '' }}">
                                                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="" class="form-label">Url</label>
                                                        <input type="text" class="form-control" name="url"
                                                            placeholder="url"
                                                            value="{{ $banners['banner_four'][0]['url'] ?? '' }}">
                                                        <x-input-error :messages="$errors->get('url')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="card mb-3 col-md-6">
                                    <div class="card-header">
                                        Banner Two

                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.banners.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <input type="hidden" name="banner_id" value="banner_five">

                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="" class="form-label">Image</label>
                                                        <x-input-image imageUploadId="image-upload-five"
                                                            imagePreviewId="image-preview-five"
                                                            imageLabelId="image-label-five" :image="asset($banners['banner_five'][0]['image'])"
                                                            name="image" />
                                                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="" class="form-label">Title</label>
                                                        <input type="text" class="form-control" name="title"
                                                            value="{{ $banners['banner_five'][0]['title'] ?? '' }}"
                                                            placeholder="Title">
                                                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="" class="form-label">Url</label>
                                                        <input type="text" class="form-control" name="url"
                                                            value="{{ $banners['banner_five'][0]['url'] ?? '' }}"
                                                            placeholder="url">
                                                        <x-input-error :messages="$errors->get('url')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="card col-md-6">
                                    <div class="card-header">
                                        Banner Three

                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.banners.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <input type="hidden" name="banner_id" value="banner_six">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="" class="form-label">Image</label>
                                                        <x-input-image imageUploadId="image-upload-six"
                                                            imagePreviewId="image-preview-six"
                                                            imageLabelId="image-label-six" name="image"
                                                            :image="asset($banners['banner_six'][0]['image'])" />
                                                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="" class="form-label">Title</label>
                                                        <input type="text" class="form-control" name="title"
                                                            value="{{ $banners['banner_six'][0]['title'] ?? '' }}"
                                                            placeholder="Title">
                                                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="" class="form-label">Url</label>
                                                        <input type="text" class="form-control" name="url"
                                                            value="{{ $banners['banner_six'][0]['url'] ?? '' }}"
                                                            placeholder="Url">
                                                        <x-input-error :messages="$errors->get('url')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="card col-md-6">
                                    <div class="card-header">
                                        Banner Four

                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.banners.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <input type="hidden" name="banner_id" value="banner_seven">
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="" class="form-label">Image</label>
                                                        <x-input-image imageUploadId="image-upload-seven"
                                                            imagePreviewId="image-preview-seven"
                                                            imageLabelId="image-label-seven" name="image"
                                                            :image="asset($banners['banner_seven'][0]['image'])" />
                                                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="" class="form-label">Title</label>
                                                        <input type="text" class="form-control" name="title"
                                                            value="{{ $banners['banner_seven'][0]['title'] ?? '' }}"
                                                            placeholder="Title">
                                                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <label for="" class="form-label">Url</label>
                                                        <input type="text" class="form-control" name="url"
                                                            value="{{ $banners['banner_seven'][0]['url'] ?? '' }}"
                                                            placeholder="Url">
                                                        <x-input-error :messages="$errors->get('url')" class="mt-2" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-3">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="tab-pane fade" id="v-pills-three" role="tabpanel"
                            aria-labelledby="v-pills-home-tab" tabindex="0">
                            <div class="card mb-3">
                                <div class="card-header">
                                    Side Banner One
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.banners.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="banner_id" value="side_banner_one">
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Image</label>
                                                    <x-input-image imageUploadId="image-upload-eight"
                                                        imagePreviewId="image-preview-eight"
                                                        imageLabelId="image-label-eight" name="image"
                                                        :image="asset($banners['side_banner_one'][0]['image'])" />
                                                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="title"
                                                        placeholder="Title"
                                                        value="{{ $banners['side_banner_one'][0]['title'] ?? '' }}">
                                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Url</label>
                                                    <input type="text" class="form-control" name="url"
                                                        placeholder="url"
                                                        value="{{ $banners['side_banner_one'][0]['url'] ?? '' }}">
                                                    <x-input-error :messages="$errors->get('url')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header">
                                    Banner Two

                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.banners.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="banner_id" value="side_banner_two">

                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Image</label>
                                                    <x-input-image imageUploadId="image-upload-nine"
                                                        imagePreviewId="image-preview-nine"
                                                        imageLabelId="image-label-nine" :image="asset($banners['side_banner_two'][0]['image'])"
                                                        name="image" />
                                                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="title"
                                                        value="{{ $banners['side_banner_two'][0]['title'] ?? '' }}"
                                                        placeholder="Title">
                                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Url</label>
                                                    <input type="text" class="form-control" name="url"
                                                        value="{{ $banners['side_banner_two'][0]['url'] ?? '' }}"
                                                        placeholder="url">
                                                    <x-input-error :messages="$errors->get('url')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    Banner Three

                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.banners.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="banner_id" value="side_banner_three">
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Image</label>
                                                    <x-input-image imageUploadId="image-upload-ten"
                                                        imagePreviewId="image-preview-ten" imageLabelId="image-label-ten"
                                                        name="image" :image="asset($banners['side_banner_three'][0]['image'])" />
                                                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="title"
                                                        value="{{ $banners['side_banner_three'][0]['title'] ?? '' }}"
                                                        placeholder="Title">
                                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <label for="" class="form-label">Url</label>
                                                    <input type="text" class="form-control" name="url"
                                                        value="{{ $banners['side_banner_three'][0]['url'] ?? '' }}"
                                                        placeholder="Url">
                                                    <x-input-error :messages="$errors->get('url')" class="mt-2" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group mb-3">
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
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

            $.uploadPreview({
                input_field: "#image-upload-three", // Default: .image-upload
                preview_box: "#image-preview-three", // Default: .image-preview
                label_field: "#image-label-three", // Default: .image-label
                label_default: "Choose File", // Default: Choose File
                label_selected: "Change File", // Default: Change File
                no_label: false // Default: false
            });

            $.uploadPreview({
                input_field: "#image-upload-four", // Default: .image-upload
                preview_box: "#image-preview-four", // Default: .image-preview
                label_field: "#image-label-four", // Default: .image-label
                label_default: "Choose File", // Default: Choose File
                label_selected: "Change File", // Default: Change File
                no_label: false // Default: false
            });

            $.uploadPreview({
                input_field: "#image-upload-five", // Default: .image-upload
                preview_box: "#image-preview-five", // Default: .image-preview
                label_field: "#image-label-five", // Default: .image-label
                label_default: "Choose File", // Default: Choose File
                label_selected: "Change File", // Default: Change File
                no_label: false // Default: false
            });

            $.uploadPreview({
                input_field: "#image-upload-six", // Default: .image-upload
                preview_box: "#image-preview-six", // Default: .image-preview
                label_field: "#image-label-six", // Default: .image-label
                label_default: "Choose File", // Default: Choose File
                label_selected: "Change File", // Default: Change File
                no_label: false // Default: false
            });

            $.uploadPreview({
                input_field: "#image-upload-seven", // Default: .image-upload
                preview_box: "#image-preview-seven", // Default: .image-preview
                label_field: "#image-label-seven", // Default: .image-label
                label_default: "Choose File", // Default: Choose File
                label_selected: "Change File", // Default: Change File
                no_label: false // Default: false
            });

            $.uploadPreview({
                input_field: "#image-upload-eight", // Default: .image-upload
                preview_box: "#image-preview-eight", // Default: .image-preview
                label_field: "#image-label-eight", // Default: .image-label
                label_default: "Choose File", // Default: Choose File
                label_selected: "Change File", // Default: Change File
                no_label: false // Default: false
            });

            $.uploadPreview({
                input_field: "#image-upload-nine", // Default: .image-upload
                preview_box: "#image-preview-nine", // Default: .image-preview
                label_field: "#image-label-nine", // Default: .image-label
                label_default: "Choose File", // Default: Choose File
                label_selected: "Change File", // Default: Change File
                no_label: false // Default: false
            });

            $.uploadPreview({
                input_field: "#image-upload-ten", // Default: .image-upload
                preview_box: "#image-preview-ten", // Default: .image-preview
                label_field: "#image-label-ten", // Default: .image-label
                label_default: "Choose File", // Default: Choose File
                label_selected: "Change File", // Default: Change File
                no_label: false // Default: false
            });
        });
    </script>
@endpush
