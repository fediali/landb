<?php

namespace App\Events;

use Botble\Thread\Models\Thread;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class NotifyManager implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    protected $user_id;
    protected $data;
    protected $thread;
    public function __construct($user_id, $data, $thread)
    {
        $this->user_id = $user_id;
        $this->data = $data;
        $this->thread = $thread;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('push_thread_notification_'.$this->user_id);
    }

    public function broadcastAs()
    {
      return 'ThreadEvent';
    }

    public function broadcastWith()
    {
      return ['message'=>$this->data->message, 'thread' => $this->thread,'url' => $this->data->url, 'created_at' => $this->thread->created_at->diffForHumans(), ''];
    }
}
