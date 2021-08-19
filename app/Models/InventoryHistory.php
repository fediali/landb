<?php

namespace App\Models;

use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Product;
use Botble\Inventory\Models\Inventory;
use Botble\Threadorders\Models\Threadorders;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;

    protected $table = 'inventory_history';

    protected $fillable = [
        'parent_product_id',
        'product_id',
        'sku',
        'quantity',
        'new_stock',
        'old_stock',
        'options',
        'created_by',
        'reference',
        'order_id',
        'inventory_id',
        'thread_order_id'
    ];

    const PROD_OSS = 'product_got_out_of_stock';
    const PROD_REORDER = 'product_reorder';
    const PROD_PUSH_ECOM = 'product_push_to_ecommerce';
    const PROD_STOCK_ADD = 'product_stock_added';
    const PROD_ORDER_QTY_ADD = 'product_order_qty_add';
    const PROD_ORDER_QTY_DEDUCT = 'product_order_qty_deduct';

    protected $with = ['user', 'order', 'thread_order', 'inventory'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function thread_order()
    {
        return $this->belongsTo(Threadorders::class, 'thread_order_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'parent_product_id');
    }

}
