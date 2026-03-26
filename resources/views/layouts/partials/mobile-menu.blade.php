<!-- Mobile Rightside for users (buyers & admins) -->
<div class="nav-item dropdown d-lg-none">
    <button class="btn text-light ms-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-list fs-4"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">

        <li><a class="dropdown-item" href="{{ route('catalog') }}"><i class="bi bi-grid me-2"></i>Catalog</a></li>
        <li><a class="dropdown-item" href="{{ route('about') }}"><i class="bi bi-people me-2"></i>About Us</a></li>


        <li><hr class="dropdown-divider"></li>

        @if(auth()->user()->buyer)
            <li><a class="dropdown-item" href="{{ url('/cart') }}"><i class="bi bi-cart me-2"></i>Cart</a></li>
            <li><a class="dropdown-item" href="{{ url('/wishlist') }}"><i class="bi bi-heart me-2"></i>Wishlist</a></li>
            <li><a class="dropdown-item" href="{{ url('/profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
        @endif

        @if(auth()->user()->admin)
            <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
        @endif


        <li><hr class="dropdown-divider"></li>

        <li><a class="dropdown-item text-danger" href="{{ url('/logout') }}"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
    </ul>
</div>
