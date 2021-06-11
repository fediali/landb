<?php

namespace App\Console\Commands;


use App\Imports\ImportProduct;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class importProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Products';


    protected $response;
    protected $productVariation;
    protected $productCategoryRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ProductVariationInterface $productVariation, ProductCategoryInterface $productCategoryRepository, BaseHttpResponse $response)
    {
        parent::__construct();
        $this->response = $response;
        $this->productVariation = $productVariation;
        $this->productCategoryRepository = $productCategoryRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = public_path('lnb-product.xlsx');
        Excel::import(new ImportProduct($this->productVariation, $this->productCategoryRepository, $this->response), $file);
        echo 'success';
    }
}
