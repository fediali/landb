<?php

namespace App\Jobs;

use App\Mail\OrderShipmentEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class SendOrderProdShipmentEmail implements ShouldQueue
{
    use Dispatchable, /*InteractsWithQueue, Queueable,*/ SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $order_id, $order_product_ids = [];

    /**
     * SendOrderProdShipmentEmail constructor.
     * @param $order_id
     * @param $order_product_ids
     */
    public function __construct($order_id, $order_product_ids)
    {
        $this->order_id = $order_id;
        $this->order_product_ids = $order_product_ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = ['order_id' => $this->order_id, 'order_product_ids' => $this->order_product_ids];
        $user_email = DB::connection('mysql2')->table('hw_orders')->where('order_id', $this->order_id)->value('email');
        $srep_email = DB::connection('mysql2')
            ->table('hw_orders')
            ->join('hw_hw_user_extra_fields', 'hw_hw_user_extra_fields.user_id', 'hw_orders.user_id')
            ->join('hw_hw_srep', 'hw_hw_srep.srep_id', 'hw_hw_user_extra_fields.srep_id')
            ->join('hw_users', 'hw_users.user_id', 'hw_hw_srep.srep_id')
            ->where('hw_orders.order_id', $this->order_id)
            ->value('hw_users.email');

        $emails = [];
        if ($srep_email) {
            // array_push($emails, $srep_email);
            $srep_email = 'heron.femat@landbapparel.com';
            array_push($emails, $srep_email);
        }

        Mail::to([$user_email])->cc($emails)->send(new OrderShipmentEmail($data));
    }
}
