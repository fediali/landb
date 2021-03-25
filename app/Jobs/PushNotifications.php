<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $type;
    protected $data1;
    protected $data2;
    protected $data3;
    protected $data4;
    public function __construct($type, $data1, $data2 = null, $data3 = null, $data4 = null)
    {
        $this->type = $type;
        $this->data1 = $data1;
        $this->data2 = $data2;
        $this->data3 = $data3;
        $this->data4 = $data4;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->type == 'notify_design_manager'){

        }
    }
}
