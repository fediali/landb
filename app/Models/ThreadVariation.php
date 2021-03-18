<?php

namespace App\Models;

use Botble\Printdesigns\Models\Printdesigns;
use Botble\Wash\Models\Wash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThreadVariation extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'thread_id',
        'name',
        'print_id',
        'regular_qty',
        'plus_qty',
        'cost',
        'notes',
        'created_by',
        'status',
        'sku',
        'plus_sku',
        'is_denim',
        'wash_id'
    ];
    public function printDesign(){
      return $this->belongsTo(Printdesigns::class, 'print_id');
    }

    public function fabrics(){
      return $this->hasMany(VariationFabric::class, 'thread_variation_id');
    }

    public function wash(){
      return $this->belongsTo(Wash::class, 'wash_id');
    }
}
