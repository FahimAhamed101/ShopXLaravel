@extends('frontend.layouts.app')

@section('contents')
    <x-frontend.breadcrumb :items="[['label' => 'Home', 'url' => '/'], ['label' => 'Flash Sale']]" />
    <div class="container mt-70">
        <div class="section-title wow animate__ animate__fadeIn animated"
            style="visibility: visible; animation-name: fadeIn;">
            <h3 class="">Flash Sale</h3>
            @if ($flashSale?->is_active && now()->between($flashSale->sale_start, $flashSale->sale_end->copy()->endOfDay()))
                <div class="flash_countdown">
                    <div class="deals-countdown" data-countdown="{{ $flashSale->sale_end->format('Y/m/d') }} 23:59:59">
                    </div>
                </div>
            @endif
            <div class="row mt-30">
                @forelse ($flashSaleProducts as $product)
                    <x-frontend.product-card :product="$product" class="col-6 col-md-4 col-lg-3 col-xl-3 col-xxl-2" />
                @empty
                    <div class="col-12"><p class="text-center">No flash sale products are available.</p></div>
                @endforelse
            </div>
            <div>
                {{ $flashSaleProducts->links() }}
            </div>
        </div>
    </div>
@endsection
