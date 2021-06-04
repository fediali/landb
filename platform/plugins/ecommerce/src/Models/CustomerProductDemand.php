<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;

class CustomerProductDemand extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'customer_product_demand';

    /**
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'product_id',
        'variation_id',
        'demand_qty',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
