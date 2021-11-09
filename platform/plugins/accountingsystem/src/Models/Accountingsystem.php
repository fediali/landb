<?php

namespace Botble\Accountingsystem\Models;

use Botble\ACL\Models\User;
use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accountingsystem extends BaseModel
{
    use EnumCastable;
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'accountingsystems';

    /**
     * @var array
     */
    protected $fillable = [
        'money',
        'description',
        'amount',
        'business_id',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
