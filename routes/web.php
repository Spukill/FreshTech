<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\GoogleOAuthController;
use App\Http\Controllers\FileController;

use App\Http\Controllers\HomeController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// About Us
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Pusher Demo
Route::get('/pusher-demo', function () {
    return view('pusher-demo');
})->name('pusher.demo');

Route::post('/post/{id}/like', [PostController::class, 'like'])->name('post.like');

// File Upload
Route::middleware('auth')->post('/file/upload', [FileController::class, 'upload'])->name('file.upload');

// Profile
Route::middleware('auth')->controller(ProfileController::class)->group(function () {
    Route::get('/profile', 'showProfile')->name('profile');
    Route::post('/profile', 'editProfile');
    Route::get('/profile/orders/{order}', 'showOrder');
    Route::get('/profile/orders/{order}/cancel', 'cancelOrder');
});

// Wishlist
Route::middleware('auth')->controller(WishlistController::class)->group(function () {
    Route::get('/wishlist', 'showWishlist')->name('wishlist');
    Route::post('/wishlist/{product}', 'addToWishlist')->name('wishlist.add');
    Route::delete('/wishlist/{product}', 'removeFromWishlist')->name('wishlist.remove');
});

// Notifications
Route::middleware('auth')->controller(NotificationController::class)->group(function () {
    Route::get('/notifications', 'index')->name('notifications.index');
    Route::post('/notifications/{id}/read', 'markAsRead')->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-read', 'markAllAsRead')->name('notifications.markAllRead');
    Route::get('/notifications/unread-count', 'unreadCount')->name('notifications.unreadCount');
});

// Cart
Route::middleware('auth')->controller(CartController::class)->group(function () {
    Route::get('/cart', 'showCart')->name('cart');
    Route::post('/cart/{productId}', 'addToCart')->name('cart.add');
    Route::delete('/cart/{productId}', 'removeFromCart')->name('cart.remove');
    Route::patch('/cart/{productId}', 'updateQuantity')->name('cart.update');
    // Checkout moved to CheckoutController for proper flow
    // Route::get('/cart/checkout', 'checkout')->name('checkout');
});

// Checkout - Now uses dedicated CheckoutController
Route::middleware('auth')->controller(CheckoutController::class)->group(function () {
    Route::get('/checkout', 'showCheckout')->name('checkout');
    Route::post('/checkout', 'processCheckout')->name('checkout.process');
    Route::get('/checkout/success/{order}', 'success')->name('checkout.success');
});

// Promotions - Now uses dedicated PromotionController
Route::middleware('auth')->controller(PromotionController::class)->group(function () {
    Route::get('/promotions', 'show')->name('promotions.show');
    Route::get('/promotions/{promo}', 'get')->name('promotions.get');
    Route::post('/promotions/create', 'create')->name('promotions.create');
    Route::patch('/promotions/{promotion}', 'edit')->name('promotions.edit');
    Route::delete('/promotions/{promotion}', 'delete')->name('promotions.destroy');
});

Route::middleware('auth')->controller(ReportController::class)->group(function () {
    Route::get('/reports', 'show')->name('reports.show');
    Route::get('/reports/{id}', 'get')->name('reports.get');
    Route::post('/reports/create', 'create')->name('reports.create');
    Route::patch('/reports/handle', 'update')->name('reports.update');
});

// Catalog
Route::controller(CatalogController::class)->group(function () {
    Route::get('/catalog', 'showCatalog')->name('catalog');
});

// ----------------------------
// Product routes
// ----------------------------

// Categories
Route::get('/categories/manage', [CategoryController::class, 'manage'])->name('categories.manage');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::get('/categories/{category}/json', [CategoryController::class, 'editJson'])->name('categories.json');
Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

// Fixed routes first: manage, create, stock
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::get('/products/manage', [ProductController::class, 'manage'])->name('products.manage');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::patch('/products/{product}/stock', [ProductController::class, 'updateStock'])->name('products.updateStock');

// Edit, update, delete
Route::patch('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::get('/products/{product}/json', [ProductController::class, 'editJson']);
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

// Reviews (after product edit routes, before generic show)
Route::middleware('auth')->controller(ReviewController::class)->group(function () {
    Route::post('/products/{product}/reviews', 'store')->name('reviews.store');
    Route::delete('/products/{product}/reviews', 'delete')->name('reviews.delete');
    Route::get('/reviews/{review}/edit', 'edit')->name('reviews.edit');
    Route::put('/reviews/{review}', 'update')->name('reviews.update');
});

// Generic catch-all product details page
Route::get('/products/{product}', [ProductController::class, 'show'])->name('product.show');


// Dashboard
Route::middleware('auth')->controller(DashboardController::class)->group(function () {
    Route::get('/dashboard', 'showDashboard')->name('dashboard');
});

// Dashboard - Manage Orders
Route::middleware('auth')->controller(OrderController::class)->group(function () {
    Route::get('/orders/manage', 'manage')->name('orders.manage');
    Route::get('/orders/{order}', 'details')->name('orders.details');  
    Route::patch('/orders/{order}', 'update')->name('orders.update');
});

// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
});

Route::controller(LogoutController::class)->group(function () {
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

// Google OAuth
Route::controller(GoogleOAuthController::class)->group(function () {
    Route::get('auth/google', 'redirect')->name('google-auth');
    Route::get('auth/google/callback', 'callbackGoogle')->name('google-call-back');
});

// Recover Password
Route::get('/password/forgot', function() { return view('auth.forgotPassword'); })->name('password.request');
Route::post('/send', [\App\Http\Controllers\MailController::class, 'send']);
Route::get('/password/reset/{token}', function($token) { return view('auth.passwords.reset', ['token' => $token]); })->name('password.reset');
Route::post('/password/reset', [\App\Http\Controllers\MailController::class, 'reset'])->name('password.reset.post');
