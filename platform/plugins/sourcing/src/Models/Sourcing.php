<?php

namespace Botble\Sourcing\Models;

use App\Models\User;
use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;

class Sourcing extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sourcings';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'file',
        'notes',
        'status',
        'parent_id',
        'user_id'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function childs(){
      return $this->hasMany(Sourcing::class, 'parent_id');
    }

    public function user(){
      return $this->belongsTo(User::class, 'user_id');
    }
}
