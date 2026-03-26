 @extends('layouts.app')

@section('title', $product->name . ' | ' . config('app.name'))


@section('content')

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('catalog') }}" class="text-decoration-none text-primary">Catalog</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{$product->name}}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-6">
        
        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
            
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1" style="background-color:#0d6efd"></button>
                <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="1" aria-label="Slide 2" style="background-color:#0d6efd"></button>
                <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="2" aria-label="Slide 3" style="background-color:#0d6efd"></button>
            </div>
            
            <div class="carousel-inner">
                
                <div class="carousel-item active">
                    <img src="{{ asset('images/products/' . $product->image1) }}" 
                         class="d-block w-100" 
                         alt="{{ $product->name }} - Image 1"
                         style="height: 400px; object-fit: contain; background-color: #f8f9fa;"> 
                         </div>
                
                <div class="carousel-item">
                    <img src="{{ asset('images/products/' . $product->image2) }}" 
                         class="d-block w-100" 
                         alt="{{ $product->name }} - Image 2"
                         style="height: 400px; object-fit: contain; background-color: #f8f9fa;">
                </div>
                
                <div class="carousel-item">
                    <img src="{{ asset('images/products/' . $product->image3) }}" 
                         class="d-block w-100" 
                         alt="{{ $product->name }} - Image 3"
                         style="height: 400px; object-fit: contain; background-color: #f8f9fa;">
                </div>
                
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon custom" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon custom" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        
    </div> 
    <div class="col-md-6">
    <div class="d-flex flex-column gap-4">
        <div class="d-flex justify-content-between">
        <div class="d-flex align-items-center">
            <h1 class="fs-3 me-2 mb-0">{{ $product->name }}</h1>

            @if($averageRating > 0)
            <div class="d-flex align-items-center">
                <div class="text-warning fs-5 me-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $averageRating >= $i ? '-fill' : '' }}"></i>
                    @endfor
                </div>
                <span class="text-muted fw-semibold">
                    {{ number_format($averageRating, 1) }} ({{ $reviews->count() }} reviews)
                </span>
            </div>
            @else
                <span class="text-muted small">No ratings yet.</span>
            @endif
        </div>
        </div>

        @if ($promotion == NULL)
            <p class="text-primary fs-4 fw-bold mb-0">${{ $product->price }}</p>
        @else
            <p class="text-primary fs-4 fw-bold mb-0">
                <span class="text-decoration-line-through text-muted fs-6 me-1">
                    ${{ $product->price }}
                </span>
                <span class="text-success fs-4">
                    ${{ number_format($product->price * (1 - ($promotion->amount / 100)), 2) }}
                </span>
            </p>
        @endif

        <p class="mb-0">{{ $product->description }}</p>

        <div>
            <form id="addToCartForm" method="POST" action="{{ route('cart.add', $product->id) }}" class="d-inline">
                @csrf
                <div class="d-flex gap-2 align-items-center">
                    <input type="number" id="cartQuantity" name="quantity" value="1" min="1"
                        class="form-control form-control-sm" style="width: 55px;">
                    <button type="submit" class="btn btn-primary btn-sm botao">Add to Cart</button>
                </div>
            </form>
            @if(Auth::check())
                @if($inWishlist)
                    <button type="button" class="btn btn-outline-danger btn-sm mt-1 botao" onclick="removeFromWishlistProduct({{ $product->id }}, this)">Remove from Wishlist</button>
                @else
                    <button type="button" class="btn btn-outline-primary btn-sm mt-1" onclick="addToWishlistProduct({{ $product->id }}, this)">Add to Wishlist</button>
                @endif
            @endif
        </div>

        <div>
            <h5 class="fs-6 mb-1">Product Details</h5>
            <ul class="small mb-0">
                <li><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</li>
                <li><strong>Stock:</strong> {{ $product->stock ?? 'N/A' }} units</li>
            </ul>
        </div>
        <div>
            <a href="{{ route('catalog') }}" class="btn btn-secondary btn-sm">Back to Catalog</a>
        </div>

    </div>
</div>
</div>

<hr class="my-5">

{{-- Reviews --}}

