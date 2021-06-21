<?php

namespace Botble\Chating\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;

class ChattingRecord extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'chatting_record';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'customer_id',
        'chat_count',
        'message_sid',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];
}
