<?php

namespace App\Jobs;

use App\Mail\ManifestProdShippedEmail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class SendManifestProdShippedEmail implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $id;

    /**
     * SendManifestProdShippedEmail constructor.
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

        $sales_rep_emails = DB::connection('mysql2')
            ->table('hw_hw_srep')
            ->whereNotNull('email')
            ->pluck('email')
            ->all();

        Mail::to($sales_rep_emails)->send(new ManifestProdShippedEmail($product));
    }
}
