@extends('layouts.app')

@section('title', 'Manage Orders | ' . config('app.name'))

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
        <li class="breadcrumb-item active" aria-current="page">Orders</li>
    </ol>
</nav>

<div class="container mt-4">
    <h1 class="fs-4 mb-4">Manage Orders</h1>

    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Buyer</th>
                <th>Status</th>
                <th>Date</th>
                <th>Total</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->cart->buyer->user_name ?? 'N/A' }}</td>

                {{-- Editable Status Column --}}
                <td>
    <form class="update-status-form d-flex align-items-center gap-1" data-id="{{ $order->id }}">
        @csrf
        @method('PATCH')

        <select name="status" class="form-select form-select-sm w-auto">
            <option value="in distribution" {{ $order->status === 'in distribution' ? 'selected' : '' }}>In Distribution</option>
            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>

        <button type="submit" class="btn btn-success btn-sm px-2 py-1 bot">
            Save
        </button>
    </form>
</td>


                <td>{{ $order->date_ord->format('d/m/Y') ?? 'N/A' }}</td>
                <td>${{ number_format($order->total_amount, 2) }}</td>

                <td class="text-center">
                    <a href="{{ route('orders.details', $order->id) }}" class="btn btn-sm btn-primary botao">
                        <i class="bi bi-eye"></i>
                        View Order Details
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">No orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection


