@extends('admin.layouts.app')

@section('contents')
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Tags</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <div class="table-responsive">
                        <table class="order_table table m-0 mt-20">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th style="width: 300px">Product</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex gap-2">

                                                <img style="width: 50px; height: 50px; object-fit: cover"
                                                    src="{{ asset($review->product->primaryImage->path) }}" alt="">
                                                <a href="{{ route('products.show', ['slug' => $review->product->slug]) }}">
                                                    <p>{{ truncate($review->product->name, 50) }}</p>
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            @for ($i = 1; $i <= $review->rating; $i++)
                                                <i style="color: gold" class="ti ti-star"></i>
                                            @endfor
                                        </td>
                                        <td>
                                            {{ $review->review }}
                                        </td>

                                        <td>
                                            {{ date('Y-m-d', strtotime($review->created_at)) }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.reviews.destroy', $review) }}" class="btn btn-danger btn-sm delete-item"><i class="ti ti-trash"></i></a>
                                        </td>

                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No data found</td></td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
