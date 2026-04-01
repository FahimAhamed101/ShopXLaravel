@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Orders</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Order Id</th>
                                <th>User</th>
                                <th>Transaction Id</th>
                                <th>Amount</th>
                                <th>Payment Status</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Store</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>
                                        <div>
                                            {{ $order->user?->name }}
                                        </div>
                                        <div>
                                            {{ $order->user?->email }}
                                        </div>
                                        <div>
                                            {{ $order->user?->phone }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $order->transaction_id }}
                                    </td>

                                    <td>
                                        {{ $order->currency }} {{ $order->total }}
                                    </td>
                                    <td>
                                        @if($order->payment_status == 'paid')
                                            <span class="badge bg-success-lt">Paid</span>
                                        @elseif($order->payment_status == 'pending')
                                            <span class="badge bg-warning-lt">Pending</span>
                                        @else
                                            <span class="badge bg-danger-lt">Failed</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $order->order_status }}
                                    </td>
                                    <td>
                                        {{ date('Y-m-d', strtotime($order->created_at)) }}
                                    </td>

                                    <td>
                                        {{ $order->store?->name }}
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary"><i class="ti ti-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No Orders</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
