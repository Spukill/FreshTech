@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center mb-4">
                <h2>Reset your password</h2>
                <p class="text-muted">Enter your new password below.</p>
            </div>

            <form method="POST" action="{{ route('password.reset.post') }}" class="p-4 border rounded">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="password" class="form-label small">New Password</label>
                    <input id="password" name="password" type="password" required class="form-control form-control-sm">
                    @error('password')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label small">Confirm New Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="form-control form-control-sm">
                    @error('password_confirmation')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-sm">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection