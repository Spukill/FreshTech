@extends('layouts.app')

@section('title', $buyer->user_name . ' | Cart')

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
    </ol>
</nav>

<h1 class="fs-4 mb-4 mt-4">Shopping Cart</h1>

@if(count($cartItems) === 0)
    <div class="d-flex justify-content-center">
        <div class="card text-center shadow-sm" style="max-width:540px; width:100%;">
            <div class="card-body py-5">
                <div class="mb-3">
                    <i class="bi bi-cart-x" style="font-size:2.5rem;color:#6c757d;"></i>
                </div>
                <h5 class="card-title">Your cart is empty</h5>
                <p class="card-text text-muted">Looks like you haven't added anything to your cart yet.</p>
                <a href="{{ route('catalog') }}" class="btn btn-primary btn-sm">Browse products</a>
            </div>
        </div>
    </div>
@else
    <div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th class="text-end">Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($cartItems as $item)
                <tr id="cart-row-{{ $item->product->id }}" data-product-id="{{ $item->product->id }}">
                    <td class="align-middle">
                        <div class="d-flex align-items-center gap-3">
                            <img 
                                src="{{ asset('storage/' . $item->product->image) }}"
                                alt="{{ $item->product->name }}"
                                class="img-thumbnail"
                                style="width: 50px; height: 70px; object-fit: cover;">
                            <a href="{{ route('product.show', $item->product->id) }}" class="text-decoration-none">
                                {{ $item->product->name }}
                            </a>
                        </div>
                    </td>

                    <td class="align-middle item-price">
                        ${{ $item->product->price }}
                    </td>

                    <td class="align-middle text-center">
                        <div class="d-flex align-items-center gap-2">
                            <input type="number"
                                name="quantity"
                                min="1"
                                value="{{ $item->quantity }}"
                                class="form-control form-control-sm quantity-input"
                                data-product-id="{{ $item->product->id }}"
                                data-update-url="{{ route('cart.update', $item->product->id) }}"
                                style="width: 60px; height: auto;">

                            <button 
                                type="button"
                                class="btn btn-sm botao-remove botao"
                                data-bs-toggle="modal"
                                data-bs-target="#removeModal"
                                data-product-id="{{ $item->product->id }}"
                                data-remove-url="{{ route('cart.remove', $item->product->id) }}"
                                aria-label="Remove product"
                            >
                                <i class="bi bi-trash fs-5"></i>
                            </button>
                        </div>
                    </td>
                    <td class="fw-bold align-middle text-end item-subtotal">
                        @if ($item->discSubTotal() == $item->subTotal())
                            ${{ $item->subTotal() }}
                        @else
                            <p class="text-primary fw-bold mb-0 d-flex flex-column">
                            <span class="text-decoration-line-through text-muted me-1 text-xs">
                            ${{ $item->subTotal() }}
                            </span>
                            <span class="text-success fs-6">
                            ${{ $item->discSubTotal() }}
                            </span>
                            </p>
                        @endif
                    </td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


    <div class="row mt-4">
        <div class="col-md-6">
            <a href="{{ route('catalog') }}" class="btn btn-secondary btn-sm botao">Continue Shopping</a>
        </div>
        <div class="col-md-6 text-end">
            <h4>Total: <span class="text-primary" id="cart-total">
                ${{ $discPrice }}</span> </h4>
            <hr class="my-4 ms-auto" style="width: 250px">
            <a href="{{ route('checkout') }}" class="btn btn-danger botao p-2"><i class="bi bi-cart me-2"></i>Proceed to Checkout</a>
        </div>
    </div>
@endif

<!-- Remove Item Modal -->
<div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeModalLabel">Remove Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove this item from your cart?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmRemoveBtn" class="btn btn-danger">Remove</button>
            </div>
        </div>
    </div>
</div>

