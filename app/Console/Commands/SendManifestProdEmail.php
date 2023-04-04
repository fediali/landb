<?php

namespace App\Console\Commands;

use App\Mail\ManifestProdEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendManifestProdEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:manifest-prod-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Manifest Product Email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = DB::connection('mysql2')->table('hw_product_manifest')
            ->where('shipped', 'no')
            ->where('email_sent_status', 0)
            ->get();
        $skus = [];
        foreach ($products as $product) {
            $to   = Carbon::now();
            $from = Carbon::parse($product->from_eta_online);
            $diff_in_days = 0;
            if ($from >= $to) {
                $diff_in_days = $to->diffInDays($from);
            }
            if ($diff_in_days > 0 && $diff_in_days <= 20) {
                $skus[] = $product->style_no;
            }
        }
        if (count($skus)) {
            Mail::to(['farhad.surani@gmail.com'])->send(new ManifestProdEmail($skus));
        }
        echo 'success';
    }
}
