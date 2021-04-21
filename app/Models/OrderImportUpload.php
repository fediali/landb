<?php

namespace App\Models;

use Botble\Printdesigns\Models\Printdesigns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderImportUpload extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ec_order_import_upload';
    protected $fillable = ['file'];

}
