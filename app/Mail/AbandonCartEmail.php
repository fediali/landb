<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AbandonCartEmail extends Mailable
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
        $sessions = DB::connection('mysql2')
            ->table('hw_user_session_products')
            ->select('hw_user_session_products.*','hw_product_descriptions.product AS product_name')
            ->join('hw_product_descriptions', 'hw_product_descriptions.product_id', 'hw_user_session_products.product_id')
            ->where('hw_user_session_products.user_id', $this->data->user_id)
            ->orderBy('hw_user_session_products.timestamp', 'DESC')
            ->get();

        $email_sent_status = $this->data->email_sent_status == 0 ? 1 : 2;

        DB::connection('mysql2')
            ->table('hw_user_session_products')
            ->where('user_id', $this->data->user_id)
            ->update(['email_sent_status' => $email_sent_status]);

        return $this->view('emails.abandon_cart_email')->subject('Check out before it sells out! -
Lucky and Blessed')->with(['sessions' => $sessions]);
    }
}
