@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center mb-4">
                <h2>Reset your password</h2>
                <p class="text-muted">Enter your email and we'll send you a link to reset your password.</p>
            </div>

            <form method="POST" action="{{ url('/send') }}" class="p-4 border rounded">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label small">E-mail</label>
                    <input id="email" name="email" type="email" required class="form-control form-control-sm" value="{{ old('email') }}">
                    @error('email')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-sm">Send reset link</button>
                    <a href="{{ route('login') }}" class="btn btn-link btn-sm">Back to login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
