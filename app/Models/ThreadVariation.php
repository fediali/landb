<?php

namespace App\Models;

use Botble\Printdesigns\Models\Printdesigns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadVariation extends Model
{
    use HasFactory;
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
        'plus_sku'
    ];
    public function printDesign(){
      return $this->belongsTo(Printdesigns::class, 'print_id');
    }

    public function fabrics(){
      return $this->hasMany(VariationFabric::class, 'thread_variation_id');
    }
}
