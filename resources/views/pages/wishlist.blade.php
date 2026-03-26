@extends('layouts.app')

@section('title', $user->buyer->user_name . ' | Wishlist')

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
    </ol>
</nav>

<div class="container">
    <h1 class="fs-4 mb-4 mt-4">My Wishlist</h1>

    @if($wishlist && $wishlist->products->count())
        <div class="row" id="wishlist-items">
            @foreach($wishlist->products as $product)
                <div class="col-md-4 mb-4" id="wishlist-item-{{ $product->id }}">
                    <div class="card h-100">
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <!-- Placeholder for product image -->
                            <span class="text-muted">Image</span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-primary fw-bold">${{ number_format($product->price, 2) }}</p>
                            <p class="card-text small text-muted">{{ Str::limit($product->description, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('product.show', $product) }}" class="btn btn-primary btn-sm">View Product</a>
                                <button type="button" class="btn btn-danger btn-sm botao-remove botao" onclick="removeFromWishlist({{ $product->id }})">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center" id="empty-wishlist">
            <p class="text-muted">Your wishlist is empty.</p>
            <a href="{{ route('catalog') }}" class="btn btn-primary">Browse Products</a>
        </div>
    @endif
</div>

<script>
function removeFromWishlist(productId) {
    // Remove the item from DOM immediately for instant feedback
    const item = document.getElementById(`wishlist-item-${productId}`);
    if (item) {
        item.remove();
    }
    
    // Check if wishlist is now empty
    const wishlistItems = document.getElementById('wishlist-items');
    if (wishlistItems && wishlistItems.children.length === 0) {
        wishlistItems.outerHTML = `
            <div class="text-center" id="empty-wishlist">
                <p class="text-muted">Your wishlist is empty.</p>
                <a href="{{ route('catalog') }}" class="btn btn-primary">Browse Products</a>
            </div>
        `;
    }
    
    // Send request to server
    fetch(`/wishlist/${productId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Optionally reload page on error
        location.reload();
    });
}
</script>
@endsection