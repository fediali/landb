<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryProducts extends Model
{
   protected $table = 'inventory_products_pivot';

   protected $fillable = ['sku', 'barcode', 'inventory_id', 'quantity'];
}
