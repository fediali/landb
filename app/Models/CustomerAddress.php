<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;
    protected $table = 'ec_customer_addresses';
    protected $fillable = ['customer_id', 'name', 'email', 'phone', 'country', 'city', 'state', 'address', 'is_default', 'zip_code', 'first_name', 'last_name', 'company', 'status', 'type'];
}
