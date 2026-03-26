@extends('layouts.app')

@section('title', 'Manage Products | ' . config('app.name'))

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
        <li class="breadcrumb-item active" aria-current="page">Products</li>
    </ol>
</nav>

<div class="container mt-4">
    <h1 class="fs-4 mb-4">Manage Products</h1>

    <!-- Botão para criar novo produto -->
    <button class="btn btn-success mb-3 botao-create-product botao">
        Add New Product
</button>

    <!-- Tabela de produtos -->
    <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Category</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody id="productTableBody">
            @forelse($products as $product)
            <tr id="row-{{$product->id}}">
                <td>
                    @if($product->image1)
                        <img src="{{ asset('images/products/' . $product->image1) }}" alt="{{ $product->name }}" width="50" height="50" class="rounded" loading="lazy">
                    @else
                        <!-- Placeholder leve -->
                        <img src="{{ asset('images/placeholder.png') }}" alt="No Image" width="50" height="50" class="rounded">
                    @endif
                </td>
                <td>{{ $product->name }}</td>
                <td>${{ number_format($product->price, 2) }}</td>
                <td>{{ $product->stock }}</td>
                <td>{{ $product->category->name ?? 'N/A' }}</td>
                <td class="text-center">
                    <!-- Edit -->
                    <button class="btn btn-sm btn-primary botao-edit-product botao" data-id="{{ $product->id }}">
                        Edit
                    </button>

                    <!-- Delete -->
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger botao"
                            onclick="return confirm('Are you sure you want to delete this product?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">No products found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal fade" id="createProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Mensagens de erro -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulário -->
                <form id="createProductForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" name="stock" id="stock" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="id_category" class="form-label">Category</label>
                        <select name="id_category" id="id_category" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Product Images (Exactly 3 required)</label>
                        <input type="file" name="image[]" id="image" class="form-control" accept="image/*" multiple required>
                        <div class="form-text">Please select exactly 3 images for this product.</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-dark">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <input type="hidden" id="edit_product_id">

                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Product Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Price</label>
                        <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_stock" class="form-label">Stock</label>
                        <input type="number" name="stock" id="edit_stock" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_id_category" class="form-label">Category</label>
                        <select name="id_category" id="edit_id_category" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Product Images (Exactly 3 required)</label>
                        <input type="file" name="image[]" id="image" class="form-control" accept="image/*" multiple>
                        <div class="form-text">Please select exactly 3 images for this product.</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

