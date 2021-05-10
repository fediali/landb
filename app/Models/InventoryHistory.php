<?php

namespace App\Models;

use Botble\Inventory\Models\Inventory;
use Botble\Threadorders\Models\Threadorders;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;
    protected $table = 'inventory_history';
    protected $fillable =[
        'product_id',
        'quantity',
        'new_stock',
        'old_stock',
        'options',
        'created_by',
        'reference',
        'order_id',
        'inventory_id'
    ];
    protected $with=['user', 'order', 'inventory'];

    public function user(){
      return $this->belongsTo(User::class, 'created_by');
    }

    public function order(){
      return $this->belongsTo(Threadorders::class, 'order_id');
    }

    public function inventory(){
      return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
