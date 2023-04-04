<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManifestProdDelayedEmail extends Mailable
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
        return $this->view('emails.manifest_prod_delayed_email')
            ->subject('Manifest Product has Delayed! - Lucky and Blessed')
            ->with([
                'product' => $this->data,
            ]);
    }
}
