@extends('layouts.app')

@section('title', 'Dashboard | ' . config('app.name'))

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
</nav>

<div class="container mt-4">

    <h1 class="fs-4 mb-4">Dashboard</h1>

        <div class="row justify-content-center g-4">
    <!-- Manage Products -->
        <div class="col-6 col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center" style="height: auto;">
                    <i class="bi bi-basket text-primary fs-2"></i>
                    <h5 class="card-title">Manage Products</h5>
                    <p class="card-text text-muted">Add, edit or delete products</p>
                    <a href="{{ route('products.manage') }}" class="btn btn-primary w-100 bot">
                        Go to Products
                    </a>
                </div>
            </div>
        </div>

    <!-- Manage Categories -->
        <div class="col-6 col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center" style="height: auto;">
                    <i class="bi bi-folder2-open text-primary fs-2"></i>
                    <h5 class="card-title">Manage Categories</h5>
                    <p class="card-text text-muted">Add, edit or delete categories</p>
                    <a href="{{ route('categories.manage') }}" class="btn btn-primary w-100 bot">
                        Go to Categories
                    </a>
                </div>
            </div>
        </div>

        <!-- Manage Order Status -->
        <div class="col-6 col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center" style="height: auto;">
                    <i class="bi bi-truck text-primary fs-2"></i>
                    <h5 class="card-title">Manage Order Status</h5>
                    <p class="card-text text-muted">Manage orders and their status</p>
                    <a href="{{ route('orders.manage') }}" class="btn btn-primary w-100 bot">
                        Go to Orders
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4 mt-2">
    <!-- Manage Promotions -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center" style="height: auto;">
                    <i class="bi bi-tags-fill text-primary fs-2"></i>
                    <h5 class="card-title">Manage Promotions</h5>
                    <p class="card-text text-muted">Add, edit or delete promotions</p>
                    <a href="{{ route('promotions.show') }}" class="btn btn-primary w-100 bot">
                        Go to Promotions
                    </a>
                </div>
            </div>
        </div>

        <!-- Manage Reports -->
        <div class="col-12 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center" style="height: auto;">
                    <i class="bi bi-graph-up-arrow text-primary fs-2"></i>
                    <h5 class="card-title">Manage Reports</h5>
                    <p class="card-text text-muted">Manage reports and their status</p>
                    <a href="{{ route('reports.show') }}" class="btn btn-primary w-100 bot">
                        Go to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection

