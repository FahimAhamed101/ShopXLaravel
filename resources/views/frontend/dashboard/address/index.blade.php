@extends('frontend.dashboard.dashboard-app')

@section('dashboard_contents')
    <div class="tab-pane fade active show" id="address" role="tabpanel" aria-labelledby="address-tab">
        <div class="card mb-4">
            <div class="card-header p-0 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1">My Addresses</h3>
                    <p class="text-muted mb-0">Manage the billing and shipping addresses used at checkout.</p>
                </div>
                <a href="#add-address-form" class="btn btn-sm">Add Address</a>
            </div>

            <div class="card-body px-0 pt-4">
                <div class="row g-3">
                    @forelse ($addresses as $address)
                        <div class="col-lg-6">
                            <article class="border rounded p-3 h-100 {{ $address->is_default ? 'border-warning' : '' }}">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <h5 class="mb-2">
                                            {{ trim($address->first_name.' '.$address->last_name) }}
                                            @if ($address->is_default)
                                                <span class="badge bg-success ms-2">Default</span>
                                            @endif
                                        </h5>
                                        <address class="mb-2 text-muted" style="font-style: normal;">
                                            {{ $address->address }}<br>
                                            {{ $address->city }}, {{ $address->state }} {{ $address->zip }}<br>
                                            {{ $address->country }}
                                        </address>
                                        <div>{{ $address->phone }}</div>
                                        <div>{{ $address->email }}</div>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <a href="{{ route('address.edit', $address) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>

                                    @unless ($address->is_default)
                                        <form action="{{ route('address.default', $address) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Set as Default</button>
                                        </form>
                                    @endunless

                                    <form action="{{ route('address.destroy', $address) }}" method="POST"
                                        onsubmit="return confirm('Delete this address?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info mb-0">No saved address yet. Add your first address below.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card" id="add-address-form">
            <div class="card-header p-0">
                <h3 class="mb-1">Add a New Address</h3>
                <p class="text-muted mb-0">Fields marked with * are required for checkout.</p>
            </div>
            <div class="card-body px-0 pt-4">
                @include('frontend.dashboard.address._form')
            </div>
        </div>
    </div>
@endsection
