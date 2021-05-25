<?php

namespace Botble\Ecommerce\Models;

use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProductShipmentVerify extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'order_products_shipment_verification';

    /**
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'is_verified',
        'qty',
        'created_by',
    ];

    /**
     * @return BelongsTo
     */
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
