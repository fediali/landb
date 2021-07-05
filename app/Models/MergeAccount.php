<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MergeAccount extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'ec_customers_merge';
    protected $fillable = ['user_id_one', 'user_id_two'];


}
