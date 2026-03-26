@extends('layouts.app')

@section('title', 'Order #' . $order->id . ' | ' . config('app.name'))

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ url('/profile') }}" class="text-decoration-none text-primary">Profile</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Order #{{ $order->id }}</li>
    </ol>
</nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-0">Order #{{ $order->id }}</h3>
                <small class="text-muted">Placed: {{ \Carbon\Carbon::parse($order->date_ord)->format('M j, Y \a\t H:i') }}</small>
            </div>

            <div class="d-flex align-items-center gap-3">
                @if($order->status == 'delivered')
                    <span class="badge bg-success">Delivered</span>
                @elseif($order->status == 'cancelled')
                    <span class="badge bg-danger">Cancelled</span>
                @elseif($order->status == 'in distribution')
                    <span class="badge bg-warning text-dark">Delivering</span>
                @else
                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                @endif
                <a href="{{ route('profile') }}" class="btn text-light bg-primary btn-sm ms-4 botao">
                    <i class="bi bi-arrow-left"></i> Back to Profile
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($order->cart->items as $item)
                        <li class="list-group-item d-flex align-items-center py-3">
                            <div class="me-3 bg-light rounded d-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                                <i class="bi bi-image text-muted" style="font-size:1.2rem;"></i>
                            </div>

                            <div class="flex-grow-1">
                                <a href="{{ route('product.show', $item->product->id) }}" class="text-decoration-none text-dark fw-bold">{{ $item->product->name }}</a>
                                <div class="text-muted small">{{ Str::limit($item->product->description ?? '', 80) }}</div>
                            </div>

                            <div class="text-end ms-3">
                                <div class="small">${{ number_format($item->product->promotionPrice($item->cart->buyer), 2) }}</div>
                                <div class="small">Qty: {{ $item->quantity }}</div>
                                <div class="fw-bold mt-1">${{ number_format($item->discSubTotal(), 2) }}</div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                @if ($order->status != 'delivered' && $order->status != 'cancelled')
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                        Cancel Order
                    </button>
                @endif
            </div>

            <div class="text-end">
                <h5 class="mb-0">Total: <span class="text-primary">${{ number_format(optional($order->cart)->discountPrice() ?? 0, 2) }}</span></h5>
            </div>
        </div>
    </div>

    <!-- Cancel Order Confirmation Modal -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel order #{{ $order->id }}?</p>
                    <p class="text-muted small mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Order</button>
                    <a href="{{ url('/profile/orders/'. $order->id . '/cancel') }}" class="btn btn-danger">Yes, Cancel Order</a>
                </div>
            </div>
        </div>
    </div>
@endsection