@extends('vendor-dashboard.layouts.app')

@section('contents')
    <div class="container-xl">
        @if (auth('web')->user()->kyc?->status == 'pending')
            <div class="alert alert-important alert-warning alert-dismissible" role="alert">
                <div class="alert-icon">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/alert-triangle -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon alert-icon icon-2">
                        <path d="M12 9v4"></path>
                        <path
                            d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                        </path>
                        <path d="M12 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="alert-heading">Your KYC is Pending</h4>
                    <div class="alert-description">Please wait for the admin to approve your KYC.</div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif 
        @if (auth('web')->user()->kyc?->status == 'rejected' || auth('web')->user()->kyc?->status == null)
            <div class="alert alert-important alert-danger alert-dismissible" role="alert">
                <div class="alert-icon">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/alert-circle -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon alert-icon icon-2">
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                        <path d="M12 8v4"></path>
                        <path d="M12 16h.01"></path>
                    </svg>
                </div>
                <div class="w-100">
                    <h4 class="alert-heading">Please Submit Your KYC</h4>
                    <div class="alert-description d-flex justify-content-between align-items-center ">
                        <span>Please submit your KYC to get started.</span>
                        <div>
                            <a href="{{ route('kyc.index') }}" class="btn btn-6 btn-outline-light">Submit KYC</a>
                        </div>
                    </div>
                </div>
                <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
            </div>
        @endif

        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="row row-cards">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="bg-warning text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/currency-dollar -->
                                            <i class="ti ti-shopping-bag-exclamation"></i></span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">{{ $pendingOrders }} Orders</div>
                                        <div class="text-secondary">Total Pending Orders</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="bg-success text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/currency-dollar -->
                                            <i class="ti ti-shopping-bag-heart"></i></span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">{{ $completedOrders }} Orders</div>
                                        <div class="text-secondary">Total Completed Orders</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="bg-danger text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/currency-dollar -->
                                            <i class="ti ti-shopping-bag-x"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">{{ $canceledOrders }} Orders</div>
                                        <div class="text-secondary">Total Cancelled Orders</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="bg-primary text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/currency-dollar -->
                                            <i class="ti ti-shopping-bag-plus"></i></span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">{{ $totalOrders }} Orders</div>
                                        <div class="text-secondary">Total Orders</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="bg-success text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/currency-dollar -->
                                            <i class="ti ti-box"></i></span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">{{ $totalProducts }} Items</div>
                                        <div class="text-secondary">Total Products</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="bg-info text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/currency-dollar -->
                                            <i class="ti ti-box"></i></span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">{{ $totalDigitalProducts }} Items</div>
                                        <div class="text-secondary">Total Digital Products</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="bg-purple text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/currency-dollar -->
                                            <i class="ti ti-box"></i></span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">{{ $totalPhysicalProducts }} Items</div>
                                        <div class="text-secondary">Total Physical Products</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
