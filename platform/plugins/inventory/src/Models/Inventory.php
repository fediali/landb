<?php

namespace Botble\Inventory\Models;

use App\Models\InventoryProducts;
use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;

class Inventory extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'inventories';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'status',
        'date',
        'description',
        'release_date',
        'comments',
        'created_by',
        'updated_by',
        'administrated_by',
        'released_by'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function products(){
      return $this->hasMany(InventoryProducts::class, 'inventory_id');
    }
}