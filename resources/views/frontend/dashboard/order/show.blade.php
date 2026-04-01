@extends('frontend.dashboard.dashboard-app')

@section('dashboard_contents')
<div class="tab-pane fade active show" id="orders" role="tabpanel" aria-labelledby="orders-tab">
    <div class="card">
        <div class="card-header p-0 d-print-none">
            <h3 class="mb-0">Your Orders</h3>
        </div>
         <div class="card-body p-0">
                <div class="page-wrapper">
                    <!-- BEGIN PAGE HEADER -->
                    <div class="page-header d-print-none" aria-label="Page header">
                        <div class="container-xl">
                            <div class="row g-2 align-items-center">
                                <div class="col">

                                </div>
                                <!-- Page title actions -->
                                <div class="col-auto ms-auto d-print-none">
                                    <button type="button" class="btn btn-primary" onclick="javascript:window.print();">
                                        <!-- Download SVG icon from http://tabler.io/icons/icon/printer -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                            <path
                                                d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2">
                                            </path>
                                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                            <path
                                                d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z">
                                            </path>
                                        </svg>
                                        Print Invoice
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END PAGE HEADER -->
                    <!-- BEGIN PAGE BODY -->
                    <div class="page-body">
                        <div class="container-xl">
                            <div class="card card-lg">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="h3">Billing Information</p>
                                            <address>
                                                @php
                                                    $billingInfo = $order->billing_info;
                                                    $shippingInfo = $order->shipping_info;
                                                @endphp
                                                {{ $billingInfo['first_name'] }} {{ $billingInfo['last_name'] }}
                                                <br>
                                                {{ $billingInfo['address'] }}, {{ $billingInfo['city'] }},
                                                {{ $billingInfo['state'] }} <br> {{ $billingInfo['country'] }}
                                                <br>
                                                {{ $billingInfo['email'] }}
                                                <br>
                                                {{ $billingInfo['phone'] }}
                                            </address>
                                        </div>
                                        <div class="col-6 text-end">
                                            <p class="h3">Shipping Information</p>
                                            <address>
                                                @if ($shippingInfo)
                                                    {{ $shippingInfo['first_name'] }} {{ $shippingInfo['last_name'] }}
                                                    <br>
                                                    {{ $shippingInfo['address'] }}, {{ $shippingInfo['city'] }},
                                                    {{ $shippingInfo['state'] }} <br> {{ $shippingInfo['country'] }}
                                                    <br>
                                                    {{ $shippingInfo['email'] }}
                                                    <br>
                                                    {{ $shippingInfo['phone'] }}
                                                @else
                                                    {{ $billingInfo['first_name'] }} {{ $billingInfo['last_name'] }}
                                                    <br>
                                                    {{ $billingInfo['address'] }}, {{ $billingInfo['city'] }},
                                                    {{ $billingInfo['state'] }} <br> {{ $billingInfo['country'] }}
                                                    <br>
                                                    {{ $billingInfo['email'] }}
                                                    <br>
                                                    {{ $billingInfo['phone'] }}
                                                @endif
                                            </address>
                                        </div>
                                        <div class="col-12 my-5">
                                            <p class="text-bold" style="font-size: 20px; font-weight: 600; color:black">Invoice #{{ $order->id }}</p>
                                            <div class="d-flex gap-2 flex-column">
                                                <span>Transaction ID: {{ $order->transaction_id }}</span>
                                                <span>Payment Method: {{ $order->payment_method }}</span>
                                                <span>Order Date: {{ date('Y-m-d', strtotime($order->created_at)) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table table-transparent table-responsive">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 1%"></th>
                                                <th>Product</th>
                                                <th class="text-center" style="width: 5%">Qnt</th>
                                                <th class="text-end" style="width: 10%">Unit ({{ $order->currency }})</th>
                                                <th class="text-end" style="width: 10%">Amount ({{ $order->currency }})
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $subtotal = 0;
                                            @endphp
                                            @foreach ($order->orderProducts as $orderProduct)
                                                @php
                                                    $subtotal += $orderProduct->unit_price * $orderProduct->quantity;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>
                                                        <p class="strong mb-1">{{ $orderProduct->product->name }}</p>
                                                        <div class="text-secondary w-50 ">
                                                            {{ $orderProduct?->variant['name'] ?? '' }}</div>
                                                    </td>
                                                    <td class="text-center">{{ $orderProduct->quantity }}</td>
                                                    <td class="text-end">{{ $orderProduct->unit_price }}</td>
                                                    <td class="text-end">
                                                        {{ $orderProduct->unit_price * $orderProduct->quantity }}</td>
                                                </tr>
                                            @endforeach

                                            <tr>
                                                <td colspan="4" class="strong text-end">Subtotal</td>
                                                <td class="text-end">{{ $subtotal }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="strong text-end">Discount</td>
                                                <td class="text-end">{{ $order?->discount ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="strong text-end">Shipping</td>
                                                <td class="text-end">{{ $order->shipping_charge ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="font-weight-bold text-uppercase text-end">Total
                                                    Amount</td>
                                                <td class="font-weight-bold text-end">{{ $order->currency }}
                                                    {{ $order->total }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p class="text-secondary text-center mt-5">Thank you very much for doing business with
                                        us. We look forward to working with you again!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END PAGE BODY -->

                </div>
            </div>
    </div>
</div>

<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 16mm;
    }
    html, body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        height: 100% !important;
    }
    .container-xl, .page-wrapper, .page-body {
        max-width: 800px !important;
        width: 100% !important;
        margin: 0 auto !important;
        padding: 0 !important;
        box-sizing: border-box !important;
    }
    .card, .card-lg, .card-body {
        box-shadow: none !important;
        border: none !important;
        background: #fff !important;
        padding: 0 !important;
    }
    .d-print-none, .d-print-none * {
        display: none !important;
    }
    table.table {
        width: 100% !important;
        font-size: 13px !important;
        border-collapse: collapse !important;
    }
    table.table th, table.table td {
        padding: 6px 4px !important;
        border: 1px solid #ddd !important;
    }
    tr, td, th {
        page-break-inside: avoid !important;
    }
    .my-5, .mt-5, .mb-5 {
        margin-top: 1.5rem !important;
        margin-bottom: 1.5rem !important;
    }
    .h3 {
        font-size: 1.2rem !important;
    }
}
</style>
@endsection
