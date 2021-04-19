<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCart extends Model
{

    use HasFactory;
    protected $fillable = ['user_id', 'status'];

    public function cartItems(){
      return $this->hasMany(UserCartItem::class, 'cart_id');
    }
}
