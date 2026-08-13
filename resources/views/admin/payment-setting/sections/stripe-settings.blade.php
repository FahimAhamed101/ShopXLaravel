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
                        <option @selected(setting('stripe_status') == 'active') value="active">Active</option>
                        <option @selected(setting('stripe_status') == 'inactive') value="inactive">Inactive</option>
                    </select>
                    <x-input-error :messages="$errors->get('stripe_status')" class="mt-2" />
                </div>

                <div class="col-md-6">
                    <div class="form-label">Stripe Mode</div>
                    <select name="stripe_mode" class="form-control" id="">
                        <option @selected(setting('stripe_mode') == 'sandbox') value="sandbox">Sandbox</option>
                        <option @selected(setting('stripe_mode') == 'live') value="live">Live</option>
                    </select>
                    <x-input-error :messages="$errors->get('stripe_mode')" class="mt-2" />
                </div>

                <div class="col-md-6">
                    <div class="form-label">Stripe Currency</div>
                    <select name="stripe_currency" class="form-control select2" id="">
                        @foreach(config('currencies') as $key => $currency)
                        <option @selected(setting('stripe_currency') == $key) value="{{ $key }}">{{ $currency }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('stripe_currency')" class="mt-2" />
                </div>


                <div class="col-md-6">
                    <div class="form-label">Stripe Rate</div>
                    <input type="text" class="form-control" value="{{ setting('stripe_rate') }}"
                        name="stripe_rate">
                    <x-input-error :messages="$errors->get('stripe_rate')" class="mt-2" />
                </div>

                <div class="col-md-6">
                    <div class="form-label">Stripe Publishable Key</div>
                    <input type="text" class="form-control" value="{{ setting('stripe_key') }}"
                        name="stripe_key" placeholder="pk_test_... or pk_live_...">
                    <x-input-error :messages="$errors->get('stripe_key')" class="mt-2" />
                </div>

                <div class="col-md-6">
                    <div class="form-label">Stripe Secret Key</div>
                    <input type="password" class="form-control" name="stripe_secret"
                        placeholder="Leave blank to keep the current secret">
                    <x-input-error :messages="$errors->get('stripe_secret')" class="mt-2" />
                </div>

                <div class="col-md-6">
                    <div class="form-label">Webhook Signing Secret</div>
                    <input type="password" class="form-control" name="stripe_webhook_secret"
                        placeholder="whsec_... (leave blank to keep current)">
                    <x-input-error :messages="$errors->get('stripe_webhook_secret')" class="mt-2" />
                    <small class="form-hint">
                        Recommended for reliable order fulfillment. Endpoint:
                        <code>{{ route('stripe.webhook') }}</code>
                    </small>
                </div>


            </div>
            <div class="btn-list justify-content-end mt-5">
                <button type="submit" class="btn btn-primary btn-2"> Submit </button>
            </div>
        </form>

    </div>
@endsection
