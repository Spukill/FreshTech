<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CartUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $buyerId;

    public function __construct($message, $buyerId)
    {
        $this->message = $message;
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
        return 'cart.updated';
    }
}
