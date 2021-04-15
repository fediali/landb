<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotifications extends Model
{
    use HasFactory;
    protected $fillable = ['notification_id', 'user_id'];
    protected $with = ['notification'];
    public function notification(){
      return $this->belongsTo(Notification::class, 'notification_id');
    }
}
