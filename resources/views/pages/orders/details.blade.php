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
            <a href="{{ url('/dashboard') }}" class="text-decoration-none text-primary">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ url('/orders/manage') }}" class="text-decoration-none text-primary">Orders</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Order #{{ $order->id }}</li>
    </ol>
</nav>

<div class="container mt-4">
    <h1 class="fs-4 mb-4">Order #{{ $order->id }}</h1>

    <p><strong>Status:</strong> {{ $order->status }}</p>
    <p><strong>Date:</strong> {{ $order->date_ord ? $order->date_ord->format('d/m/Y H:i') : 'N/A' }}</p>

    <h5 class="mt-4 mb-3">Items</h5>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->cart->items as $item)
            <tr>
                <td>
                    @if($item->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="50" height="50" class="rounded" loading="lazy">
                    @else
                        <!-- Placeholder leve -->
                        <img src="{{ asset('images/placeholder.png') }}" alt="No Image" width="50" height="50" class="rounded">
                    @endif
                </td>
                <td>{{ $item->product->name }}</td>
                <td>${{ $item->product->promotionPrice($item->cart->buyer) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ $item->discSubTotal() }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total</th>
                <th>${{ $order->cart->discountPrice() }}</th>
            </tr>
        </tfoot>
    </table>

    <a href="{{ route('orders.manage') }}" class="btn btn-primary mt-3 botao"><i class="bi bi-arrow-left me-2 fs-6"></i>Back to Orders</a>
</div>
@endsection
