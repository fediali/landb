<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePreOrderProdEtaJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:pre-order-prod-eta-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Pre-Order Product ETA Job';

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
        $order_products = DB::connection('mysql2')
            ->table('hw_orders')
            ->select('hw_orders.order_id', 'hw_order_details.product_id')
            ->join('hw_order_details', 'hw_order_details.order_id', 'hw_orders.order_id')
            ->join('hw_products', 'hw_products.product_id', 'hw_order_details.product_id')
            ->where('hw_products.ptype', 'P')
            ->where('eta_from', 0)
            ->where('eta_to', 0)
            ->where('hw_orders.timestamp', '>=', strtotime(date('Y-m-d')))
            ->where(function($q){
                $q->where('hw_orders.status', 'AR');
                $q->orWhere('hw_orders.status', 'AZ');
                $q->orWhere('hw_orders.status', 'B');
                $q->orWhere('hw_orders.status', 'BB');
                $q->orWhere('hw_orders.status', 'BC');
                $q->orWhere('hw_orders.status', 'AQ');
                $q->orWhere('hw_orders.status', 'BA');
            })
            ->get();
        foreach ($order_products as $order_product) {
            $product = DB::connection('mysql2')
                ->table('hw_products')
                ->select('avail_since', 'avail_to')
                ->where('avail_since', '!=', 0)
                ->where('avail_to', '!=', 0)
                ->where('product_id', $order_product->product_id)
                ->first();
            if ($product && $product->avail_since && $product->avail_to) {
                DB::connection('mysql2')
                    ->table('hw_order_details')
                    ->where(['order_id' => $order_product->order_id, 'product_id' => $order_product->product_id])
                    ->update(['eta_from' => $product->avail_since, 'eta_to' => $product->avail_to]);
            }
        }

        echo 'success';
    }
}
