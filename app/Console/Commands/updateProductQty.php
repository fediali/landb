<?php

namespace App\Console\Commands;

use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Thread\Models\Thread;
use Botble\Threadorders\Models\Threadorders;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class updateProductQty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:product-qty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Product Qty';


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
        $products = Product::all();

        foreach ($products as $product) {

            $getPackId = ProductVariation::where('configurable_product_id', $product->id)->where('is_default', 1)->value('product_id');
            $packQty = Product::where('id', $getPackId)->value('quantity');

            $getSingleIds = ProductVariation::where('configurable_product_id', $product->id)->where('is_default', 0)->pluck('product_id')->all();
            $singleQty = Product::whereIn('id', $getSingleIds)->sum('quantity');

            $getProdIds = ProductVariation::where('configurable_product_id', $product->id)->pluck('product_id')->all();
            $getProdIds[] = $product->id;

            $soldQty = OrderProduct::join('ec_orders', 'ec_orders.id', 'ec_order_product.order_id')
                ->whereIn('product_id', $getProdIds)
                ->whereNotIn('status', [OrderStatusEnum::CANCELED, OrderStatusEnum::PENDING])
                ->sum('qty');

            $pre_orderQty = OrderProduct::join('ec_orders', 'ec_orders.id', 'ec_order_product.order_id')
                ->whereIn('product_id', $getProdIds)
                ->where('order_type', Order::PRE_ORDER)
                ->whereNotIn('status', [OrderStatusEnum::CANCELED, OrderStatusEnum::PENDING])
                ->sum('qty');

            $inCartQty = OrderProduct::join('ec_orders', 'ec_orders.id', 'ec_order_product.order_id')
                ->whereIn('product_id', $getProdIds)
                ->where('ec_orders.is_finished', 0)
                ->sum('qty');

            $getProdSKU = Product::where('id', $product->id)->value('sku');
            $reorderQty = Threadorders::join('thread_order_variations', 'thread_order_variations.thread_order_id', 'threadorders.id')
                ->leftJoin('inventory_history', 'inventory_history.order_id', 'threadorders.id')
                ->where('thread_order_variations.sku', $getProdSKU)
                ->where('threadorders.order_status', Thread::REORDER)
                ->whereNull('inventory_history.order_id')
                ->value('thread_order_variations.quantity');

            $product->quantity = $packQty;
            $product->single_qty = $singleQty;
            $product->sold_qty = $soldQty;
            $product->pre_order_qty = $pre_orderQty;
            $product->in_cart_qty = $inCartQty;
            $product->reorder_qty = $reorderQty;

            $product->save();

            echo $product->sku.'<br>';
        }

    }
}
