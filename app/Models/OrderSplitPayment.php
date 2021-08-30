<?php

namespace App\Models;

use Botble\Ecommerce\Models\Order;
use Illuminate\Database\Eloquent\Model;

class OrderSplitPayment extends Model
{
    protected $table = 'order_split_payments';
    protected $fillable = ['order_id', 'payment_type', 'amount'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
