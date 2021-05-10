<?php

namespace Botble\Vendorproducts\Models;

use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Vendorproductunits\Models\Vendorproductunits;

class Vendorproducts extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'vendorproducts';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'quantity',
        'product_unit_id',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function product_unit()
    {
        return $this->belongsTo(Vendorproductunits::class, 'product_unit_id', 'id');
    }

}
