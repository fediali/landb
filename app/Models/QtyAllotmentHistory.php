<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QtyAllotmentHistory extends Model
{
    use HasFactory;

    protected $table = 'qty_allotment_history';

    protected $fillable = [
        'product_id',
        'online_sales_qty',
        'in_person_sales_qty',
        'reference'
    ];

}
