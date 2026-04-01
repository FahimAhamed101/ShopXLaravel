@extends('frontend.layouts.app')

@section('contents')

    <x-frontend.breadcrumb :items="[['label' => 'Home', 'url' => '/'], ['label' => 'Payment Success']]" />
    <div class="container mb-60 mt-55">
        <div class="text-center mt-100 mb-100">
            <i class="fa-solid fa-circle-check fa-10x text-success"></i>
            <h1>Payment Success</h1>
            <p>Your payment has been successfully completed</p>
            <a href="{{ route('dashboard') }}" class="btn btn-success mt-10">Go To Dashboard</a>
        </div>
    </div>
@endsection

