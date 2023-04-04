<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManifestProdShippedEmail extends Mailable
{
    use SerializesModels;

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
        return $this->view('emails.manifest_prod_shipped_email')
            ->subject('Manifest Product has Shipped! - Lucky and Blessed')
            ->with([
                'product' => $this->data,
            ]);
    }
}
