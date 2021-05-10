<?php

namespace Botble\ACL\Models;

use Botble\Base\Models\BaseModel;

class UserOtherEmail extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_other_email_addresses';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'email',
    ];

    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
