<?php

namespace App\Events;

use Botble\Thread\Models\Thread;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class NotifyManager implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    protected $vendor;
    protected $thread;
    protected $designer;
    public function __construct($vendor, $designer, $thread)
    {
        $this->thread = $thread;
        $this->vendor = $vendor;
        $this->designer = $designer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('push_thread_notification_'.$this->vendor->id);
    }

    public function broadcastAs()
    {
      return 'ThreadEvent';
    }

    public function broadcastWith()
    {
      return ['title'=>'A thread has been added by Designer ( '.$this->designer->first_name.' '.$this->designer->last_name.' )', 'thread' => $this->thread];
    }
}
