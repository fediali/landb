<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderEditAccess implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('order-edit-access-' . $this->data->user_id);
    }

    public function broadcastAs()
    {
        return 'orderEditAccess';
    }

    public function broadcastWith()
    {
        return [
            'user_id'  => $this->data->user_id,
            'user_name'  => $this->data->user_name,
            'order_id' => $this->data->order_id,
            'time'     => now()->toDateTimeString(),
        ];
    }

}
