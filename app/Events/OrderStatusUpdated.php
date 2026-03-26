<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $message;

    public function __construct($orderId, $status)
    {
        $this->message = "Order #{$orderId} status changed to {$status}";
    }

    public function broadcastOn()
    {
        return new Channel('notifications');
    }

    public function broadcastAs(): string
    {
        return 'order.status.notification';
    }
}
