<?php

namespace Botble\Base\Events;

use Eloquent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use stdClass;

class CreatedContentEvent extends Event implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public $screen;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Eloquent|false
     */
    public $data;

    /**
     * CreatedContentEvent constructor.
     * @param string $screen
     * @param Request $request
     * @param Eloquent|false|stdClass $data
     */
    public function __construct($screen, $request, $data, $manager)
    {
        $this->screen = $screen;
        $this->request = $request;
        $this->data = $data;
        $this->manager = $manager;
    }

  /**
   * Get the channels the event should broadcast on.
   *
   * @return \Illuminate\Broadcasting\Channel|array
   */
  public function broadcastOn()
  {
    return new PrivateChannel('thread-notification');
  }
}
