@extends('layouts.app')

@section('title', $user->buyer->user_name . ' | ' . config('app.name'))

@section('content')

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-light p-3 rounded">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}" class="text-decoration-none text-primary">
                <i class="bi bi-house-door"></i> Home
            </a>
        </li>
        @if ($edit != 'true')
            <li class="breadcrumb-item active" aria-current="page">Profile</li>
        @else
            <li class="breadcrumb-item">
                <a href="{{ url('/profile') }}" class="text-decoration-none text-primary">Profile</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
        @endif
    </ol>
</nav>

    <section class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fs-4 mb-0">My Profile</h2>
            @if ($edit != 'true')
                <a href="{{ url('/profile?edit=true') }}" class="btn btn-primary btn-sm">Edit</a>
            @else
                <a href="{{ url('/profile') }}" class="btn btn-secondary btn-sm">Cancel</a>
            @endif
        </div>

        <!-- Profile card -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <img src="{{ $user->buyer->getProfileImage() }}" alt="avatar" class="rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover;">
                        
                        @if ($edit == 'true')
                            <form method="POST" action="{{ route('file.upload') }}" enctype="multipart/form-data" class="mb-3">
                                @csrf
                                <input type="file" name="file" id="profileImageInput" accept="image/png,image/jpeg,image/jpg,image/gif" class="d-none" onchange="this.form.submit()">
                                <input type="hidden" name="id" value="{{ $user->buyer->id }}">
                                <input type="hidden" name="type" value="profile">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('profileImageInput').click()">
                                    <i class="bi bi-camera"></i> Change Image
                                </button>
                            </form>
                        @endif
                        
                        <h4 class="mb-0 fw-bold">{{ $user->buyer->user_name }}</h4>
                        <p class="text-muted small mb-2">{{ $user->email }}</p>

                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <span class="badge bg-primary">Level {{ $user->buyer->getLevel() }}</span>
                            <span class="badge bg-secondary">{{ $user->buyer->exp ?? 0 }} XP</span>
                        </div>

                        @php
                            $xp = $user->buyer->exp ?? 0;
                            $xpPercent = ($xp % 500) / 5; // gets us the xp bar %
                        @endphp
                        <div class="progress mb-3" style="height:8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $xpPercent }}%;" aria-valuenow="{{ $xpPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Profile -->
            <div class="col-md-8">
                @if ($edit == 'true')
                    <div class="card shadow-sm p-3">
                        <form id="profileForm" method="POST" action="{{ url('/profile') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small">Username</label>
                                    <input type="text" name="user_name" class="form-control form-control-sm" value="{{ $user->buyer->user_name }}">
                                </div>
                                    <div class="col-12">
                                        <label class="form-label small">Password (leave blank to keep current)</label>
                                        <input type="password" name="password" class="form-control form-control-sm" @if(!empty($isOauth)) disabled @endif>
                                        @if(!empty($isOauth))
                                            <div class="form-text small text-muted">You logged in via Google, you cannot change your password.</div>
                                        @else
                                            <div class="form-text small text-muted">Leave blank to keep your current password. Enter a new password and confirm below.</div>
                                        @endif
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small">Confirm password</label>
                                        <input type="password" name="password_confirmation" class="form-control form-control-sm" @if(!empty($isOauth)) disabled @endif>
                                    </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <script>
                    document.getElementById('profileForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const formData = new FormData(this);
                        
                        fetch('{{ url('/profile') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                // Redirect after a short delay to allow Pusher notification to arrive
                                setTimeout(() => {
                                    window.location.href = '{{ route('profile') }}';
                                }, 500);
                            } else {
                                return response.json().then(data => {
                                    console.error('Error:', data);
                                    alert('Error updating profile. Please try again.');
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error updating profile. Please try again.');
                        });
                    });
                    </script>
                <!-- Get and show orders -->
                @else
                    <div class="mb-3">
                        <h5 class="fs-6">Order History</h5>

                        @php
                            $flatOrders = collect();
                            if ($orders !== NULL) {
                                foreach ($orders as $o) {
                                    if ($o instanceof \Illuminate\Support\Collection || is_array($o)) {
                                        $flatOrders = $flatOrders->concat($o);
                                    } else {
                                        $flatOrders->push($o);
                                    }
                                }
                                $flatOrders = $flatOrders->sortByDesc('date_ord')->values();
                            }
                        @endphp

                        @if ($flatOrders && $flatOrders->count() > 0)
                            <div class="list-group">
                                @foreach ($flatOrders as $order)
                                    @php
                                        $firstProduct = optional($order->products()->first());
                                        $date = \Carbon\Carbon::parse($order->date_ord)->format('M j, Y');
                                        $total = ($order->cart && method_exists($order->cart, 'totalPrice')) ? $order->cart->totalPrice() : null;
                                        switch($order->status) {
                                            case 'delivered': $badgeClass = 'bg-success'; $statusLabel = 'Delivered'; break;
                                            case 'cancelled': $badgeClass = 'bg-danger'; $statusLabel = 'Cancelled'; break;
                                            case 'in distribution': $badgeClass = 'bg-warning text-dark'; $statusLabel = 'Delivering'; break;
                                            default: $badgeClass = 'bg-secondary'; $statusLabel = ucfirst($order->status); break;
                                        }
                                    @endphp

                                    <div class="mb-3">
                                        <a href="{{ url('/profile/orders/' . $order->id) }}" class="text-decoration-none text-dark">
                                            <div class="card shadow-sm">
                                                <div class="card-body d-flex align-items-center">
                                                    <div class="me-3 bg-light rounded d-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                                                        <i class="bi bi-image text-muted" style="font-size:1.2rem;"></i>
                                                    </div>

                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <div class="fw-bold">Order #{{ $order->id }}</div>
                                                                <div class="text-muted small">{{ $firstProduct->name ?? 'Order' }}</div>
                                                            </div>

                                                            <div class="text-end">
                                                                @if($total)
                                                                    <div class="fw-bold">${{ $total }}</div>
                                                                @endif
                                                                <div class="text-muted small">{{ $date }}</div>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="ms-3 text-muted">
                                                        <i class="bi bi-chevron-right"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        <!-- No orders found -->
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-receipt-x fs-1 mb-2"></i>
                                <p class="mb-2">No orders yet</p>
                                <small>Make a purchase in order to see your order history</small>
                            </div>
                        @endif

                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection
