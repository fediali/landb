<?php

namespace Botble\Threadvariationsamples\Models;

use App\Models\ThreadVariation;
use Botble\ACL\Models\User;
use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Thread\Models\Thread;

class Threadvariationsamples extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'threadvariationsamples';

    /**
     * @var array
     */
    protected $fillable = [
        'photographer_id',
        'thread_id',
        'thread_variation_id',
        'assign_date',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function photographer()
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function thread_variation()
    {
        return $this->belongsTo(ThreadVariation::class, 'thread_variation_id');
    }

    public function sample_media()
    {
        return $this->hasMany(ThreadVariationSampleMedia::class, 'thread_variation_sample_id');
    }
}
