<?php

namespace App\Jobs;

use App\Mail\ManifestProdDelayedEmail;
use App\Mail\OrderShipmentEmail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class SendManifestProdDelayedEmail implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $id;

    /**
     * SendManifestProdDelayedEmail constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product = DB::connection('mysql2')
            ->table('hw_product_manifest')
            ->where('hw_product_manifest.id', $this->id)
            ->first();

        $emails = [
            'farhad.surani@gmail.com',

            'hassan@landbapparel.com',
            'luis.garza@luckyfactory.com.mx',
            'luis.parra@luckyfactory.com.mx',

            'jesus.arredondo@luckyfactory.com.mx',
            'heron.femat@landbapparel.com',
            'ramsha@landbapparel.com',
        ];

        Mail::to($emails)->send(new ManifestProdDelayedEmail($product));
    }
}
