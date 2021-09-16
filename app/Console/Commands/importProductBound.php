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

class importProductBound extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:bound-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Bound Products';


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
            $getBound = DB::table('hw_hw_bounded_products')->where('product_id', $product->id)->first();
            if ($getBound) {
                $getBoundProducts = DB::table('hw_hw_bounded_products')
                    ->where('bound_id', $getBound->bound_id)
                       // ->where('product_id', '!=', $product->id)
                    ->pluck('product_id')
                    ->all();
                if (count($getBoundProducts)) {
                    Product::where('id', $product->id)->update(['color_products' => json_encode($getBoundProducts)]);
                }
            }
            echo $product->sku.'<br>';
        }

    }
}
