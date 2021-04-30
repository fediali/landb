<?php

namespace Botble\Orderstatuses\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;

class Orderstatuses extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'orderstatuses';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'qty_action',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public const INCREASE = 'increase';
    public const DECREASE = 'decrease';

    public static $QTY_ACTIONS = [
        self::INCREASE  => self::INCREASE,
        self::DECREASE  => self::DECREASE,
    ];

}
