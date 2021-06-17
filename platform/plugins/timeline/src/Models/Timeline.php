<?php

namespace Botble\Timeline\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class Timeline extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'timelines';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'date',
        'status',
    ];
    protected $with = [
        'detail'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function detail()
    {
        return $this->hasMany(TimelineDetail::class, 'product_timeline_id');
    }
}
