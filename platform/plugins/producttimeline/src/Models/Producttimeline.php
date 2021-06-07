<?php

namespace Botble\Producttimeline\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;

class Producttimeline extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'producttimelines';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'status',
        'date',
        'schedule_date',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];
}
