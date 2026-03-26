<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WishlistUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $productName;
    public $action;
    public $buyerId;

    public function __construct($productName, $action, $buyerId)
    {
        $this->productName = $productName;
        $this->action = $action; // 'added' or 'removed'
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
        return 'wishlist.updated';
    }
}
