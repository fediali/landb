<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderEdit implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    protected $order;
    protected $user;

    public function __construct($user, $order)
    {
        $this->order = $order;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('order-edit-' . $this->order->id);
    }

    public function broadcastAs()
    {
        return 'orderEdit';
    }

    public function broadcastWith()
    {
        return [
            'user_id'  => $this->user->id,
            'user_name'  => $this->user->getFullName(),
            'order_id' => $this->order->id,
            'time'     => now()->toDateTimeString(),
        ];
    }

}
