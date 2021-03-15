<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThreadComment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['thread_id', 'user_id', 'comment', 'image'];

    public function user(){
      return $this->belongsTo(User::class, 'user_id');
    }
}
