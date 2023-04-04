<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateProdStatusLimitExceed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:prod-status-limit-exceed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Product Status When Pre-order Limit Exceeded';

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
     * @return string
     */
    public function handle()
    {
        $order_products = DB::connection('mysql2')
            ->table('hw_products')
            ->select('hw_products.product_id', 'hw_products.qty_preorder', 'hw_products.paid_preorder')
            ->where('hw_products.ptype', 'P')
            ->get();

        foreach ($order_products as $order_product) {
            if ($order_product->paid_preorder >= $order_product->qty_preorder) {
                $order_product->status = 'D';
                $order_product->save();
            }
        }

        echo 'success';
    }
}
