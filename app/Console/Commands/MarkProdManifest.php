<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkProdManifest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mark:prod-manifest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark Prod Manifest';

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
        $pre_products = DB::connection('mysql2')
            ->table('hw_products')
            ->where('hw_products.ptype', 'P')
            ->where('hw_products.avail_since', '!=', 0)
            ->where('hw_products.avail_to', '!=', 0)
            ->get();
        foreach ($pre_products as $pre_product) {
            if ($pre_product && $pre_product->avail_since && $pre_product->avail_to) {
                $batch = DB::connection('mysql2')->table('hw_product_manifest')->where(['product_id' => $pre_product->product_id])->orderBy('id', 'DESC')->value('batch_no');
                $where = [
                    'product_id' => $pre_product->product_id,
                    'from_eta_online' => $pre_product->avail_since,
                    'to_eta_online' => $pre_product->avail_to,
                ];
                $exist = DB::connection('mysql2')->table('hw_product_manifest')->where($where)->value('product_id');
                if (!$exist && $batch) {
                    $batch++;
                }
                $data = [
                    'product_id' => $pre_product->product_id,
                    'style_no' => $pre_product->product_code,
                    'from_eta_online' => $pre_product->avail_since,
                    'to_eta_online' => $pre_product->avail_to,
                    'qty_required' => $pre_product->quantity_preorder,
                    'batch_no' => $batch ? $batch : 1,
                ];
                DB::connection('mysql2')->table('hw_product_manifest')->updateOrInsert($where, $data);
            }
        }

        echo 'success';
    }
}
