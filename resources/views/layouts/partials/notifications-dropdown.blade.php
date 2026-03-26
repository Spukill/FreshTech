<!-- Notifications -->
@php
    $unreadCount = auth()->user()->buyer->notifications()->unread()->count();
    $recentNotifications = auth()->user()->buyer->notifications()->orderBy('date_not', 'desc')->take(5)->get();
@endphp

<div class="nav-item dropdown">
    <a class="nav-link text-light position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-bell fs-5"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                {{ $unreadCount }}
            </span>
        @endif
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown" style="min-width: 300px; max-height: 400px; overflow-y: auto;">
        @forelse($recentNotifications as $notification)
            <li>
                <a class="dropdown-item {{ $notification->viewed ? 'text-muted' : 'fw-bold' }}" 
                   href="#"
                   onclick="markAsRead({{ $notification->id }}); return false;">
                    <small class="text-muted d-block">{{ $notification->date_not->diffForHumans() }}</small>
                    {{ $notification->title }}
                </a>
            </li>
            @if(!$loop->last)
                <li><hr class="dropdown-divider"></li>
            @endif
        @empty
            <li class="dropdown-item text-muted text-center">No notifications</li>
        @endforelse

        @if($recentNotifications->count() > 0)
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-center text-primary" href="{{ route('notifications.index') }}">
                    View all notifications
                </a>
            </li>
        @endif
    </ul>
</div>
