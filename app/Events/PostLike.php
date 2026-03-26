<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostLike implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $post_id;

    public function __construct($post_id)
    {
        $this->post_id = $post_id;
        $this->message = 'You liked post '.$post_id;
    }

    public function broadcastOn(): array
    {
        // Define the channel to broadcast on. This is a public channel
        return [
            new Channel('notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        // Define the event name for the broadcast
        return 'post.like';
    }
}
