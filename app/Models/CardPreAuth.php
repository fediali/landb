<?php

namespace App\Models;

use Botble\Printdesigns\Models\Printdesigns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CardPreAuth extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ec_order_preauth';
    protected $fillable = ['order_id', 'transaction_id', 'response', 'status'];

}
