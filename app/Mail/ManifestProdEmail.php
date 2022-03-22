<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ManifestProdEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $skus;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($skus)
    {
        $this->skus = $skus;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        DB::connection('mysql2')->table('hw_product_manifest')->whereIn('style_no', $this->skus)->update(['email_sent_status' => 1]);
        return $this->view('emails.manifest_prod_email')->subject('Product Manifest! - Lucky and Blessed')->with(['skus' => $this->skus]);
    }
}
