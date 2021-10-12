<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRefundProduct extends BaseModel
{

    /**
     * @var string
     */
    protected $table = 'ec_order_refund_product';

    /**
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'qty',
        'is_pack',
        'price',
        'tax_amount',
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
    protected $casts = [];

    protected $appends = [];

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
        return $this->belongsTo(Order::class)->withDefault();
    }

}
