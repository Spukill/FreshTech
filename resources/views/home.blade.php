@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="categor d-flex flex-column flex-md-row m-0">
<div class="img-category d-flex flex-column align-items-start justify-content-end ps-4 pe-5 pb-2">
    <h3 class="text-left text-light t-ca">Innovative Technology</h3>
    <h3 class="text-left text-light fs-5 title">Experience Tomorrows's Technology Today</h3>
    <h3 class="text-left p-2 bg-primary text-light fs-5 title">Featured Categories</h3>
</div>
<div class="d-flex justify-content-around mt-0 flex-grow-1 pt-5 pb-5 flex-wrap">
    @foreach($categories as $category)
        @php
            // simple filename: lowercase and remove spaces
            $fileName = str_replace(' ', '', strtolower($category->name));
            $imagePath = "images/categories/{$fileName}.png";
        @endphp
        <!-- This will assign each category to its image (e.g Laptops category gets laptops.png image)-->
        <a href="{{ route('catalog', ['category' => $category->id]) }}" class="text-decoration-none text-center d-block text-black">
            @if (file_exists(public_path($imagePath)))
                <div class="rounded-circle bg-light mx-auto mb-3 d-flex align-items-center justify-content-center category-bubble cat"
                     style="background-image: url('{{ asset($imagePath) }}'); background-size: cover; background-position: center;">
                </div>
            @else
                <div class="rounded-circle bg-light mx-auto mb-3 d-flex align-items-center justify-content-center category-bubble cat">
                    <span class="text-muted">Image</span>
                </div>
            @endif
            <h3 class="h5 fs-6">{{ $category->name }}</h3>
        </a>
    @endforeach
</div>
</div>

<h3 class="text-left mt-5 mb-4 text-primary fs-5">Featured Products</h3>
<div class="row border-top border-secondary border-1 pt-4 ms-1 me-4">
    @foreach($products as $product)
        <div class="col-md-4 mb-4">
            <a href="{{route('product.show',$product->id)}}" class="text-decoration-none text-dark">
            <div class="card h-100">
                <div class="text-center mt-3">
                    <div class="square bg-light mx-auto mb-3 d-flex align-items-center justify-content-center category-bubble">
                        <span class="text-muted">Image</span>
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fs-6">{{ $product->name }}</h5>
                    <p class="card-text small">{{ Str::limit($product->description, 100) }}</p>
                    @if (Auth::user() !== NULL && Auth::user()->admin == NULL)
                        @php
                            $promoPrice = $product->promotionPrice(Auth::user()->buyer);
                        @endphp
                        <p class="card-text text-primary fw-bold small mt-auto d-flex flex-column justify-content-end">
                        <span class="{{ $promoPrice == $product->price ? 'invisible' : 'text-decoration-line-through text-muted text-xs' }}">
                            ${{ $product->price }}
                        </span>
                        <span class=" {{ $promoPrice < $product->price ? 'text-success' : '' }}">
                            ${{ $promoPrice }}
                        </span>
                        </p>
                    @else
                        <p class="card-text text-primary fw-bold small mt-auto d-flex flex-column justify-content-end">
                            ${{ $product->price }}
                        </p>
                    @endif
                </div>
            </div>
            </a>
        </div>
    @endforeach
</div>

<div class="text-center mt-3 mb-3">
    <a href="{{ route('catalog') }}">See our Catalog</a>
</div>

<!-- TODO: FOOTER -->
 
@endsection