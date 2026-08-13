@extends('frontend.dashboard.dashboard-app')

@section('dashboard_contents')
    <div class="card">
        <div class="card-header p-0 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1">Add a New Address</h3>
                <p class="text-muted mb-0">This address will be available during checkout.</p>
            </div>
            <a href="{{ route('address.index') }}" class="btn btn-sm btn-light">Back</a>
        </div>
        <div class="card-body px-0 pt-4">
            @include('frontend.dashboard.address._form')
        </div>
    </div>
@endsection
