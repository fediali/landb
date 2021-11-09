<?php

namespace App\Console\Commands;


use App\Imports\ImportProduct;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class updateProductPackQtyDisabled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:disable-prod-qty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Disabled Product Qty';


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
        $products = DB::connection('mysql2')->table('hw_products')->where(/*'status', 'D'*/ 'product_id', 91010)->get();

        foreach ($products as $product) {
            $getProd = Product::where('id', $product->product_id)->first();
            if ($getProd) {
dd($product->amount);
                if ($product->amount) {
                    dd();
                    $getProd->single_qty = 0;
                    $getProd->quantity = 0;
                    if ($product->min_qty) {
                        $packQty = floor($product->amount / $product->min_qty);
                        $looseQty = $packQty * $product->min_qty;
                        $diff = $product->amount - $looseQty;
                        $getProd->quantity = $packQty;
                        $getProd->extra_qty = $diff;
                    } else {
                        $getProd->extra_qty = $product->amount;
                    }
                    dd($getProd);
                    $getProd->save();
                    echo 'Qty==>'.$getProd->sku.'<br>';
                }

                $sizes = DB::connection('mysql2')->table('hw_product_options')
                    ->selectRaw('hw_product_option_variants_descriptions.variant_name')
                    ->leftJoin('hw_product_option_variants', 'hw_product_option_variants.option_id', 'hw_product_options.option_id')
                    ->leftJoin('hw_product_option_variants_descriptions', 'hw_product_option_variants_descriptions.variant_id', 'hw_product_option_variants.variant_id')
                    ->where('hw_product_options.product_id', $product->product_id)
                    ->orderBy('hw_product_options.product_id', 'ASC')
                    ->get();

                if (isset($sizes[0]->variant_name)) {
                    $getProd->sizes = $sizes[0]->variant_name;
                    $getProd->save();

                    foreach ($getProd->variations as $variation) {
                        $variation->product->sizes = $sizes[0]->variant_name;
                        $variation->product->single_qty = 0;
                        $variation->product->quantity = 0;
                        if ($variation->product->sku == $getProd->sku) {
                            $variation->product->quantity = $getProd->quantity;
                        }
                        $variation->product->save();
                    }

                    echo 'Sizes==>'.$getProd->sizes.'<br>';
                }

            }
        }

    }
}
