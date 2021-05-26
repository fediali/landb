<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends BaseModel
{

    /**
     * @var string
     */
    protected $table = 'ec_order_product';

    /**
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'qty',
        'weight',
        'price',
        'tax_amount',
        'options',
        'restock_quantity',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'options' => 'json',
    ];

    protected $appends = ['shipment_verified', 'shipment_verified_qty'];

    /**
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    public function getShipmentVerifiedAttribute()
    {
        $check = OrderProductShipmentVerify::where(['order_id' => $this->order_id, 'product_id' => $this->product_id])->value('is_verified');
        if ($check) {
            return true;
        }
        return false;
    }

    public function getShipmentVerifiedQtyAttribute()
    {
        return OrderProductShipmentVerify::where(['order_id' => $this->order_id, 'product_id' => $this->product_id])->value('qty');
    }

}
