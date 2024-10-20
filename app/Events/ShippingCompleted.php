<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShippingCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderId;
    public $productId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($orderId, $productId)
    {
        $this->orderId = $orderId;
        $this->productId = $productId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
