<?php

namespace Botble\Textmessages\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;

class Textmessages extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'textmessages';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'status',
        'text',
        'schedule_date',
        'created_by',
        'updated_by',
        'customer_ids',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];
}
