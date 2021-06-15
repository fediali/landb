<?php

namespace App\Imports;

use App\Models\InventoryHistory;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Categorysizes\Models\Categorysizes;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductAttribute;
use Botble\Ecommerce\Models\ProductAttributeSet;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use SlugHelper;


class ImportProduct implements ToModel, WithHeadingRow
{
    protected $response;
    protected $productVariation;
    protected $productCategoryRepository;

    public function __construct($productVariation, $productCategoryRepository, $response)
    {
        $this->response = $response;
        $this->productVariation = $productVariation;
        $this->productCategoryRepository = $productCategoryRepository;
    }

    public function model(array $row)
    {
        if ($row['product_code'] && $row['category_id'] && $row['product'] && $row['category']) {

            $category = ProductCategory::where('name', $row['category'])->first();
            if (!$category && $row['category'] && $row['parent_id']) {
                $category = new ProductCategory();
                $category->name = $row['category'];
                $category->parent_id = $row['parent_id'];
                $category->save();
            }


            $check = Product::where('sku', $row['product_code'])->first();
            if (!$check) {
                $packQuantity = $this->quantityCalculate($category->id);
                $product = new Product();
                $product->name = $row['product'];
                if ($row['full_description']) {
                    $product->description = $row['full_description'];
                }
                if ($row['composition']) {
                    $product->content = $row['composition'];
                }
                if ($row['status']) {
                    $product->status = BaseStatusEnum::$STATUSES[$row['status']];
                }
                $product->sku = $row['product_code'];
                $product->category_id = $category->id;
                $product->quantity = 0;

                $percentage = !is_null(setting('sales_percentage')) ? setting('sales_percentage') : 0;

                $product->price = 0;
                if ($row['price']) {
                    $extras = ($row['price'] * $packQuantity) * $percentage / 100;
                    $packPrice = $row['price'] * $packQuantity + $extras;
                    $single = $row['price'] * $percentage / 100;
                    $singlePrice = $row['price'] + $single;
                    $product->price = $packPrice;
                }
                // $product->sale_price = $variation->cost + $extras;

                if ($row['image_path']) {
                    $product->images = json_encode([$row['image_path']]);
                }
                $product->tax_id = 1;

                if ($row['upc_pack']) {
                    $product->upc = $row['upc_pack'];
                    $product->barcode = get_barcode_by_upc($row['upc_pack'])['barcode'];
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

                                    $barcodePackAll = get_barcode_by_upc($row['upc_pack']);
                                    $packAllProd->upc = $barcodePackAll['upc'];
                                    $packAllProd->barcode = $barcodePackAll['barcode'];
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
        }
    }

    public function quantityCalculate($id)
    {
        $category = $this->productCategoryRepository->findOrFail($id);
        $totalQuantity = 0;
        foreach ($category->category_sizes as $cat) {
            $quan = substr($cat->name, strpos($cat->name, "-") + 1);
            $totalQuantity += $quan;
        }
        return $totalQuantity;
    }
}
