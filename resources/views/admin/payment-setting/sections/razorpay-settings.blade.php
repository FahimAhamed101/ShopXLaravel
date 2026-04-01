@extends('admin.payment-setting.index')

@section('settings_contents')
    <div class="card-body">
        <h2 class="mb-4">Razorpay Settings</h2>

        <form action="{{ route('admin.razorpay-settings.store') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="form-label">Razorpay Status</div>
                     <select name="razorpay_status" class="form-control" id="">
                        <option @selected(config('settings.razorpay_status') == 'active') value="active">Active</option>
                        <option @selected(config('settings.razorpay_status') == 'inactive') value="inactive">Inactive</option>
                    </select>
                    <x-input-error :messages="$errors->get('razorpay_status')" class="mt-2" />
                </div>

                <div class="col-md-6">
                    <div class="form-label">Razorpay Currency</div>
                    <select name="razorpay_currency" class="form-control select2" id="">
                        @foreach(config('currencies') as $key => $currency)
                        <option @selected(config('settings.razorpay_currency') == $key) value="{{ $key }}">{{ $currency }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('razorpay_currency')" class="mt-2" />
                </div>


                <div class="col-md-6">
                    <div class="form-label">Razorpay Rate</div>
                    <input type="text" class="form-control" value="{{ config('settings.razorpay_rate') }}"
                        name="razorpay_rate">
                    <x-input-error :messages="$errors->get('razorpay_rate')" class="mt-2" />
                </div>

                <div class="col-md-6">
                    <div class="form-label">Razorpay Client Id</div>
                    <input type="text" class="form-control" value="{{ config('settings.razorpay_client_id') }}"
                        name="razorpay_client_id">
                    <x-input-error :messages="$errors->get('razorpay_client_id')" class="mt-2" />
                </div>

                <div class="col-md-6">
                    <div class="form-label">Razorpay Secret Key</div>
                    <input type="text" class="form-control" value="{{ config('settings.razorpay_secret') }}"
                        name="razorpay_secret">
                    <x-input-error :messages="$errors->get('razorpay_secret')" class="mt-2" />
                </div>


            </div>
            <div class="btn-list justify-content-end mt-5">
                <button type="submit" class="btn btn-primary btn-2"> Submit </button>
            </div>
        </form>

    </div>
@endsection
