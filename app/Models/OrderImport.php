<?php

namespace App\Models;

use Botble\Printdesigns\Models\Printdesigns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderImport extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ec_order_import';
    protected $fillable = ['order_id', 'po_number', 'order_date'];

}
