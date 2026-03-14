<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderId;
    public $status;

    /**
     * Create a new event instance.
     */
    public function __construct($orderId, $status)
    {
        $this->orderId = $orderId;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // We create a specific channel for this exact order
        return [
            new Channel('order.' . $this->orderId),
        ];
    }

    /**
     * Give the event a specific name so Javascript can listen for it
     */
    public function broadcastAs()
    {
        return 'payment.updated';
    }
}