<?php

namespace App\Models;

use Botble\Printdesigns\Models\Printdesigns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariationFabric extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'thread_variation_id', 'print_id', 'created_by'];

    protected $with=['printdesign'];

    public function printdesign(){
      return $this->belongsTo(Printdesigns::class, 'print_id');
    }
}
