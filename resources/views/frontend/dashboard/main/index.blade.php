@extends('frontend.dashboard.dashboard-app')

@section('dashboard_contents')
<div class="tab-pane fade active show" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
    <div class="card">
        <div class="card-header p-0 pb-10">
            <h3 class="mb-0">Hello {{ user()->name }}!</h3>
        </div>
        <div class="card-body p-0">
            <p>
                From your account dashboard. you can easily check &amp; view your <a href="{{ route('orders.index') }}">recent
                    orders</a>,<br />
                manage your <a href="{{ route('address.index') }}">shipping and billing addresses</a> and <a href="{{ route('profile') }}">edit your password
                    and account details.</a>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-sm-6">
            <div class="dashboard_card blue">
                <span><i class="fa-solid fa-cart-shopping"></i></span>
                <h3>{{ $totalOrders }}</h3>
                <p>Total Order</p>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="dashboard_card red">
                <span><i class="fa-solid fa-xmark"></i></span>
                <h3>{{ $totalCanceledOrders }}</h3>
                <p>Cancel Order</p>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="dashboard_card orange">
                <span><i class="fa-solid fa-spinner"></i></span>
                <h3>{{ $totalPendingOrders }}</h3>
                <p>Pending Order</p>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="dashboard_card green">
                <span><i class="fa-solid fa-star"></i></span>
                <h3>{{ $totalReviews }}</h3>
                <p>Total Reviews</p>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="dashboard_card pink">
                <span><i class="fa-solid fa-location-dot"></i></span>
                <h3>{{ $totalAddresses }}</h3>
                <p>Total Addresses</p>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="dashboard_card purple">
                <span><i class="fi-rs-shopping-bag"></i></span>
                <h3>{{ $totalWishlists }}</h3>
                <p>Total Wishlist</p>
            </div>
        </div>
    </div>
</div>

@endsection
