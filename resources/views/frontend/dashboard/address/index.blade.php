@extends('frontend.dashboard.dashboard-app')

@section('dashboard_contents')
    <div class="tab-pane fade active show" id="address" role="tabpanel" aria-labelledby="address-tab">
        <div class="wsus__shipping_address mb_40">
            <h4>Billing Address
                <a href="{{ route('address.create') }}" class="btn btn-primary">add new address</a>
            </h4>

            <div class="row">
                @forelse ($addresses as $address)
                <div class="col-md-6 col-lg-4 col-xl-4">
                    <div class="wsus__shipping_address_item">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1"
                                value="option1">
                            <label class="form-check-label" for="inlineRadio1">{{ $address->address }}, {{ $address->city }}, {{ $address->state }}, {{ $address->zip }}, {{ $address->country }} </label>
                        </div>
                        <div class="wsus__shipping_mail_address">
                            <a href="javascript:;">{{ $address->email }}</a>
                            <a href="javascript:;">{{ $address->phone }}</a>
                            @if($address->is_default == 1)
                            <span class="text-success">(Default)</span>
                            @endif
                        </div>
                        <ul class="btn_list">
                            <li>
                                <a href="{{ route('address.edit', $address) }}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('address.destroy', $address) }}" class="delete-item">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                @empty
                    <h6 class="text-center py-5">No address found</h6>
                @endforelse

            </div>

            <div class="panel-collapse collapse login_form" id="loginform">
                <div class="panel-body">
                    <h4>Add New Address</h4>
                    <form>
                        <div class="row mt-20">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="text" placeholder="Name ">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="email" placeholder="Email ">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" placeholder="Phone ">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea placeholder="Address" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <button class="btn btn-md" name="login">Save</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
