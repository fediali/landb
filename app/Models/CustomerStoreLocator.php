<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerStoreLocator extends Model
{
    use HasFactory;
    protected $table = 'ec_customer_store_locator';
    protected $fillable = [
        'customer_id',
        'locator_company',
        'locator_phone',
        'locator_website',
        'locator_address',
        'locator_city',
        'locator_country',
        'locator_state',
        'locator_zip_code',
        'locator_customer_type',
    ];
}
