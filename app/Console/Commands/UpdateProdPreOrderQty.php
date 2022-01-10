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
        DB::connection('mysql2')
            ->table('hw_products')
            //->where('hw_products.product_id', 114388)
            ->orderBy('hw_products.product_id', 'ASC')
            ->chunk(500, function ($products) {
                foreach ($products as $product) {
                    $row = (array)$product;
                    if ($row['product_id']) {
                        $product = DB::connection('mysql2')
                            ->table('hw_order_details')
                            ->selectRaw('COUNT(hw_order_details.order_id) AS sum_order')
                            ->selectRaw('SUM(hw_order_details.amount) AS sum_quantity')
                            ->join('hw_orders', 'hw_orders.order_id', 'hw_order_details.order_id')
                            ->where('hw_orders.status', 'AZ')
                            ->where('hw_order_details.product_id', $row['product_id'])
                            ->groupBy('hw_order_details.product_code')
                            ->first();

                        if($product){
                            DB::connection('mysql2')
                                ->table('hw_products')
                                ->where('hw_products.product_id', $row['product_id'])
                                ->update(['quantity_preorder' => $product->sum_quantity, 'count_preorder' => $product->sum_order]);
                        }

                    }
                }
            });
        echo 'success';
    }
}
