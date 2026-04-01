@extends('admin.payment-setting.index')

@section('settings_contents')
    <div class="card-body">
        <h2 class="mb-4">Stripe Settings</h2>

        <form action="{{ route('admin.stripe-settings.store') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-label">Stripe Status</div>
                     <select name="stripe_status" class="form-control" id="">
                        <option @selected(config('settings.stripe_status') == 'active') value="active">Active</option>
                        <option @selected(config('settings.stripe_status') == 'inactive') value="inactive">Inactive</option>
                    </select>
                    <x-input-error :messages="$errors->get('stripe_status')" class="mt-2" />
                </div>

                <div class="col-md-6">
                    <div class="form-label">Stripe Mode</div>
                    <select name="stripe_mode" class="form-control" id="">
                        <option @selected(config('settings.stripe_mode') == 'sandbox') value="sandbox">Sandbox</option>
                        <option @selected(config('settings.stripe_mode') == 'live') value="live">Live</option>
                    </select>
                    <x-input-error :messages="$errors->get('stripe_mode')" class="mt-2" />
                </div>

                <div class="col-md-6">
                    <div class="form-label">Stripe Currency</div>
                    <select name="stripe_currency" class="form-control select2" id="">
                        @foreach(config('currencies') as $key => $currency)
                        <option @selected(config('settings.stripe_currency') == $key) value="{{ $key }}">{{ $currency }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('stripe_currency')" class="mt-2" />
                </div>


                <div class="col-md-6">
                    <div class="form-label">Stripe Rate</div>
                    <input type="text" class="form-control" value="{{ config('settings.stripe_rate') }}"
                        name="stripe_rate">
                    <x-input-error :messages="$errors->get('stripe_rate')" class="mt-2" />
                </div>

                <div class="col-md-6">
                    <div class="form-label">Stripe Client Id</div>
                    <input type="text" class="form-control" value="{{ config('settings.stripe_client_id') }}"
                        name="stripe_client_id">
                    <x-input-error :messages="$errors->get('stripe_client_id')" class="mt-2" />
                </div>

                <div class="col-md-6">
                    <div class="form-label">Stripe Secret Key</div>
                    <input type="text" class="form-control" value="{{ config('settings.stripe_secret') }}"
                        name="stripe_secret">
                    <x-input-error :messages="$errors->get('stripe_secret')" class="mt-2" />
                </div>


            </div>
            <div class="btn-list justify-content-end mt-5">
                <button type="submit" class="btn btn-primary btn-2"> Submit </button>
            </div>
        </form>

    </div>
@endsection
