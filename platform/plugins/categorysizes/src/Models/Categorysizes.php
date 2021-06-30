<?php

namespace Botble\Categorysizes\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class Categorysizes extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'categorysizes';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $appends = ['full_name'];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function getFullNameAttribute()
    {
        return DB::table('categorysizes')->where('id', $this->id)->value('name');
    }

    public function getNameAttribute($value)
    {
        return strtok($value,'-');
    }

}
