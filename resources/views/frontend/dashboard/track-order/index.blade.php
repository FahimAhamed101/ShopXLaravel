@extends('frontend.dashboard.dashboard-app')

@section('dashboard_contents')

    <div class="tab-pane fade active show" id="track-orders" role="tabpanel" aria-labelledby="track-orders-tab">
        <div class="card">
            <div class="card-header p-0">
                <h3 class="mb-0">Orders tracking</h3>
            </div>
            <div class="card-body p-0 contact-from-area">
                <p>To track your order please enter your OrderID in the box below and
                    press "Track" button. This was given to you on your receipt and in
                    the confirmation email you should have received.</p>
                <div class="row">
                    <div class="col-lg-8">
                        <form class="contact-form-style mt-30 mb-50" action="{{ route('track.order.index') }}"
                            method="GET">
                            <div class="input-style mb-20">
                                <label>Order ID</label>
                                <input name="order-id" placeholder="Order ID" type="text" />
                            </div>
                            <button class="btn" type="submit">Track</button>
                        </form>
                    </div>
                </div>
                @if ($order)
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="wsus__track_header">
                                <div class="wsus__track_header_text">
                                    <div class="row">
                                        <div class="col-xl-3 col-sm-6 col-lg-3">
                                            <div class="wsus__track_header_single">
                                                <h5>Purchased by:</h5>
                                                <p>{{ $order->user->name }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-sm-6 col-lg-3">
                                            <div class="wsus__track_header_single">
                                                <h5>Store:</h5>
                                                <p>{{ $order->store->name }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-sm-6 col-lg-3">
                                            <div class="wsus__track_header_single">
                                                <h5>Status:</h5>
                                                <p>{{ $order->order_status }}</p>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-sm-6 col-lg-3">
                                            <div class="wsus__track_header_single">
                                                <h5>tracking:</h5>
                                                <p>#{{ $order->id }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <ul class="pro_trckr">
                                @forelse($order->orderHistory as $orderHistory)
                                    <li class="check_mark">{{ $orderHistory->status }}</li>
                                @empty
                                    <li class="check_mark">Order pending</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="col-12">
                            <div class="col-12">
                                <div class="track_pro_table">
                                    <div class="table-responsive">
                                        <table class="table table-transparent table-responsive">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 1%"></th>
                                                    <th>Product</th>
                                                    <th class="text-center" style="width: 5%">Qnt</th>
                                                    <th class="text-end" style="width: 10%">Unit ({{ $order->currency }})
                                                    </th>
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
                                                        $subtotal +=
                                                            $orderProduct->unit_price * $orderProduct->quantity;
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
                                                    <td colspan="4" class="font-weight-bold text-uppercase text-end">
                                                        Total
                                                        Amount</td>
                                                    <td class="font-weight-bold text-end">{{ $order->currency }}
                                                        {{ $order->total }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
