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

        //
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
        return new Channel('order-edit' . $this->user->id);
    }

    public function broadcastAs()
    {
        return 'orderEdit';
    }

//    public function broadcastWith()
//    {
//        return 'ss';
//
////        return [
////            'id'    => $this->user->id,
////            'order' => $this->order,
////            'on'    => now()->toDateTimeString(),
////        ];
//    }

    public function broadcastWith()
    {
        return ['message' =>
                    's'];
    }


}
