@extends('frontend.dashboard.dashboard-app')

@section('dashboard_contents')
    <div class="wsus__shipping_address mb_40">
        <h4>Billing Address
            <a href="{{ route('address.index') }}" class="btn btn-primary">Back</a>
        </h4>

        {{-- <div class="row">
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="wsus__shipping_address_item">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1"
                            value="option1">
                        <label class="form-check-label" for="inlineRadio1">98 Winn
                            St, Woburn, MA
                            01801,USA</label>
                    </div>
                    <div class="wsus__shipping_mail_address">
                        <a href="mailto:example@gmail.com">example@gmail.com</a>
                        <a href="callto:+(402)76328246">+(402) 763 282 46</a>
                    </div>
                    <ul class="btn_list">
                        <li>
                            <a href="dashboard_address_edit.html">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="wsus__shipping_address_item">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2"
                            value="option2">
                        <label class="form-check-label" for="inlineRadio2">98 Winn
                            St, Woburn, MA 01801,
                            USA</label>
                    </div>
                    <div class="wsus__shipping_mail_address">
                        <a href="mailto:example@gmail.com">example@gmail.com</a>
                        <a href="callto:+(402)76328246">+(402) 763 282 46</a>
                    </div>
                    <ul class="btn_list">
                        <li>
                            <a href="dashboard_address_edit.html">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-4">
                <div class="wsus__shipping_address_item">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3"
                            value="option3">
                        <label class="form-check-label" for="inlineRadio3">98 Winn
                            St, Woburn, MA 01801,
                            USA</label>
                    </div>
                    <div class="wsus__shipping_mail_address">
                        <a href="mailto:example@gmail.com">example@gmail.com</a>
                        <a href="callto:+(402)76328246">+(402) 763 282 46</a>
                    </div>
                    <ul class="btn_list">
                        <li>
                            <a href="#">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div> --}}

        <div class=" login_form" id="loginform">
            <div class="panel-body">
                <h4>Add New Address</h4>
                <form action="{{ route('address.store') }}" method="POST">
                    @csrf
                    <div class="row mt-20">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" placeholder="First Name" name="first_name">
                                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" placeholder="Last Name" name="last_name">

                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" placeholder="Phone " name="phone">
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" placeholder="Email " name="email">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" placeholder="City" name="city">
                                <x-input-error :messages="$errors->get('city')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" placeholder="State" name="state">
                                <x-input-error :messages="$errors->get('state')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" placeholder="Zip" name="zip">
                                <x-input-error :messages="$errors->get('zip')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="country" class="form-control select-active" id="">
                                    <option value="">Country</option>
                                    @foreach (config('countries') as $country)
                                        <option value="{{ $country }}">{{ $country }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('country')" class="mt-2" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="text" placeholder="Address" name="address">
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <select name="is_default" class="form-control">
                                    <option value="">Is Default</option>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                                <x-input-error :messages="$errors->get('is_default')" class="mt-2" />
                            </div>
                        </div>

                    </div>
                    <div class="form-group mb-0">
                        <button class="btn btn-md">Save</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
