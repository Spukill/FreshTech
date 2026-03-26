@extends('layouts.app')

@section('content')
    <header>
        <h1>LBAW Tutorial 03 - Pusher Notifications</h1>
    </header>
    <main>
        <section class="posts">
            @for ($i = 1; $i <= 2; $i++)
                @include('partials.post', ['id' => $i])
            @endfor
        </section>
        @include('partials.notification')
    </main>
    <footer>
        <p>LBAW @ 2025</p>
    </footer>
@endsection
