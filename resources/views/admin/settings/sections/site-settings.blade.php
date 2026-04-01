@extends('admin.settings.index')

@section('settings_contents')
    <div class="card-body">
        <h2 class="mb-4">Site Settings</h2>

        <form action="{{ route('admin.site-settings.store') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="form-label">Site Short Description</div>
                    <input type="text" class="form-control " value="{{ config('settings.site_short_description') }}" name="site_short_description">
                    <x-input-error :messages="$errors->get('site_short_description')" class="mt-2" />
                </div>
                <div class="col-md-12">
                    <div class="form-label">Site Address</div>
                    <input type="text" class="form-control " value="{{ config('settings.site_address') }}" name="site_address">
                    <x-input-error :messages="$errors->get('site_address')" class="mt-2" />
                </div>

                <div class="col-md-12">
                    <div class="form-label">Site Hours</div>
                    <input type="text" class="form-control " value="{{ config('settings.site_hours') }}" name="site_hours">
                    <x-input-error :messages="$errors->get('site_hours')" class="mt-2" />
                </div>

                <div class="col-md-12">
                    <div class="form-label required">Copy Right</div>
                    <input type="text" class="form-control " value="{{ config('settings.site_copyright') }}" name="site_copyright">
                    <x-input-error :messages="$errors->get('site_copyright')" class="mt-2" />
                </div>

            </div>
            <div class="btn-list justify-content-end mt-5">
                <button type="submit" class="btn btn-primary btn-2"> Submit </button>
            </div>
        </form>

    </div>
@endsection
