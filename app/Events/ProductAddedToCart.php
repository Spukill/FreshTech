<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductAddedToCart implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $productName;
    public $quantity;
    public $buyerId;

    public function __construct($productName, $quantity, $buyerId)
    {
        $this->productName = $productName;
        $this->quantity = $quantity;
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
        return 'product.added.to.cart';
    }
}
