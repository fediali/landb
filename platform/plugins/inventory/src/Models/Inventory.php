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

    protected $appends = ['is_full_released'];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function products()
    {
        return $this->hasMany(InventoryProducts::class, 'inventory_id')->where('inventory_products.is_variation', 1 );
    }

    public function getIsFullReleasedAttribute()
    {
        foreach ($this->products as $product) {
            if (!$product->is_released) {
                return false;
            }
        }
        return true;
    }

}
