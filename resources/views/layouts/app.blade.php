<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Styles -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')

        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('js/app.js') }}" defer></script>
        @stack('scripts')
    </head>
    <body>
        
        <div class="white"> 
            <div class="squares">
                <div class="obj"></div> 
                <div class="obj"></div>
                <div class="obj"></div>
                <div class="obj"></div>
                <div class="obj"></div>
                <div class="obj"></div>
                <div class="obj"></div>
                <div class="obj"></div>
                <div class="obj"></div>
                <div class="obj"></div>
            </div>
        </div>
        <nav class="navbar navbar-expand-lg bg-dark fixed-top">
            <div class="container">
                <a class="navbar-brand fs-3 brand-font text-light" href="{{ route('home') }}">Fresh<span class="text-primary brand-font1">Tech</span></a>
                
                <!-- Desktop -->
                <div class="d-none d-lg-flex align-items-center">
                    <a class="nav-link ms-4 text-light fs-6 align-items-center d-flex" href="{{ route('catalog') }}"> 
                        Catalog<i class="bi bi-grid ms-2 text-light fs-5"></i>
                    </a>
                    <a class="nav-link ms-4 text-light fs-6 align-items-center d-flex" href="{{ route('about') }}"> 
                        About Us<i class="bi bi-people ms-2 text-light fs-5"></i>
                    </a>
                </div>
                
                <!-- Navigation Actions -->
                <div class="d-flex ms-auto align-items-center">
                    @auth
                        <!-- Notifications -->
                        @if(auth()->user()->buyer)
                            @include('layouts.partials.notifications-dropdown')
                        @endif

                        <!-- Desktop Right Side -->
                        <div class="d-none d-lg-flex align-items-center">
                            @if(auth()->user()->buyer)
                                <a class="nav-link ms-3 text-light" href="{{ url('/cart') }}"><i class="bi bi-cart me-1 fs-5"></i></a>
                                <a class="nav-link ms-3 text-light" href="{{ url('/wishlist') }}"><i class="bi bi-heart text-light fs-5"></i></a>
                                <a class="nav-link ms-3 text-light" href="{{ url('/profile') }}"><i class="bi bi-person me-1 text-light fs-4"></i></a>
                            @elseif(auth()->user()->admin)
                                <a class="nav-link ms-3 text-light" href="{{ route('dashboard') }}">Dashboard</a>
                            @endif
                            <a class="btn text-light bg-primary btn-sm ms-4 botao" href="{{ url('/logout') }}" style="white-space: nowrap;">Logout</a>
                        </div>

                        <!-- Mobile Right Side (Dropdown) -->
                        @include('layouts.partials.mobile-menu')
                        
                    <!-- Guests -->
                    @else
                         <!-- Guest Desktop -->
                        <div class="d-none d-lg-flex">
                            <a class="btn btn-dark me-4 btn-sm btn-login" href="{{ route('login') }}">Login</a>
                            <a class="btn btn-sm btn-primary text-light btn-register" href="{{ route('register') }}">Register</a>
                        </div>

                        <!-- Guest Mobile -->
                        @include('layouts.partials.mobile-menu-guest')
                    @endauth
                </div>
            </div>
        </nav>
        
        <!-- Success messages -->
        <main class="container mt-5 pt-3">
            <section class="mt-5" id="message-container">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </section>

            <section id="content">
                @yield('content')
            </section>
        </main>

        <script>
            // Auto-dismiss success alerts after 4 seconds
            document.addEventListener('DOMContentLoaded', function () {
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach(a => {
                    if (a.classList.contains('alert-success')) {
                        setTimeout(() => {
                            try { bootstrap.Alert.getOrCreateInstance(a).close(); } catch(e) {}
                        }, 4000);
                    }
                });

                // Listen for real-time notifications (only if user is authenticated and is a buyer)
                @auth
                    @if(auth()->user()->buyer)
                        const buyerId = {{ auth()->user()->buyer->id }};
                        
                        // Listen for order notifications
                        window.Echo.channel(`buyer.${buyerId}`).listen('.order.notification', (e) => {
                            console.log('Notification received:', e);
                            
                            // Update badge count
                            updateBadgeCount();
                            
                            // Show toast notification
                            showToastNotification(e.notification.title);
                            
                            // Add to dropdown (if on same page)
                            addNotificationToDropdown(e.notification);
                        });

                        // Listen for product added to cart
                        window.Echo.channel(`buyer.${buyerId}`).listen('.product.added.to.cart', (e) => {
                            console.log('Product added to cart:', e);
                            
                            const message = `${e.productName} (${e.quantity}x) added to cart!`;
                            showToastNotification(message, 'success');
                        });

                        // Listen for wishlist updates
                        window.Echo.channel(`buyer.${buyerId}`).listen('.wishlist.updated', (e) => {
                            console.log('Wishlist updated:', e);
                            
                            const message = e.action === 'added' 
                                ? `${e.productName} added to wishlist!`
                                : `${e.productName} removed from wishlist!`;
                            showToastNotification(message, 'success');
                        });

                        // Listen for cart updates
                        window.Echo.channel(`buyer.${buyerId}`).listen('.cart.updated', (e) => {
                            console.log('Cart updated:', e);
                            showToastNotification(e.message, 'success');
                        });

                        // Listen for review actions
                        window.Echo.channel(`buyer.${buyerId}`).listen('.review.action', (e) => {
                            console.log('Review action:', e);
                            showToastNotification(e.message, 'success');
                        });

                        // Listen for profile updates
                        window.Echo.channel(`buyer.${buyerId}`).listen('.profile.updated', (e) => {
                            console.log('Profile updated:', e);
                            showToastNotification('Profile updated successfully!', 'success');
                        });

                        // Listen for stock notifications
                        window.Echo.channel(`buyer.${buyerId}`).listen('.stock.notification', (e) => {
                            console.log('Stock notification received:', e);
                            
                            // Update badge count
                            updateBadgeCount();
                            
                            // Show toast notification
                            showToastNotification(e.notification.title);
                            
                            // Add to dropdown (if on same page)
                            addNotificationToDropdown(e.notification);
                        });

                        function updateBadgeCount() {
                            fetch('/notifications/unread-count')
                                .then(response => response.json())
                                .then(data => {
                                    const badge = document.querySelector('.badge.bg-danger');
                                    if (badge) {
                                        if (data.count > 0) {
                                            badge.textContent = data.count;
                                            badge.style.display = '';
                                        } else {
                                            badge.style.display = 'none';
                                        }
                                    }
                                });
                        }

                        function showToastNotification(message, type = 'primary') {
                            // Create toast element
                            const bgClass = type === 'success' ? 'bg-success' : 'bg-primary';
                            const icon = type === 'success' ? 'bi-cart-check-fill' : 'bi-bell-fill';
                            
                            const toastHtml = `
                                <div class="toast align-items-center text-white ${bgClass} border-0 position-fixed end-0 m-3" role="alert" style="top: 70px; z-index: 9999;">
                                    <div class="d-flex">
                                        <div class="toast-body">
                                            <i class="bi ${icon} me-2"></i>${message}
                                        </div>
                                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                                    </div>
                                </div>
                            `;
                            
                            const toastContainer = document.createElement('div');
                            toastContainer.innerHTML = toastHtml;
                            document.body.appendChild(toastContainer);
                            
                            const toastElement = toastContainer.querySelector('.toast');
                            const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
                            toast.show();
                            
                            // Remove from DOM after hidden
                            toastElement.addEventListener('hidden.bs.toast', () => {
                                toastContainer.remove();
                            });
                        }

                        function addNotificationToDropdown(notification) {
                            const dropdown = document.querySelector('#notificationsDropdown + .dropdown-menu');
                            if (!dropdown) return;
                            
                            const noNotifications = dropdown.querySelector('.text-muted.text-center');
                            if (noNotifications) {
                                noNotifications.remove();
                            }
                            
                            const newItem = document.createElement('li');
                            newItem.innerHTML = `
                                <a class="dropdown-item fw-bold" href="#" onclick="markAsRead(${notification.id}); return false;">
                                    <small class="text-muted d-block">${notification.date}</small>
                                    ${notification.title}
                                </a>
                            `;
                            
                            dropdown.insertBefore(newItem, dropdown.firstChild);
                        }
                    @endif
                @endauth
            });

            // Mark notification as read (without reload)
            function markAsRead(notificationId) {
                fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI without reload
                        const notificationItem = event.target.closest('.dropdown-item');
                        if (notificationItem) {
                            notificationItem.classList.remove('fw-bold');
                            notificationItem.classList.add('text-muted');
                        }
                        
                        // Update badge count
                        @auth
                            @if(auth()->user()->buyer)
                                const badge = document.querySelector('.badge.bg-danger');
                                if (badge) {
                                    let count = parseInt(badge.textContent) - 1;
                                    if (count > 0) {
                                        badge.textContent = count;
                                    } else {
                                        badge.style.display = 'none';
                                    }
                                }
                            @endif
                        @endauth
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        </script>
    </body>
</html>