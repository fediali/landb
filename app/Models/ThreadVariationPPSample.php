<?php

namespace App\Models;

use Botble\Printdesigns\Models\Printdesigns;
use Botble\Thread\Models\Thread;
use Botble\Wash\Models\Wash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ThreadVariationPPSample extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'thread_variation_id',
        'receive_date',
        'comments',
        'status',
    ];

    protected $table = 'thread_variation_pp_sample';

    public function thread_variation()
    {
        return $this->belongsTo(ThreadVariation::class, 'thread_variation_id');
    }


}
