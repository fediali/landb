<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserSearchItem extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_search_items';

    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'user_search_id',
        'key',
        'value',
    ];

    /**
     * @return HasMany
     */
    public function user_search()
    {
        return $this->hasMany(UserSearch::class, 'user_search_id');
    }
}
