@extends('layouts.app')

@section('title', 'Catalog | ' . config('app.name'))

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        
        @if(request('category'))
            <li class="breadcrumb-item">
                <a href="{{ route('catalog') }}" class="text-decoration-none text-primary">Catalog</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $categories->find(request('category'))->name ?? 'Category' }}
            </li>
        @else
            <li class="breadcrumb-item active" aria-current="page">Catalog</li>
        @endif
    </ol>
</nav>

<div class="container mt-4">
    <h1 class="mb-4 fs-4">Catalog</h1>

    <div class="row">
        <!-- Filters Sidebar (LEFT SIDE) -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="fs-6 mt-2 text-light">Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('catalog') }}">
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="search" class="form-label small">Search</label>
                            <input type="text" name="search" id="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Product name or description">
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category" class="form-label small">Category</label>
                            <select name="category" id="category" class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label small">Price Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" name="price_min" class="form-control form-control-sm" placeholder="Min" value="{{ request('price_min') }}" step="0.01">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="price_max" class="form-control form-control-sm" placeholder="Max" value="{{ request('price_max') }}" step="0.01">
                                </div>
                            </div>
                        </div>

                        <!-- Sorting -->
                        <div class="mb-3">
                            <label for="sort" class="form-label small">Sort By</label>
                            <select name="sort" id="sort" class="form-select form-select-sm">
                                <option value="">Default</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Lowest Price</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Highest Price</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary btn-sm botao">Apply Filters</button>
                            <a href="{{ route('catalog') }}" class="btn btn-secondary btn-sm botao">Clear Filters</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Section (RIGHT SIDE) -->
        <div class="col-md-9">
            <div class="row">
                @forelse($products as $product)
                    <div class="col-md-4 mb-4">
                        <a href="{{route('product.show',$product->id)}}" class="text-decoration-none text-dark">
                        <div class="card h-100">
                            <div class="text-center mt-3">
                                <div class="square bg-light mx-auto mb-3 d-flex align-items-center justify-content-center category-bubble">
                                    <!-- Placeholder for category image -->
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
                @empty
                    <p>No products found matching your filters.</p>
                @endforelse
            </div>

            <!-- Laravel Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(array_filter(request()->query()))->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Script to disable urls like 
 "http://127.0.0.1:8000/catalog?search=&category=4&price_min=&price_max=&sort=" 
 for a simple category filter. It simply checks if a filter is = "" and trims it from the url if it is-->
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const inputs = this.querySelectorAll('input[name], select[name]');
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.disabled = true;
        }
    });
});
</script>

@endsection