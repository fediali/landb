<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWishlist extends Model
{
  protected $table = 'user_wishlist';
    use HasFactory;
    protected $fillable = ['user_id', 'status'];

    public function wishlistItems(){
      return $this->hasMany(UserWishlistItems::class, 'wishlist_id');
    }
}