<!-- Authentication required modal -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="authModalLabel">Authentication required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                You must be logged in to proceed to checkout. Please login or register to continue.
            </div>
            <div class="modal-footer">
                <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary">Register</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================
// Update quantity with AJAX
// ============================================
document.querySelectorAll('.quantity-input').forEach(input => {
    let timeout;
    
    input.addEventListener('change', function() {
        clearTimeout(timeout);
        
        const productId = this.dataset.productId;
        const updateUrl = this.dataset.updateUrl;
        const newQuantity = this.value;
        const row = document.getElementById(`cart-row-${productId}`);
        
        this.disabled = true;
        
        timeout = setTimeout(() => {
            fetch(updateUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity: parseInt(newQuantity) })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const subtotalCell = row.querySelector('.item-subtotal');
                    if (data.hasDiscount) {
                        subtotalCell.innerHTML = `
                            <p class="text-primary fw-bold mb-0 d-flex flex-column">
                                <span class="text-decoration-line-through text-muted me-1 text-xs">
                                    $${data.itemSubtotal}
                                </span>
                                <span class="text-success fs-6">
                                    $${data.itemDiscSubtotal}
                                </span>
                            </p>
                        `;
                    } else {
                        subtotalCell.textContent = '$' + data.itemSubtotal;
                    }
                    
                    document.getElementById('cart-total').textContent = '$' + data.discPrice;
                    row.classList.add('table-success');
                    setTimeout(() => row.classList.remove('table-success'), 1000);
                }
                input.disabled = false;
            })
            .catch(error => {
                console.error('Error updating quantity:', error);
                alert('Error updating quantity. Please try again.');
                input.disabled = false;
            });
        }, 500);
    });
});

// ============================================
// Remove item with AJAX using Modal
// ============================================
let itemToRemove = null;

// When modal opens, store the item info
document.getElementById('removeModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    itemToRemove = {
        productId: button.getAttribute('data-product-id'),
        removeUrl: button.getAttribute('data-remove-url')
    };
});

// When confirm button is clicked, remove via AJAX
document.getElementById('confirmRemoveBtn').addEventListener('click', function() {
    if (!itemToRemove) return;
    
    const { productId, removeUrl } = itemToRemove;
    const row = document.getElementById(`cart-row-${productId}`);
    const modal = bootstrap.Modal.getInstance(document.getElementById('removeModal'));
    
    // Close modal first
    modal.hide();
    
    // Make AJAX request to remove item
    fetch(removeUrl, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Animate row removal
            row.style.transition = 'opacity 0.3s';
            row.style.opacity = '0';
            
            setTimeout(() => {
                row.remove();
                
                // Check if there are any rows left in the table
                const remainingRows = document.querySelectorAll('tbody tr').length;
                
                if (remainingRows === 0) {
                    // Cart is empty, replace only table and total section with empty message
                    const tableContainer = document.querySelector('.table-responsive');
                    const totalsSection = document.querySelector('.row.mt-4');
                    
                    const emptyCartHTML = `
                        <div class="d-flex justify-content-center">
                            <div class="card text-center shadow-sm" style="max-width:540px; width:100%;">
                                <div class="card-body py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-cart-x" style="font-size:2.5rem;color:#6c757d;"></i>
                                    </div>
                                    <h5 class="card-title">Your cart is empty</h5>
                                    <p class="card-text text-muted">Looks like you haven't added anything to your cart yet.</p>
                                    <a href="{{ route('catalog') }}" class="btn btn-primary btn-sm">Browse products</a>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    tableContainer.outerHTML = emptyCartHTML;
                    if (totalsSection) totalsSection.remove();
                } else {
                    // Update cart total
                    document.getElementById('cart-total').textContent = '$' + data.discPrice;
                }
            }, 300);
        }
    })
    .catch(error => {
        console.error('Error removing item:', error);
        alert('Error removing item. Please try again.');
    });
    
    itemToRemove = null;
});
</script>

@endsection

