<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateProdPreOrderQty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:prod-pre-order-qty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Product Pre Order Qty';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $row  = DB::connection('mysql2')
            ->table('hw_products')
            ->where('hw_products.ptype', 'P')
            ->where('hw_products.product_id', 116458)->first();

//            ->orderBy('hw_products.product_id', 'ASC')
//            ->chunk(500, function ($products) {
//                foreach ($products as $product) {
//                    $row = (array)$product;
//                    if ($row['product_id']) {
                        $product = DB::connection('mysql2')
                            ->table('hw_order_details')
                            ->selectRaw('COALESCE(COUNT(hw_order_details.order_id), 0) AS sum_order')
                            ->selectRaw( 'COALESCE(SUM(hw_order_details.amount), 0) AS sum_quantity')
                            ->join('hw_orders', 'hw_orders.order_id', 'hw_order_details.order_id')
                            ->where(function ($q) {
                                $q->where('hw_orders.status', 'AR');
                                $q->orWhere('hw_orders.status', 'AZ');
                                $q->orWhere('hw_orders.status', 'B');
                                $q->orWhere('hw_orders.status', 'L');
                                $q->orWhere('hw_orders.status', 'U');
                                $q->orWhere('hw_orders.status', 'BB');
                                $q->orWhere('hw_orders.status', 'BC');
                                $q->orWhere('hw_orders.status', 'AQ');
                                $q->orWhere('hw_orders.status', 'BA');
                            })
                            ->where(function ($query) use ($row) {
                                $query->where('hw_order_details.product_id', $row->product_id);
                                $query->where('hw_order_details.ship_status', 0);
                            })
                            ->groupBy('hw_order_details.product_code')
                            ->first();
                        dd($product, $product->sum_quantity, $product->sum_order);
                        if ($product) {
                            DB::connection('mysql2')
                                ->table('hw_products')
                                ->where('hw_products.product_id', $row['product_id'])
                                ->update(['quantity_preorder' => $product->sum_quantity, 'count_preorder' => $product->sum_order]);
                        }
//                    }
//                }
//            });
        echo 'success';
    }
}
