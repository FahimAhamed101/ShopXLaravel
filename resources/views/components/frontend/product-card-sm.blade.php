@props(['product'])

@php
    $productUrl = filled($product?->slug) ? route('products.show', $product->slug) : '#';
    $imagePath = $product?->image;

    if (method_exists($product, 'primaryImage')) {
        $imagePath = $product?->primaryImage?->path ?? $imagePath;
    } elseif (method_exists($product, 'images')) {
        $imagePath = $product?->images?->first()?->path ?? $imagePath;
    }

    $price = method_exists($product, 'getEffectivePriceAndStock')
        ? $product->getEffectivePriceAndStock()
        : [
            'price' => $product?->price ?? 0,
            'old_price' => $product?->old_price ?? 0,
            'in_stock' => true,
        ];
@endphp

<article class="row align-items-center hover-up">
    <figure class="col-sm-4 mb-0">
        <a href="{{ $productUrl }}">
            <img src="{{ $imagePath ? asset($imagePath) : asset('assets/frontend/dist/imgs/shop/product-1-1.jpg') }}" alt="{{ $product?->name }}" />
        </a>
    </figure>
    <div class="col-sm-8 mb-0">
        <h6>
            <a href="{{ $productUrl }}">{{ $product?->name }}</a>
        </h6>
        <div class="product-rate-cover">
            <div class="product-rate d-inline-block">
                <div class="product-rating" style="width: {{ ratingPercent($product?->reviews_avg_rating ?? 0) }}%"></div>
            </div>
            <span class="font-small ml-5 text-muted">({{ round($product?->reviews_avg_rating ?? 0, 2) }})</span>
        </div>
        <div class="product-price">
            @if ($price['in_stock'] ?? true)
                <span>${{ $price['price'] ?? 0 }}</span>
                @if (($price['old_price'] ?? 0) > 0)
                    <span class="old-price">${{ $price['old_price'] }}</span>
                @endif
            @else
                <span class="text-danger">Out of stock</span>
            @endif
        </div>
    </div>
</article>
