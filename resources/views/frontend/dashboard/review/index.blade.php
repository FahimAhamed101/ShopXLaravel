@extends('frontend.dashboard.dashboard-app')

@section('dashboard_contents')

<div class="tab-pane fade active show" id="orders" role="tabpanel" aria-labelledby="orders-tab">
    <div class="card">
        <div class="card-header p-0">
            <h3 class="mb-0">Your Reviews</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="order_table table m-0 mt-20">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th style="width: 300px">Product</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex gap-2">

                                <img style="width: 50px; height: 50px; object-fit: cover" src="{{ asset($review->product->primaryImage->path) }}" alt="">
                                <a href="{{ route('products.show', ['slug' => $review->product->slug]) }}"><p>{{ truncate($review->product->name, 50) }}</p></a>
                                </div>
                            </td>
                            <td>
                                @for($i = 1; $i <= $review->rating; $i++)
                                <i style="color: gold" class="fa fa-star"></i>
                                @endfor
                            </td>
                            <td>
                                {{ $review->review }}
                            </td>

                            <td>
                                {{ date('Y-m-d', strtotime($review->created_at)) }}
                            </td>

                        </tr>
                        @endforeach

                    </tbody>
                </table>
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>

@endsection
