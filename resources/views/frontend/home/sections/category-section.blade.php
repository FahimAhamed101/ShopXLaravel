<section class="popular-categories section-padding">
    <div class="container wow animate__animated animate__fadeIn">
        <div class="section-title">
            <div class="title">
                <h3>Top Categories</h3>
            </div>
            <div class="slider-arrow slider-arrow-2 flex-right carausel-10-columns-arrow" id="carausel-10-columns-arrows">
            </div>
        </div>
        <div class="carausel-10-columns-cover position-relative">
            <div class="carausel-10-columns" id="carausel-10-columns">
                @foreach ($featuredCategories as $category)
                    <div class="card-2 bg-9 wow animate__animated animate__fadeInUp" data-wow-delay=".1s">
                        <figure class="img-hover-scale overflow-hidden">
                            <a href="{{ route('products.index', ['category' => $category->slug]) }}"><img
                                    src="{{ asset($category->image) }}" alt="" /></a>
                        </figure>
                        <h6><a
                                href="{{ route('products.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                        </h6>
                        <span>
                            {{ $category->products_count > 0 ? $category->products_count . ' items' : 'No items' }}
                        </span>
                        {{-- <span>{{ $category->products_count }} items</span> --}}

                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
