@extends('admin.settings.index')

@section('settings_contents')
    <div class="card-body">
        <h2 class="mb-4">Commission Settings</h2>

        <form action="{{ route('admin.commission-settings.store') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="form-label required">Admin Commission Per Order (%)</div>
                    <input type="text" class="form-control " value="{{ config('settings.admin_commission') }}" name="admin_commission">
                    <x-input-error :messages="$errors->get('admin_commission')" class="mt-2" />
                </div>

            </div>
            <div class="btn-list justify-content-end mt-5">
                <button type="submit" class="btn btn-primary btn-2"> Submit </button>
            </div>
        </form>

    </div>
@endsection
