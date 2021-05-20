<?php

namespace App\Models;

use Botble\Ecommerce\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerTaxCertificate extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'ec_customer_tax_certificate';
    protected $fillable = [
        'customer_id',
        'purchaser_name',
        'purchaser_phone',
        'purchaser_address',
        'purchaser_city',
        'permit_no',
        'registration_no',
        'items_description',
        'business_description',
        'title',
        'date',
        'purchaser_sign',
        'status'
    ];
    public function customer(){
      return $this->belongsTo(Customer::class, 'customer_id');
    }
}
