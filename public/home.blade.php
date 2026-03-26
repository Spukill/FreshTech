@extends('layouts.app')

@section('title', 'Home')

@section('content')
<h3 class="text-left mt-5 mb-4 text-primary">Featured Categories</h3>
<div class="d-flex justify-content-around mt-5">
    @foreach($categories as $category)
        <div class="text-center">
            <div class="rounded-circle bg-light mx-auto mb-3 d-flex align-items-center justify-content-center category-bubble">
                <!-- Placeholder for category image -->
                <span class="text-muted">Image</span>
            </div>
            <h3 class="h5">{{ $category->name }}</h3>
        </div>
    @endforeach
</div>

<h3 class="text-left mt-5 mb-4 text-primary">Featured Products</h3>
<div class="row">
    @foreach($products as $product)
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="text-center mt-3">
                    <div class="square bg-light mx-auto mb-3 d-flex align-items-center justify-content-center category-bubble">
                        <!-- Placeholder for category image -->
                        <span class="text-muted">Image</span>
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
                    <p class="card-text text-primary fw-bold mt-auto">${{ $product->price }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection