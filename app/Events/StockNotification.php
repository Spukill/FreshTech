<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;

class StockNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $buyerId;

    public function __construct(Notification $notification, $buyerId)
    {
        $this->notification = [
            'id' => $notification->id,
            'title' => $notification->title,
            'date' => $notification->date_not->diffForHumans(),
            'viewed' => $notification->viewed,
        ];
        $this->buyerId = $buyerId;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('buyer.' . $this->buyerId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'stock.notification';
    }
}
