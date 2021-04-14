<?php

namespace App\Models;

use Botble\Printdesigns\Models\Printdesigns;
use Botble\Wash\Models\Wash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ThreadVariationTrim extends Model
{
    use HasFactory;
    //use SoftDeletes;
    protected $fillable = [
        'thread_variation_id',
        'trim_image',
        'trim_note',
    ];

}
