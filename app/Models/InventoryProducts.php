<?php

namespace App\Models;

use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Inventory\Models\Inventory;
use Illuminate\Database\Eloquent\Model;

class InventoryProducts extends Model
{
    protected $fillable = ['inventory_id', 'product_id', 'sku', 'upc', 'barcode', 'is_variation', 'ecom_qty', 'ordered_qty', 'received_qty'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class,'inventory_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class,'product_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function product_categories()
    {
        return $this->belongsToMany(ProductCategory::class,'inventory_product_cat_qty', 'inventory_product_id', 'product_category_id')->withPivot('loose_qty');
    }

}