<div>
    <h3 class="fs-4 mb-4">Reviews</h3>

    <div class="mb-4">
        @forelse($reviews ?? collect() as $review)
        <div class="border ps-3 pe-3 rounded mb-3 costum-review" id="review-{{ $review->id }}">
            <div class="d-flex justify-content-between">
            <div class="d-flex align-items-center gap-4">
                <h6 class="fw-bold mt-3">{{ $review->order?->cart?->buyer?->user_name ?? 'Anonymous' }}</h6>
                <div class="text-warning mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                    @endfor
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
            @if ( Auth::user() !== NULL && ((Auth::user()->buyer !== NULL && $review->order?->cart?->buyer?->id == Auth::user()->buyer->id) || (Auth::user()->admin !== NULL))) 
                @if (Auth::user()->admin == NULL)
                <button class="btn btn-primary btn-sm botao-edit mt-2 botao" data-id="{{ $review->id }}">
                    <i class="bi bi-pencil fs-5"></i>
                </button>
                @endif
                <form method="POST" action="{{ route('reviews.delete', $review->id) }}">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm botao-remove mt-2 botao">
                    <i class="bi bi-trash fs-5"></i>
                </button>
                </form>
            @endif
            @if (Auth::user() !== NULL && Auth::user()->buyer !== NULL && $review->order->cart->buyer->id != Auth::user()->buyer->id)
                <button class="btn btn-warning btn-sm botao-report mt-2 botao" data-id="{{ $review->id }}">
                    <i class="bi bi-flag-fill fs-5"></i>
                </button>
            @endif
            </div>
            </div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <p>{{ $review->description }}</p>
                <small class="text-muted">{{ $review->created_at?->diffForHumans() ?? 'Just now' }}</small>
            </div>
    </div>
@empty
    <p class="text-muted">No reviews yet. Be the first to review this product!</p>
@endforelse

    </div>

<div class="modal fade" id="editReviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="editReviewForm">
                    <input type="hidden" id="review_id">

                    <div class="mb-3">
                        <label>Rating</label> 
                        <select id="rating" name="rating" class="form-select">
                            <option value="5">★★★★★</option>
                            <option value="4">★★★★☆</option>
                            <option value="3">★★★☆☆</option>
                            <option value="2">★★☆☆☆</option>
                            <option value="1">★☆☆☆☆</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Comentário</label>
                        <textarea id="comment" name="comment" class="form-control"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reportReviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reportReviewForm">
                    <input type="hidden" id="review_id">
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Report Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Please describe the issue in detail (min. 10 characters)..." required minlength="10" maxlength="1000">{{ old('description', $report->description ?? '') }}</textarea>
                        <div class="form-text">Briefly explain the reason for this report. (10-1000 characters)</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

    {{-- Add Review Form --}}
    @auth
        @if ( Auth::user()->admin == NULL)
        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Leave a Review</h5>

                <form method="POST" action="{{ route('reviews.store', $product->id) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <select name="rating" class="form-select" required>
                            <option value="">Choose rating...</option>
                            <option value="5">★★★★★</option>
                            <option value="4">★★★★☆</option>
                            <option value="3">★★★☆☆</option>
                            <option value="2">★★☆☆☆</option>
                            <option value="1">★☆☆☆☆</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea name="comment" class="form-control" rows="3" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary botao">Submit Review</button>
                </form>
            </div>
        </div>
        @endif
    @endauth 
    @guest
        <p class="mt-3">
            <a href="{{ route('login') }}" class="text-primary">Log in</a> to leave a review.
        </p>
    @endguest
</div>

<script>
// Add to Cart with AJAX
document.getElementById('addToCartForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const button = form.querySelector('button[type="submit"]');
    const originalButtonText = button.innerHTML;
    const quantity = document.getElementById('cartQuantity').value;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ quantity: parseInt(quantity) })
    })
    .then(response => response.json())
    .then(data => {
        // Show success feedback
        button.innerHTML = '<i class="bi bi-check-circle"></i> Added!';
        button.classList.remove('btn-primary');
        button.classList.add('btn-success');
        
        // Reset button after 2 seconds
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalButtonText;
            button.classList.remove('btn-success');
            button.classList.add('btn-primary');
            // Reset quantity to 1
            document.getElementById('cartQuantity').value = 1;
        }, 2000);
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = originalButtonText;
        alert('Error adding product to cart. Please try again.');
    });
});

function addToWishlistProduct(productId, btn) {
    // Update button immediately for instant feedback
    const originalHTML = btn.outerHTML;
    btn.outerHTML = `<button type="button" class="btn btn-outline-danger btn-sm mt-1 botao" onclick="removeFromWishlistProduct(${productId}, this)">Remove from Wishlist</button>`;
    
    fetch(`/wishlist/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert button on error
        document.querySelector(`[onclick*="removeFromWishlistProduct(${productId}"]`).outerHTML = originalHTML;
    });
}

function removeFromWishlistProduct(productId, btn) {
    // Update button immediately for instant feedback
    const originalHTML = btn.outerHTML;
    btn.outerHTML = `<button type="button" class="btn btn-outline-primary btn-sm mt-1" onclick="addToWishlistProduct(${productId}, this)">Add to Wishlist</button>`;
    
    fetch(`/wishlist/${productId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert button on error
        document.querySelector(`[onclick*="addToWishlistProduct(${productId}"]`).outerHTML = originalHTML;
    });
}
</script>

@endsection