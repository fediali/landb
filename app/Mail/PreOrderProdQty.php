<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreOrderProdQty extends Mailable
{
    use Queueable, SerializesModels;

    public $data, $dates;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $dates)
    {
        $this->data = $data;
        $this->dates = $dates;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $date = date('m-d-Y', strtotime($this->dates['from_date'])).' to '.date('m-d-Y', strtotime($this->dates['to_date']));
        return $this->view('emails.pre_order_prod_qty')
            //->from('')
            ->subject('[L&B Pre Order Product Qty]['.$date.']')
            //->replyTo($this->data['email'])
            ->with(['data' => $this->data]);
    }
}
