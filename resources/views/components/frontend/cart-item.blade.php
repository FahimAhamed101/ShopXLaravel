@forelse($cartItems as $cartItem)
    <tr>
        <td class="image product-thumbnail"><img
                src="{{ imageUrl($cartItem->product?->primaryImage?->path) }}" alt="#"></td>
        <td class="product-des product-name">
            <h6 class="mb-5"><a class="product-name mb-10 text-heading"
                    href="{{ $cartItem->product?->slug ? route('products.show', $cartItem->product->slug) : '#' }}">{{ $cartItem->product?->name }}</a></h6>
            <div class="product-rate-cover">
                <span>{{ $cartItem->product?->variants()->where('id', $cartItem->variant_id)->first()->name ?? '' }}</span>
            </div>
        </td>
        @php
            $price = $cartItem->product->getVariantOrProductPriceAndStock($cartItem->variant_id);
        @endphp
        @if ($price['in_stock'])
            <td class="price" data-title="Price">
                @if ($price['old_price'])
                    <h4 class="text-body">$ {{ $price['price'] }}</h4>
                    <h4 class="text-danger" style="font-size: 18px;text-decoration: line-through;">$
                        {{ $price['old_price'] }}</h4>
                @else
                    <h4 class="text-body">$ {{ $price['price'] }}</h4>
                @endif
            </td>
            <td class="text-center detail-info" data-title="Stock">
                <div class="detail-extralink mr-15">
                    <div class="detail-qty border radius">
                        <a href="#" class="qty-down"><i class="fi-rs-angle-small-down"></i></a>
                        <input type="text" data-cart-item="{{ $cartItem->id }}"
                            name="quantity" class="qty-val" value="{{ $cartItem->quantity }}"
                            min="1" readonly>
                        <a href="#" class="qty-up"><i class="fi-rs-angle-small-up"></i></a>
                    </div>
                </div>
            </td>
            <td class="price" data-title="Price">
                <h4 class="text-brand">$ {{ $price['price'] * $cartItem->quantity }} </h4>
            </td>
        @else
            <td colspan="3">
                <h4 class="text-brand">Out of stock</h4>
            </td>
        @endif
        <td class="action text-center" data-title="Remove">
            <form action="{{ route('cart.destroy', $cartItem->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-body border-0 bg-transparent p-0"
                    aria-label="Remove {{ $cartItem->product?->name }} from cart">
                    <i class="fi-rs-trash"></i>
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr class="pt-30">
        <td colspan="6" class="text-center">Cart is empty</td>
    </tr>
@endforelse
