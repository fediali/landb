<?php

namespace App\Models;

use Botble\Ecommerce\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCartItem extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable =['cart_id', 'product_id', 'quantity', 'price', 'status', 'expires_at'];

    public function product(){
      return $this->belongsTo(Product::class, 'product_id');
    }
}
