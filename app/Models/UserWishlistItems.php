<?php

namespace App\Models;

use Botble\Ecommerce\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWishlistItems extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['wishlist_id', 'product_id', 'quantity', 'status'];

  public function product(){
    return $this->belongsTo(Product::class, 'product_id');
  }
}
