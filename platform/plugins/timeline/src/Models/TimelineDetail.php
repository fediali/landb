<?php

namespace Botble\Timeline\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class TimelineDetail extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'timelines_detail';

    /**
     * @var array
     */
    protected $fillable = [
        'timelines_detail',
        'product_image',
        'product_link',
        'product_desc',
    ];
    protected $with = [

    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function timeline()
    {
        return $this->belongsTo(Timeline::class);
    }

}
