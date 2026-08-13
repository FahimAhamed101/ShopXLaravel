@extends('frontend.dashboard.dashboard-app')

@section('dashboard_contents')

<div class="tab-pane fade active show" id="orders" role="tabpanel" aria-labelledby="orders-tab">
    <div class="card">
        <div class="card-header p-0">
            <h3 class="mb-0">Your Purchased Products</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="order_table table m-0 mt-20">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Product</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchasedProducts as $product)
                        <tr>
                            <td>{{ $product->invoice_id ?: '#'.$product->order_id }}</td>
                            <td>{{ $product->product_name }}</td>
                            <td>{{ date('Y-m-d', strtotime($product->created_at)) }}</td>
                            <td>
                                @if ($product->product_type === 'digital')
                                    <a href="{{ route('purchased.products.show', $product->id) }}">Downloads</a>
                                @else
                                    <a href="{{ route('products.show', $product->slug) }}">View Product</a>
                                @endif
                            </td>

                        </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">No paid purchases found yet.</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $purchasedProducts->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
