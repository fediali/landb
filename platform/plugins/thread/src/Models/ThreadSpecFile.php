<?php

namespace Botble\Thread\Models;

use Botble\Base\Models\BaseModel;

class ThreadSpecFile extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'thread_spec_files';

    /**
     * @var array
     */
    protected $fillable = [
        'thread_id',
        'spec_file'
    ];

    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

}
