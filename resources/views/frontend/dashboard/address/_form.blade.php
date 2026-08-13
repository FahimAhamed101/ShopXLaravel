@php
    $editing = isset($address) && $address->exists;
@endphp

<form action="{{ $editing ? route('address.update', $address) : route('address.store') }}" method="POST"
    class="address-form">
    @csrf
    @if (isset($returnTo))
        <input type="hidden" name="return_to" value="{{ $returnTo }}">
    @endif
    @if ($editing)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="first_name">First name <span class="text-danger">*</span></label>
            <input id="first_name" class="form-control" type="text" name="first_name"
                value="{{ old('first_name', $address->first_name ?? user()->name) }}" autocomplete="given-name" required>
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>
        <div class="col-md-6">
            <label class="form-label" for="last_name">Last name</label>
            <input id="last_name" class="form-control" type="text" name="last_name"
                value="{{ old('last_name', $address->last_name ?? '') }}" autocomplete="family-name">
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>
        <div class="col-md-6">
            <label class="form-label" for="address_email">Email <span class="text-danger">*</span></label>
            <input id="address_email" class="form-control" type="email" name="email"
                value="{{ old('email', $address->email ?? user()->email) }}" autocomplete="email" required>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="col-md-6">
            <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>
            <input id="phone" class="form-control" type="tel" name="phone"
                value="{{ old('phone', $address->phone ?? user()->phone) }}" autocomplete="tel" required>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>
        <div class="col-12">
            <label class="form-label" for="street_address">Street address <span class="text-danger">*</span></label>
            <textarea id="street_address" class="form-control" name="address" rows="3"
                autocomplete="street-address" required>{{ old('address', $address->address ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="city">City <span class="text-danger">*</span></label>
            <input id="city" class="form-control" type="text" name="city"
                value="{{ old('city', $address->city ?? '') }}" autocomplete="address-level2" required>
            <x-input-error :messages="$errors->get('city')" class="mt-2" />
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="state">State / district <span class="text-danger">*</span></label>
            <input id="state" class="form-control" type="text" name="state"
                value="{{ old('state', $address->state ?? '') }}" autocomplete="address-level1" required>
            <x-input-error :messages="$errors->get('state')" class="mt-2" />
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="zip">Postal code <span class="text-danger">*</span></label>
            <input id="zip" class="form-control" type="text" name="zip"
                value="{{ old('zip', $address->zip ?? '') }}" autocomplete="postal-code" required>
            <x-input-error :messages="$errors->get('zip')" class="mt-2" />
        </div>
        <div class="col-md-6 col-lg-3">
            <label class="form-label" for="country">Country <span class="text-danger">*</span></label>
            <select id="country" class="form-control" name="country" autocomplete="country-name" required>
                <option value="">Select country</option>
                @foreach (config('countries', []) as $country)
                    <option value="{{ $country }}" @selected(old('country', $address->country ?? '') === $country)>
                        {{ $country }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('country')" class="mt-2" />
        </div>
        <div class="col-12">
            <label class="form-check d-flex align-items-center gap-2">
                <input type="hidden" name="is_default" value="0">
                <input class="form-check-input" type="checkbox" name="is_default" value="1"
                    @checked(old('is_default', $address->is_default ?? false))>
                <span class="form-check-label">Use as my default billing and shipping address</span>
            </label>
            <x-input-error :messages="$errors->get('is_default')" class="mt-2" />
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-md">{{ $editing ? 'Update Address' : 'Save Address' }}</button>
        @if ($editing)
            <a href="{{ route('address.index') }}" class="btn btn-md btn-light">Cancel</a>
        @endif
    </div>
</form>
