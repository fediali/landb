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

class updateProductPackQty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:prod-qty';

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
            $getProd = DB::table('hw_products')->where('product_id', $product->id)->first();
            if ($getProd) {
                if ($getProd->amount) {
                    $product->quantity = 0;
                    if ($getProd->min_qty) {
                        $packQty = floor($getProd->amount / $getProd->min_qty);
                        $looseQty = $packQty * $getProd->min_qty;
                        $diff = $getProd->amount - $looseQty;
                        $product->quantity = $packQty;
                        $product->extra_qty = $diff;
                    } else {
                        $product->extra_qty = $getProd->amount;
                    }
                    $product->save();
                    echo 'Qty==>'.$product->sku.'<br>';
                }
                $sizes = DB::table('hw_product_options')
                    ->selectRaw('hw_product_option_variants_descriptions.variant_name')
                    ->leftJoin('hw_product_option_variants', 'hw_product_option_variants.option_id', 'hw_product_options.option_id')
                    ->leftJoin('hw_product_option_variants_descriptions', 'hw_product_option_variants_descriptions.variant_id', 'hw_product_option_variants.variant_id')
                    ->where('hw_product_options.product_id', $product->id)
                    ->orderBy('hw_product_options.product_id', 'ASC')
                    ->get();
                if (isset($sizes[0]->variant_name)) {
                    $product->sizes = $sizes[0]->variant_name;
                    $product->save();
                    echo 'Sizes==>'.$product->sizes.'<br>';
                }
            }
        }

    }
}
