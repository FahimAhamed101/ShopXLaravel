@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
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
                                            class="bg-warning text-white avatar"><!-- Download SVG icon from http://tabler.io/icons/icon/currency-dollar -->
                                            <i class="ti ti-box"></i></span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">{{ $totalPendingProducts }} Items</div>
                                        <div class="text-secondary">Total Pending Products</div>
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
                                        <div class="font-weight-medium">{{ $totalApprovedProducts }} Items</div>
                                        <div class="text-secondary">Total Approved Products</div>
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
                                            <i class="ti ti-box"></i></span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">{{ $totalRejectedProducts }} Items</div>
                                        <div class="text-secondary">Total Rejected Products</div>
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
                                        <span class="bg-warning text-white avatar">
                                            <i class="ti ti-user"></i></span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">{{ $totalPendingKycRequests }} Kyc's</div>
                                        <div class="text-secondary">Total Pending Requests</div>
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
                                        <span class="bg-success text-white avatar">
                                            <i class="ti ti-user-check"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">{{ $totalApprovedKycRequests }} Kyc's</div>
                                        <div class="text-secondary">Total Approved Requests</div>
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
                                        <span class="bg-danger text-white avatar">
                                            <i class="ti ti-user-x"></i></span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">{{ $totalRejectedKycRequests }} Kyc's</div>
                                        <div class="text-secondary">Total Rejected Requests</div>
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
                                        <span class="bg-purple text-white avatar">
                                            <i class="ti ti-user"></i></span>
                                    </div>
                                    <div class="col">
                                        <div class="font-weight-medium">{{ $totalKycRequests }} Kyc's</div>
                                        <div class="text-secondary">Total Requests</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
            <div class="col-12">

                <div class="row row-cards">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h3>Analytics</h3>

                                <div class="card-actions">
                                    <form action="">
                                        <select name="month" class="form-control" onchange="this.form.submit()">
                                            @foreach ($months as $m)
                                                <option value="{{ $m['value'] }}" @selected($m['value'] == $month)>
                                                    {{ $m['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            </div>

                            <div class="card-body">
                                <div id="ordersChart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>Sales & Revenue ({{ date('Y') }})</h3>
                            </div>

                            <div class="card-body " style="padding-top:40px; padding-bottom:73px">
                                <div id="yearlyDonutChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="row row-cards">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>Pending Kyc's</h3>
                            </div>
                            <div class="card-body">
                                <div class="divide-y">
                                    @foreach ($pendingKycs as $kyc)
                                        <div>
                                            <div class="row">
                                                <div class="col-auto">
                                                    <span class="avatar avatar-1"
                                                        style="background-image: url({{ asset($kyc->user->avatar) }})">
                                                    </span>
                                                </div>
                                                <div class="col">
                                                    <div class="text-truncate">{{ $kyc->full_name }}</div>
                                                    <div class="text-secondary">{{ $kyc->created_at->diffForHumans() }}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>Pending Orders</h3>
                            </div>
                            <div class="card-body">
                                <div class="divide-y">
                                    @foreach ($recentPendingOrders as $order)
                                        <div>
                                            <div class="row">
                                                <div class="col">
                                                    <a href="{{ route('admin.orders.show', $order->id) }}">
                                                        <div class="text-truncate">#{{ $order->id }} -
                                                            {{ $order->customer_first_name }} <span
                                                                class="text-secondary">({{ $order->customer_email }})</span>
                                                        </div>
                                                    </a>
                                                    <div class="text-secondary">{{ $order->created_at->diffForHumans() }}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>Pending Products</h3>
                            </div>
                            <div class="card-body">
                                <div class="divide-y">
                                    @foreach ($pendingProducts as $product)
                                        <div>
                                            <div class="row">
                                                <div class="col">
                                                    @if ($product->product_type == 'physical')
                                                        <a href="{{ route('admin.products.edit', $product->id) }}">
                                                            <div class="text-truncate">{{ $product->name }}</div>
                                                        </a>
                                                    @else
                                                        <a
                                                            href="{{ route('admin.digital-products.edit', $product->id) }}">
                                                            <div class="text-truncate">{{ $product->name }}</div>
                                                        </a>
                                                    @endif
                                                    <div class="text-secondary">
                                                        {{ $product->created_at->diffForHumans() }}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var options = {
            chart: {
                height: 400,
                type: 'line'
            },
            series: [{
                name: "Orders",
                type: 'column',
                data: @json($ordersData)
            }, {
                name: "Total Amount",
                type: 'line',
                data: @json($amountData)
            }, {
                name: "Commission",
                type: 'line',
                data: @json($commissionData)
            }],
            xaxis: {
                categories: @json($dates)
            },
            yaxis: [{
                    seriesName: "Orders",
                    title: {
                        text: "Orders"
                    },
                    labels: {
                        formatter: function(val) {
                            return val.toFixed(0);
                        }
                    }
                },
                {
                    seriesName: "Total Amount",
                    opposite: true, // shows on right side
                    title: {
                        text: "Total Amount"
                    },
                    labels: {
                        formatter: function(val) {
                            return val.toFixed(2);
                        }
                    }
                },
                {
                    seriesName: "Commission",
                    opposite: true,
                    show: false // hide axis (shares same scale as Total Amount)
                }
            ],
            stroke: {
                width: [0, 3, 3],
                curve: 'smooth'
            },
            markers: {
                size: 4
            },
            colors: ['#1E90FF', '#FF4500', '#32CD32'],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val) {
                        return val.toFixed(2);
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#ordersChart"), options);
        chart.render();

        var donutOptions = {
            chart: {
                type: 'donut',
                width: 400
            },
            series: [{{ $totalSales }}, {{ $totalCommission }}],
            labels: ['Total Sales (Orders)', 'Revenue (Commission)'],
            colors: ['#008FFB', '#00E396'],
            legend: {
                position: 'bottom'
            },

            tooltip: {
                y: {
                    formatter: function(val, {
                        seriesIndex
                    }) {
                        if (seriesIndex == 0) {
                            return "{{ config('settings.site_currency_icon') }}" + val;
                        }

                        return "{{ config('settings.site_currency_icon') }}" + val.toFixed(2);
                    }
                }
            }

        }

        var donutChart = new ApexCharts(document.querySelector("#yearlyDonutChart"), donutOptions);
        donutChart.render();
    </script>
@endpush
