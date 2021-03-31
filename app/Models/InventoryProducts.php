<?php

namespace App\Models;

use Botble\Ecommerce\Models\Product;
use Botble\Inventory\Models\Inventory;
use Illuminate\Database\Eloquent\Model;

class InventoryProducts extends Model
{
    protected $table = 'inventory_products_pivot';

    protected $fillable = ['inventory_id', 'product_id', 'sku', 'barcode', 'ecom_pack_qty', 'ordered_pack_qty', 'received_pack_qty', 'loose_qty'];

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

}
