<?php

namespace App\Console\Commands;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Cart\Cart;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class remDisabledProdFromCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:disabled-cart-prod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Disabled Product From Cart';


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
        $carts = Order::where('is_finished', 0)->get();
        foreach ($carts as $cart) {
            foreach ($cart->products as $product) {
                if ($product->product->status != BaseStatusEnum::ACTIVE) {
                    OrderProduct::where(['order_id' => $cart->id, 'product_id' => $product->product_id])->delete();
                }
                echo 'Order Product#'.$product->product_id.'<br>';
            }
            echo 'Order#'.$cart->id.'<br>';
        }
    }
}
