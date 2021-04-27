<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCard extends Model
{
    use HasFactory;

    protected $table = 'ec_customer_card';
    protected $fillable = ['customer_id', 'customer_data', 'customer_omni_id'];


}
