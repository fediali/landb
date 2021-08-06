<?php

namespace App\Console\Commands;


use App\Imports\ImportProduct;
use App\Models\InventoryHistory;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Categorysizes\Models\Categorysizes;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductAttribute;
use Botble\Ecommerce\Models\ProductAttributeSet;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Slug\Models\Slug;
use File;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use SlugHelper;

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
        /*$file = public_path('lnb-products-3000.xlsx');
        Excel::import(new ImportProduct($this->productVariation, $this->productCategoryRepository, $this->response), $file);*/

        $file = File::get(public_path('lnb-products-all.json'));
        $data = json_decode(utf8_encode($file), true);

        foreach ($data['rows'] as $row) {

            if ($row['product_id'] && $row['product_code'] && $row['category_id'] && $row['product'] && $row['category']) {

                $category = ProductCategory::where('name', $row['category'])->first();
                if (!$category && $row['category'] && $row['parent_id']) {
                    $category = new ProductCategory();
                    $category->name = $row['category'];
                    $category->parent_id = $row['parent_id'];
                    $category->save();
                }

                $check = Product::where('sku', $row['product_code'])->first();
                if (!$check && $category) {
                    $packQuantity = quantityCalculate($category->id);
                    $product = new Product();
                    $product->id = $row['product_id'];
                    $product->name = $row['product'];
                    $product->warehouse_sec = @$row['bin'];
                    if ($row['full_description']) {
                        $product->description = $row['full_description'];
                    }
                    if ($row['composition']) {
                        $product->content = $row['composition'];
                    }
                    if ($row['status']) {
                        $product->status = BaseStatusEnum::$STATUSES[$row['status']];
                    }
                    $product->cost_price = $row['cost_price'];
                    $product->creation_date = date('Y-m-d', $row['timestamp']);
                    $product->sku = $row['product_code'];
                    $product->prod_pieces = $row['min_qty'];
                    $product->sizes = $row['variant_name'];
                    $product->category_id = $category->id;
                    $product->quantity = 0;

                    $percentage = !is_null(setting('sales_percentage')) ? setting('sales_percentage') : 0;

                    $product->price = 0;
                    $singlePrice = 0;
                    if ($row['price']) {
                        if ($product->prod_pieces) {
                            $packQuantity = $product->prod_pieces;
                        }
                        $extras = ($row['price'] * $packQuantity) * $percentage / 100;
                        $packPrice = $row['price'] * $packQuantity ;
                        $single = $row['price'] * $percentage / 100;
                        $singlePrice = $row['price'];
                        $product->price = $packPrice;
                    }
                    // $product->sale_price = $variation->cost + $extras;

                    if ($row['image_path']) {
                        $product->images = json_encode([$row['image_path']]);
                    }
                    $product->tax_id = 1;

                    if ($row['upc_pack']) {
                        $product->upc = $row['upc_pack'];
                        try {
                            $product->barcode = get_barcode_by_upc($row['upc_pack'])['barcode'];
                        } catch (\ErrorException $exception) {}
                    }

                    if ($row['restock']) {
                        $product->restock = $row['restock'];
                    }
                    if ($row['new_label']) {
                        $product->new_label = $row['new_label'];
                    }
                    if ($row['usa_made']) {
                        $product->usa_made = $row['usa_made'];
                    }
                    if ($row['ptype']) {
                        $product->ptype = $row['ptype'];
                    }

                    if ($product->save()) {
                        $product->categories()->sync([$category->id]);
                        $product->productCollections()->detach();
                        $product->productCollections()->attach([1]);//new arrival
                        Slug::create([
                            'reference_type' => Product::class,
                            'reference_id' => $product->id,
                            'key' => Str::slug($product->name),
                            'prefix' => SlugHelper::getPrefix(Product::class),
                        ]);

                        $getTypeAttrSet = ProductAttributeSet::where('slug', 'type')->value('id');
                        if ($getTypeAttrSet) {
                            $getTypeAttrs = ProductAttribute::where('attribute_set_id', $getTypeAttrSet)->pluck('id')->all();
                            if ($getTypeAttrs) {
                                $product->productAttributeSets()->attach([$getTypeAttrSet]);
                                $product->productAttributes()->attach($getTypeAttrs);
                                $getSizeAttrSet = ProductAttributeSet::where('slug', 'size')->value('id');
                                if ($getSizeAttrSet) {
                                    $getCatSizes = Categorysizes::join('product_categories_sizes', 'categorysizes.id', 'product_categories_sizes.category_size_id')
                                        ->where('product_categories_sizes.product_category_id', $category->id)
                                        ->pluck('categorysizes.name')
                                        ->all();
                                    $getSizeAttrs = [];
                                    foreach ($getCatSizes as $getCatSize) {
                                        $sizeExist = ProductAttribute::where('slug', strtolower($getCatSize))->where('attribute_set_id', $getSizeAttrSet)->value('id');
                                        if ($sizeExist) {
                                            $getSizeAttrs[] = $sizeExist;
                                        } else {
                                            $sizeAttrData = ['attribute_set_id' => $getSizeAttrSet, 'title' => $getCatSize, 'slug' => strtolower($getCatSize)];
                                            $sizeAttr = ProductAttribute::create($sizeAttrData);
                                            if ($sizeAttr) {
                                                $getSizeAttrs[] = $sizeAttr->id;
                                            }
                                        }
                                    }


                                    $addedAttributes = [];
                                    $getTypePackAttr = ProductAttribute::where('attribute_set_id', $getTypeAttrSet)->where('slug', 'pack')->value('id');
                                    $addedAttributes[$getTypeAttrSet] = $getTypePackAttr;
                                    $getSizeAllAttr = ProductAttribute::where('attribute_set_id', $getSizeAttrSet)->where('slug', 'all')->value('id');
                                    $addedAttributes[$getSizeAttrSet] = $getSizeAllAttr;
                                    $result = $this->productVariation->getVariationByAttributesOrCreate($product->id, $addedAttributes);

                                    if ($result['created']) {
                                        app('eComProdContr')->postSaveAllVersions([$result['variation']->id => ['attribute_sets' => $addedAttributes]], $this->productVariation, $product->id, $this->response);
                                        ProductVariation::where('id', $result['variation']->id)->update(['is_default' => 1]);

                                        $prodId = ProductVariation::where('id', $result['variation']->id)->value('product_id');
                                        $packAllProd = Product::where('id', $prodId)->first();
//no barcode image need
                                        try {
                                            $barcodePackAll = get_barcode_by_upc($row['upc_pack']);
                                            $packAllProd->upc = $barcodePackAll['upc'];
                                            $packAllProd->barcode = $barcodePackAll['barcode'];
                                        } catch (\ErrorException $exception) {}

                                        //change
                                        $packAllProd->private_label = $product->private_label;
                                        $packAllProd->restock = $product->restock;
                                        $packAllProd->new_label = $product->new_label;
                                        $packAllProd->usa_made = $product->usa_made;
                                        $packAllProd->ptype = $product->ptype;
                                        $packAllProd->save();

                                        $logParam = [
                                            'parent_product_id' => $product->id,
                                            'product_id' => $prodId,
                                            'sku' => $packAllProd->sku,
                                            'created_by' => 1,
                                            'reference' => InventoryHistory::PROD_PUSH_ECOM
                                        ];
                                        log_product_history($logParam, false);
                                    }

                                    if (count($getSizeAttrs)) {
                                        $product->productAttributeSets()->attach([$getSizeAttrSet]);
                                        $product->productAttributes()->attach($getSizeAttrs);

                                        foreach ($getSizeAttrs as $getSizeAttr) {
                                            $addedAttributes = [];
                                            $getTypeSingleAttr = ProductAttribute::where('attribute_set_id', $getTypeAttrSet)->where('slug', 'single')->value('id');
                                            $addedAttributes[$getTypeAttrSet] = $getTypeSingleAttr;
                                            $addedAttributes[$getSizeAttrSet] = $getSizeAttr;
                                            $result = $this->productVariation->getVariationByAttributesOrCreate($product->id, $addedAttributes);
                                            if ($result['created']) {
                                                app('eComProdContr')->postSaveAllVersions([$result['variation']->id => ['attribute_sets' => $addedAttributes]], $this->productVariation, $product->id, $this->response);

                                                $prodId = ProductVariation::where('id', $result['variation']->id)->value('product_id');
                                                Product::where('id', $prodId)->update(['price' => $singlePrice]);
                                                $sizeProd = Product::where('id', $prodId)->first();

                                                //$barcodeSize = get_barcode();
                                                //$sizeProd->upc = $barcodeSize['upc'];
                                                //$sizeProd->barcode = $barcodeSize['barcode'];
                                                $sizeProd->private_label = $product->private_label;
                                                $sizeProd->restock = $product->restock;
                                                $sizeProd->new_label = $product->new_label;
                                                $sizeProd->usa_made = $product->usa_made;
                                                $sizeProd->ptype = $product->ptype;
                                                $sizeProd->save();

                                                $logParam = [
                                                    'parent_product_id' => $product->id,
                                                    'product_id' => $prodId,
                                                    'sku' => $sizeProd->sku,
                                                    'created_by' => 1,
                                                    'reference' => InventoryHistory::PROD_PUSH_ECOM
                                                ];
                                                log_product_history($logParam, false);

                                            }
                                        }
                                    }
                                }
                            }
                        }

                    }

                }

                //echo $check ? $check->sku : $row['product_code'].'\n';
                echo isset($product) ? $product->sku : '--no--'.'====';
            }
        }

        echo 'success';
    }
}
