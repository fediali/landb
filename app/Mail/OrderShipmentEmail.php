<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class OrderShipmentEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $products = DB::connection('mysql2')
            ->table('hw_order_details')
            ->where('order_id', $this->data['order_id'])
            ->whereIn('product_id', $this->data['order_product_ids'])
            ->get();

        return $this->view('emails.order_shipment_email')->subject('Your Items has Shipped! - Lucky and Blessed')->with(['products' => $products]);
    }
}
