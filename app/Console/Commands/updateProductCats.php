<?php

namespace App\Console\Commands;

use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class updateProductCats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:product-cats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Product Cats';


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
            $catIds = [];
            $getCats = DB::table('hw_products_categories')
                ->join('hw_category_descriptions', 'hw_category_descriptions.category_id', 'hw_products_categories.category_id')
                ->where('hw_products_categories.product_id', $product->id)
                ->pluck('hw_category_descriptions.category')
                ->all();
            if ($getCats) {
                foreach ($getCats as $cat) {
                    $catId = ProductCategory::where('name', $cat)->value('id');
                    if (!$catId) {
                        $category = new ProductCategory();
                        $category->name = $cat;
                        $category->save();
                        $catId = $category->id;
                    }
                    $catIds[] = $catId;
                }
            }
            if (count($catIds)) {
                $product->categories()->sync($catIds);
            }
            echo $cat.'<br>';
        }
    }
}
