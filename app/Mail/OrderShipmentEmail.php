<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class OrderShipmentEmail extends Mailable
{
    use /*Queueable, */SerializesModels;

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
            ->select('hw_order_details.*', 'hw_product_descriptions.product AS product_name')
            ->join('hw_product_descriptions', 'hw_product_descriptions.product_id', 'hw_order_details.product_id')
            ->where('hw_order_details.order_id', $this->data['order_id'])
            ->whereIn('hw_order_details.product_id', $this->data['order_product_ids'])
            ->get();

        return $this->view('emails.order_shipment_email')->subject('Your Items has Shipped! - Lucky and Blessed')->with(['products' => $products]);
    }
}
