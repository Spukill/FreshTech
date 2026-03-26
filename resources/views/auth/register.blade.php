@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center mb-4">
                <h2>Join FreshTech</h2>
                <p class="text-muted">Create your account to start shopping!</p>
            </div>
            <form method="POST" action="{{ route('register') }}" class="p-4 border rounded log">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label small">Name</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        class="form-control form-control-sm"
                    >
                    @error('name')
                        <div class="text-danger mt-1 fs-6">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label small">E-mail</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        inputmode="email"
                        class="form-control form-control-sm"
                    >
                    @error('email')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        class="form-control form-control-sm"
                    >
                    @error('password')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password-confirm" class="form-label small">Confirm Password</label>
                    <input
                        id="password-confirm"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="form-control form-control-sm"
                    >
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm botao">Register</button>
                        <a class="btn btn-outline-primary ms-2 btn-sm botao1" href="{{ route('login') }}">Login</a>
                    </div>
                    <a href="{{ route('google-auth') }}" class="btn d-flex align-items-center" style="background-color: #fff; border: 1px solid #dadce0; color: #3c4043; padding: 8px 16px; border-radius: 4px; text-decoration: none; transition: background-color 0.2s, box-shadow 0.2s; font-size: 14px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" class="me-2">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span style="font-weight: 500; font-size: 14px;">Google</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection