<?php

namespace App\Console\Commands;

use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductAttributeSet;
use Botble\Ecommerce\Models\ProductCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class updateProductAttributes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:product-attribute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Product Attributes';


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
        $getAttributeSets = ProductAttributeSet::whereIn('type', ['size', 'type'])->pluck('id')->all();
        $products = Product::where('is_variation', 0)->where('id', 113456)->get();
        foreach ($products as $product) {
            if ($product->productAttributeSets->pluck('attribute_set_id')->all() != $getAttributeSets) {
                $product->productAttributeSets()->sync($getAttributeSets);
            }
        }
    }
}
