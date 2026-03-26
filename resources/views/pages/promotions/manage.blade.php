@extends('layouts.app')

@section('title', 'Manage Promotions | ' . config('app.name'))

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
        <li class="breadcrumb-item active" aria-current="page">Promotions</li>
    </ol>
</nav>

<div class="container mt-4">
    <h1 class="fs-4 mb-4">Manage Promotions</h1>

    <button class="btn btn-success mb-3 botao-create-promo botao">
        Add new Promotion
    </button>
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Product Name</th>
                <th>Product ID</th>
                <th>Discount Amount</th>
                <th>Level Limit</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="promoTableBody">
            @forelse($promotions as $promotion)
            <tr id="row-{{$promotion->id}}">
                <td>
                    {{ $promotion->product->name ?? 'N/A' }}
                </td>
                <td style="padding-left: 2.5vw">
                    <code class="text-muted">#{{ $promotion->product->id ?? 'N/A' }}</code>
                </td>
                <td class="amount-colum" style="padding-left: 3.5vw">
                    {{-- Assuming amount might be a percentage or currency --}}
                    {{ $promotion->amount }}%
                </td>
                <td style="padding-left: 1.5vw">
                    <span class="badge bg-info text-dark level-column">Lvl {{ $promotion->level_limit }}</span>
                </td>
                <td class="text-center">
                    <button class="btn btn-primary btn-sm botao-edit-promo botao" data-id="{{ $promotion->id }}">
                        Edit
                    </button>

                    <form action="{{ route('promotions.destroy', $promotion->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger botao"
                            onclick="return confirm('Are you sure you want to delete this promotion?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">No promotions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal fade" id="editPromotionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Header igual ao Create -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Promotion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="editPromotionForm">
                    <input type="hidden" id="promotion_id">

                    <!-- Level -->
                    <div class="mb-3">
                        <label for="level_limit" class="form-label">Level</label>
                        <select id="level_limit" name="level_limit" class="form-select" required>
                            <option value="5">Level 5</option>
                            <option value="4">Level 4</option>
                            <option value="3">Level 3</option>
                            <option value="2">Level 2</option>
                            <option value="1">Level 1</option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (%)</label>
                        <div class="input-group">
                            <input type="number"
                                   id="amount"
                                   name="amount"
                                   class="form-control"
                                   min="1"
                                   max="100"
                                   required>
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text">
                            Choose a value between 1 and 100.
                        </div>
                    </div>

                    <!-- Botão igual ao Create -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            Save Changes
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="createPromotionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Header consistente -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add Promotion</h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="createPromotionForm">

                    <!-- Product -->
                    <div class="mb-3">
                        <label for="product_search" class="form-label">Product</label>
                        <input class="form-control"
                               list="productOptions"
                               id="product_search"
                               placeholder="Type to search product..."
                               required>

                        <datalist id="productOptions">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }} (ID: {{ $product->id }})
                                </option>
                            @endforeach
                        </datalist>

                        <input type="hidden" name="product_id" id="product_id">
                        <div class="form-text">
                            Start typing to search for a product.
                        </div>
                    </div>

                    <!-- Level -->
                    <div class="mb-3">
                        <label for="level_limit-create" class="form-label">Level</label>
                        <select id="level_limit-create"
                                name="level_limit"
                                class="form-select"
                                required>
                            <option value="5">Level 5</option>
                            <option value="4">Level 4</option>
                            <option value="3">Level 3</option>
                            <option value="2">Level 2</option>
                            <option value="1">Level 1</option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="mb-3">
                        <label for="amount-create" class="form-label">Amount (%)</label>
                        <div class="input-group">
                            <input type="number"
                                   id="amount-create"
                                   name="amount"
                                   class="form-control"
                                   min="1"
                                   max="100"
                                   required>
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text">
                            Choose a value between 1 and 100.
                        </div>
                    </div>

                    <!-- Botão full-width -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            Add Promotion
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection