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
            'KA-0411-G',
            'KA-0431-G-LB',
            'KA0431-LB',
            'KA0422-G',
            'KA0422-G',
            'GKJE01-G',
            'L18018-MW',
            'L18018-DW',
            'L18074-MW',
            'L18074-DW',
            'L18074-BLK',
            'AN18116-MW',
            'AN18116-SW',
            'AN18116-BLK',
            '052419-2-G',
            'AN18115-DW',
            'AN18115-MW',
            'KA0431-LB',
            'L18021-DW',
            'L18021-MW',
            'L18021-LW',
            'AN18117',
            '18087-2',
            'L19581-MW',
            'L19581-DW',
            'NJE26-DW',
            'NJE26-TS',
            'L18074-G'
        ];

        $order_products = DB::connection('mysql2')
            ->table('hw_orders')
            ->select('hw_orders.order_id', 'hw_order_details.product_id')
            ->join('hw_order_details', 'hw_order_details.order_id', 'hw_orders.order_id')
            ->join('hw_products', 'hw_products.product_id', 'hw_order_details.product_id')
            ->where('hw_products.ptype', 'P')
            ->whereIn('hw_order_details.product_code', $codes)
            ->where('hw_orders.timestamp', '<=', 1642809599)
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
