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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use SlugHelper;

class fetchProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Products';


    protected $response;
    protected $productVariation;
    protected $productCategoryRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        ProductVariationInterface $productVariation,
        ProductCategoryInterface $productCategoryRepository,
        BaseHttpResponse $response
    )
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
        $this->insertProducts();
        echo 'success-inactive';
//        $this->bounds();
    }

    public function bounds()
    {
        $products = Product::all();

        foreach ($products as $product) {
            $getBound = DB::connection('mysql2')->table('hw_hw_bounded_products')->where('product_id', $product->id)->first();
            if ($getBound) {
                $getBoundProducts = DB::connection('mysql2')->table('hw_hw_bounded_products')
                    ->where('bound_id', $getBound->bound_id)
                    //->where('product_id', '!=', $product->id)
                    ->pluck('product_id')
                    ->all();
                if (count($getBoundProducts)) {
                    Product::where('id', $product->id)->update(['color_products' => json_encode($getBoundProducts)]);
                }
            }
            echo $product->sku . '<br>';
        }
    }

    public function insertProducts()
    {
        DB::connection('mysql2')
            ->table('hw_products')
            ->selectRaw('hw_products.product_id, hw_products.`product_code`, hw_products.`status`, hw_products.`bin`, hw_products.`upc_pack`, hw_products.`ptype`,
                        hw_products.`amount`, hw_products.`usa_made`, hw_products.`restock`, hw_products.`new_label`, hw_products.min_qty, hw_products.cost_price, hw_products.timestamp,
                        hw_product_descriptions.`product`, hw_product_descriptions.`full_description`, hw_product_descriptions.`composition`,
                        hw_products_categories.`category_id`, hw_category_descriptions.category, hw_categories.parent_id, hw_product_prices.`price`,
                        GROUP_CONCAT(hw_product_option_variants_descriptions.variant_name) AS variant_name')
            ->join('hw_product_descriptions', 'hw_product_descriptions.product_id', 'hw_products.product_id')
            ->join('hw_products_categories', 'hw_products_categories.product_id', 'hw_products.product_id')
            ->join('hw_categories', 'hw_categories.category_id', 'hw_products_categories.category_id')
            ->join('hw_category_descriptions', 'hw_category_descriptions.category_id', 'hw_categories.category_id')
            ->join('hw_product_prices', 'hw_product_prices.product_id', 'hw_products.product_id')
            ->leftJoin('hw_product_options', 'hw_product_options.product_id', 'hw_products.product_id')
            ->leftJoin('hw_product_option_variants', 'hw_product_option_variants.option_id', 'hw_product_options.option_id')
            ->leftJoin('hw_product_option_variants_descriptions', 'hw_product_option_variants_descriptions.variant_id', 'hw_product_option_variants.variant_id')
            ->where('hw_products.fetch_status', 0)
            ->groupBy('hw_products.product_id')
            ->orderBy('hw_products.product_id', 'ASC')
            ->chunk(500, function ($products) {

                foreach ($products as $product) {
                    $row = (array)$product;
                    if ($row['product_id'] && $row['product_code'] && $row['category_id'] && $row['product'] && $row['category']) {

                        $category = ProductCategory::where('name', $row['category'])->first();

                        if (!$category && $row['category'] /*&& $row['parent_id']*/) {
                            $category = new ProductCategory();
                            $category->name = $row['category'];
                            $category->parent_id = @$row['parent_id'];
                            $category->save();
                        }

                        $check = Product::where('id', $row['product_id'])->first();
                        if (!$check && $category) {
                            //$packQuantity = quantityCalculate($category->id);
                            $packQuantity = 0;
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
                            if ($row['amount']) {
                                if ($row['min_qty']) {
                                    $packQty = floor($row['amount'] / $row['min_qty']);
                                    $looseQty = $packQty * $row['min_qty'];
                                    $diff = $row['amount'] - $looseQty;
                                    $product->quantity = $packQty;
                                    $product->extra_qty = $diff;
                                } else {
                                    $product->extra_qty = $row['amount'];
                                }
                            }

                            $percentage = !is_null(setting('sales_percentage')) ? setting('sales_percentage') : 0;

                            $product->price = 0;
                            $singlePrice = 0;
                            if ($row['price']) {
                                if ($product->prod_pieces) {
                                    $packQuantity = $product->prod_pieces;
                                }
                                $extras = ($row['price'] * $packQuantity) * $percentage / 100;
                                $packPrice = $row['price'] * $packQuantity;
                                $single = $row['price'] * $percentage / 100;
                                $singlePrice = $row['price'];
                                $product->price = $packPrice;
                            }
                            // $product->sale_price = $variation->cost + $extras;

                            // if ($row['image_id'] && $row['image_path']) {

                            $getProdImages = DB::connection('mysql2')->table('hw_images_links')
                                ->select('hw_images.image_id', 'hw_images.image_path', 'hw_images_links.type')
                                ->join('hw_images', 'hw_images.image_id', 'hw_images_links.detailed_id')
                                ->where('hw_images_links.object_type', 'product')
                                ->where('hw_images_links.object_id', $row['product_id'])
                                ->orderBy('hw_images_links.type', 'DESC')
                                ->get();
                            $arrr = [];
                            foreach ($getProdImages as $getProdImage) {
                                $idLen = getDigitsLength($getProdImage->image_id);
                                if ($idLen <= 5) {
                                    $folder = substr($getProdImage->image_id, 0, 2);
                                } elseif ($idLen >= 6) {
                                    $folder = substr($getProdImage->image_id, 0, 3);
                                }
                                $arrr[] = 'product-images/detailed/' . $folder . '/' . $getProdImage->image_path;
                            }
                            $product->images = json_encode($arrr);
                            // }
                            $product->tax_id = 1;

                            if ($row['upc_pack']) {
                                $product->upc = $row['upc_pack'];
                                /*try {
                                    $product->barcode = get_barcode_by_upc($row['upc_pack'])['barcode'];
                                } catch (\ErrorException $exception) {}*/
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

                                $slugParams = [
                                    'reference_type' => Product::class,
                                    'reference_id'   => $product->id,
                                    'key'            => Str::slug($product->name),
                                    'prefix'         => SlugHelper::getPrefix(Product::class),
                                ];
                                $checkSlug = Slug::where(['key' => Str::slug($product->name), 'prefix' => SlugHelper::getPrefix(Product::class)])->first();
                                if ($checkSlug) {
                                    $slugParams['key'] .= '-' . time();
                                }
                                Slug::create($slugParams);

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
                                                filter_product_sku($prodId);
                                                $packAllProd = Product::where('id', $prodId)->first();

                                                //no barcode image need
                                                try {
                                                    //$barcodePackAll = get_barcode_by_upc($row['upc_pack']);
                                                    //$packAllProd->upc = $barcodePackAll['upc'];
                                                    //$packAllProd->barcode = $barcodePackAll['barcode'];
                                                    $packAllProd->upc = $row['upc_pack'];
                                                } catch (\ErrorException $exception) {
                                                }

                                                //change
                                                $packAllProd->private_label = $product->private_label;
                                                $packAllProd->restock = $product->restock;
                                                $packAllProd->new_label = $product->new_label;
                                                $packAllProd->usa_made = $product->usa_made;
                                                $packAllProd->ptype = $product->ptype;
                                                $packAllProd->prod_pieces = $product->prod_pieces;
                                                $packAllProd->sizes = $product->sizes;
                                                $packAllProd->images = $product->images;
                                                $packAllProd->save();

                                                $logParam = [
                                                    'parent_product_id' => $product->id,
                                                    'product_id'        => $prodId,
                                                    'sku'               => $packAllProd->sku,
                                                    'created_by'        => 1,
                                                    'reference'         => InventoryHistory::PROD_PUSH_ECOM
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
                                                        filter_product_sku($prodId);
                                                        $sizeProd = Product::where('id', $prodId)->first();

                                                        if ($sizeProd) {

                                                            //$barcodeSize = get_barcode();
                                                            //$sizeProd->upc = $barcodeSize['upc'];
                                                            //$sizeProd->barcode = $barcodeSize['barcode'];


                                                            //TODO:: get attribute slug from id($getSizeAttr) then match it with exploded form from hw table if match then get upc and save it to our db.
                                                            $getAttrSlug = ProductAttribute::where('id', $getSizeAttr)->value('slug');
                                                            if ($getAttrSlug) {
                                                                $getAttrSlug = str_replace(' ', '', $getAttrSlug);
                                                                $get_HW_UPCs = DB::table('hw_hw_upc_extra')->where('product_id', $row['product_id'])->get();
                                                                foreach ($get_HW_UPCs as $get_HW_UPC) {
                                                                    $explode = explode('|', $get_HW_UPC->description);
                                                                    if (isset($explode[1])) {
                                                                        $explode[1] = str_replace(' ', '', strtolower($explode[1]));
                                                                        if ($explode[1] == $getAttrSlug) {
                                                                            $sizeProd->upc = $get_HW_UPC->upc;
                                                                        }
                                                                    }
                                                                }
                                                            }


                                                            $sizeProd->private_label = $product->private_label;
                                                            $sizeProd->restock = $product->restock;
                                                            $sizeProd->new_label = $product->new_label;
                                                            $sizeProd->usa_made = $product->usa_made;
                                                            $sizeProd->ptype = $product->ptype;
                                                            $sizeProd->save();

                                                            $logParam = [
                                                                'parent_product_id' => $product->id,
                                                                'product_id'        => $prodId,
                                                                'sku'               => $sizeProd->sku,
                                                                'created_by'        => 1,
                                                                'reference'         => InventoryHistory::PROD_PUSH_ECOM
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


                            DB::connection('mysql2')->table('hw_products')->where('hw_products.fetch_status', 0)->where('product_id', $row['product_id'])->update(['hw_products.fetch_status' => 1]);
                        }
                        else{
                            DB::connection('mysql2')->table('hw_products')->where('hw_products.fetch_status', 0)->where('product_id', $row['product_id'])->update(['hw_products.fetch_status' => 1]);
                        }

                        //echo $check ? $check->sku : $row['product_code'].'\n';
                        echo isset($product) ? $row['product_id'] : $row['product_id'] . '====';
                    }
                }
            });
    }

}
