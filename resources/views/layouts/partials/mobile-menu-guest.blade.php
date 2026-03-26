<!-- Mobile Right Side for Guests -->
<div class="nav-item dropdown d-lg-none">
    <button class="btn text-light ms-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-list fs-4"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">

        <li><a class="dropdown-item" href="{{ route('catalog') }}"><i class="bi bi-grid me-2"></i>Catalog</a></li>
        <li><a class="dropdown-item" href="{{ route('about') }}"><i class="bi bi-people me-2"></i>About Us</a></li>

        <li><hr class="dropdown-divider"></li>

        <li><a class="dropdown-item" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a></li>
        <li><a class="dropdown-item" href="{{ route('register') }}"><i class="bi bi-person-plus me-2"></i>Register</a></li>
    </ul>
</div>
