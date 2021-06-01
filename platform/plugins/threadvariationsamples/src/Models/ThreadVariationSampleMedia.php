<?php

namespace Botble\Threadvariationsamples\Models;

use Botble\Base\Models\BaseModel;

class ThreadVariationSampleMedia extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'thread_variation_sample_media';

    /**
     * @var array
     */
    protected $fillable = [
        'thread_variation_sample_id',
        'media',
    ];

    public function thread_variation_sample()
    {
        return $this->belongsTo(Threadvariationsamples::class, 'thread_variation_sample_id');
    }

}
