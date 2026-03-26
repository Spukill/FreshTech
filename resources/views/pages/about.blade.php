@extends('layouts.app')

@section('title', 'About Us - FreshTech')

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">About Us</li>
    </ol>
</nav>

<!-- Hero Section -->
<section class="bg-primary text-white py-5 mb-5" style="background: linear-gradient(135deg, #8f9ddaff 0%, #142e9eff 100%) !important;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-4 fw-bold mb-4">About FreshTech</h1>
                <p class="lead opacity-75">FreshTech is an online tech shop that offers a trusted selection of quality products, making technology easy to find, compare, and buy. From everyday devices to specialized components, we provide a wide range of products with clear information, fair prices, and fast service.</p>
            </div>
        </div>
    </div>
</section>

<div class="container">

    <!-- Statistics -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-3">
            <div class="card text-white border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #81bcffff 0%, #2a84faff 100%);">
                <div class="card-body text-center p-4">
                    <h3 class="display-4 fw-bold mb-2">100+</h3>
                    <p class="mb-0 fs-5">Tech Products</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #81bcffff 0%, #2a84faff 100%);">
                <div class="card-body text-center p-4">
                    <h3 class="display-4 fw-bold mb-2">10+</h3>
                    <p class="mb-0 fs-5">Product Categories</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #81bcffff 0%, #2a84faff 100%);">
                <div class="card-body text-center p-4">
                    <h3 class="display-4 fw-bold mb-2">99%</h3>
                    <p class="mb-0 fs-5">Uptime</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Meet the Team -->
    <div class="row mb-5">
        <div class="col-lg-10 mx-auto">
            <h2 class="text-center mb-5" style="color: #667eea;">Our Team</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; background: linear-gradient(135deg, #8f9ddaff 0%, #142e9eff 100%); color: white; font-size: 3rem;">
                            <i class="bi bi-person"></i>
                        </div>
                        <h5 class="h6 mb-2">Catarina Bastos</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; background: linear-gradient(135deg, #8f9ddaff 0%, #142e9eff 100%); color: white; font-size: 3rem;">
                            <i class="bi bi-person"></i>
                        </div>
                        <h5 class="h6 mb-2">João Júnior</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; background: linear-gradient(135deg, #8f9ddaff 0%, #142e9eff 100%); color: white; font-size: 3rem;">
                            <i class="bi bi-person"></i>
                        </div>
                        <h5 class="h6 mb-2">Tiago Cunha</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; background: linear-gradient(135deg, #8f9ddaff 0%, #142e9eff 100%); color: white; font-size: 3rem;">
                            <i class="bi bi-person"></i>
                        </div>
                        <h5 class="h6 mb-2">Vasco Gonçalves</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection