@extends('layouts.app')

@section('content')
<div class="container py-5">

    <div class="row">

        {{-- PRODUCT IMAGE --}}
        <div class="col-md-6 mb-4">
            <img src="{{ $product->image_url ?? '/images/default-product.png' }}" 
                 class="img-fluid rounded shadow" 
                 alt="{{ $product->name }}">
        </div>

        {{-- PRODUCT INFO --}}
        <div class="col-md-6">

            <h1 class="mb-2">{{ $product->name }}</h1>

            <h3 class="text-success mb-3">
                € {{ number_format($product->price, 2) }}
            </h3>

            <p class="text-muted mb-4">
                {{ $product->description }}
            </p>

            {{-- ADD TO CART --}}
            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-primary btn-lg">
                    🛒 Add to Cart
                </button>
            </form>
<!-- 
            {{-- WISHLIST BUTTON --}}
            @if($inWishlist ?? false)
                {{-- REMOVE FROM WISHLIST --}}
                <form action="{{ route('wishlist.remove', $product->id) }}" 
                      method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger btn-lg">
                        ❤️ Remove from Wishlist
                    </button>
                </form>
            @else
                {{-- ADD TO WISHLIST --}}
                <form action="{{ route('wishlist.add', $product->id) }}" 
                      method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-danger btn-lg">
                        🤍 Add to Wishlist
                    </button>
                </form>
            @endif
 -->
            <hr class="my-4">

            {{-- SPECS --}}
            @if($product->specifications && $product->specifications->count())
            <h4>Specifications</h4>
            <ul class="list-unstyled">
                @foreach($product->specifications as $spec)
                    <li><strong>{{ $spec->spec_key }}:</strong> {{ $spec->spec_value }}</li>
                @endforeach
            </ul>
            @endif

        </div>
    </div>

</div>
@endsection
