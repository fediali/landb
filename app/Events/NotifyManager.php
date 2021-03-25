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
    protected $vendor;
    protected $thread;
    protected $designer;
    protected $message;
    protected $url;
    public function __construct($vendor, $designer, $thread)
    {
        $this->thread = $thread;
        $this->vendor = $vendor;
        $this->designer = $designer;

        $this->message = 'A new thread has been created by the Designer ( '.$this->designer->first_name.' '.$this->designer->last_name.' )';
        $this->url = route('thread.details', $this->thread->id);

        generate_notification($this->message, $this->designer->id, $this->vendor->id, $this->url);
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
      return ['message'=>$this->message, 'thread' => $this->thread,'url' => $this->url, 'created_at' => $this->thread->created_at->diffForHumans(), ''];
    }
}
