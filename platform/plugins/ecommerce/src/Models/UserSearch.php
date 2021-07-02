<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserSearch extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_searches';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'search_type',
        'name',
        'status',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @return HasMany
     */
    public function user_search_items()
    {
        return $this->hasMany(UserSearchItem::class, 'user_search_id');
    }
}
