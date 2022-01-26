<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePreOrderProductEta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:pre-order-prod-eta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Pre-Order Product ETA';

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
        $codes = [
            'JJE1-DW',
            'JJE5-MW',
            'JJE5-DW',
            'L17061-LW',
            'L17061-DW',
            'L18006',
            'L18027-DW',
            'L18027-MW',
            'L18052-DW',
            'L18052-MW',
            'L18052-BLK',
            'LB-8877-MW',
            'LB-8888-MID-ZIP',
            'LB-9879-ZIP-LW',
            'LB-9879-ZIP-MW',
            'LB-9881',
            'NJE04-LW',
            'NJE04-MW',
            'NSH10',
        ];

        $order_products = DB::connection('mysql2')
            ->table('hw_orders')
            ->select('hw_orders.order_id', 'hw_order_details.product_id')
            ->join('hw_order_details', 'hw_order_details.order_id', 'hw_orders.order_id')
            ->join('hw_products', 'hw_products.product_id', 'hw_order_details.product_id')
            ->where('hw_products.ptype', 'P')
            ->whereIn('hw_order_details.product_code', $codes)
            ->where('hw_orders.timestamp', '<=', 1643209200)
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
            DB::connection('mysql2')
                ->table('hw_order_details')
                ->where(['order_id' => $order_product->order_id, 'product_id' => $order_product->product_id])
                ->update(['eta_from' => '1645401600', 'eta_to' => '1645747200', 'from_query' => 1]);
        }

        echo 'success';
    }
}
