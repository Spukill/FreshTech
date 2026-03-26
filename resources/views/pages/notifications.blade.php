@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Notifications</h2>
        @if($notifications->where('viewed', false)->count() > 0)
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-check-all"></i> Mark all as read
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($notifications->count() > 0)
        <div class="list-group">
            @foreach($notifications as $notification)
                <div class="list-group-item {{ $notification->viewed ? '' : 'list-group-item-primary' }}">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 {{ $notification->viewed ? '' : 'fw-bold' }}">
                                @if(!$notification->viewed)
                                    <span class="badge bg-primary me-2">New</span>
                                @endif
                                {{ $notification->title }}
                            </h6>
                            <small class="text-muted">
                                <i class="bi bi-clock"></i> {{ $notification->date_not->diffForHumans() }}
                                <span class="text-muted ms-2">{{ $notification->date_not->format('d/m/Y H:i') }}</span>
                            </small>
                        </div>
                        @if(!$notification->viewed)
                            <button 
                                class="btn btn-sm btn-outline-secondary ms-2" 
                                onclick="markAsRead({{ $notification->id }})"
                                title="Mark as read">
                                <i class="bi bi-check"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-bell-slash" style="font-size: 3rem; color: #ccc;"></i>
            <p class="text-muted mt-3">You don't have any notifications yet.</p>
            <a href="{{ route('catalog') }}" class="btn btn-primary">Browse Products</a>
        </div>
    @endif
</div>

<script>
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
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endsection
