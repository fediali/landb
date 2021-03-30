<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;
use Botble\Categorysizes\Models\Categorysizes;
use Botble\Vendorproductunits\Models\Vendorproductunits;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ec_product_categories';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'parent_id',
        'description',
        'order',
        'status',
        'image',
        'is_featured',
        'is_plus_cat',
        'impact_price',
        'per_piece_qty',
        'product_unit_id',
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

    /**
     * @return BelongsToMany
     */
    public function category_sizes(): BelongsToMany
    {
        return $this->belongsToMany(Categorysizes::class, 'product_categories_sizes', 'product_category_id', 'category_size_id');
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this
            ->belongsToMany(Product::class, 'ec_product_category_product', 'category_id', 'product_id')
            ->where('is_variation', 0);
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id')->withDefault();
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    protected static function boot()
    {
        parent::boot();
        self::deleting(function (ProductCategory $productCategory) {
            $productCategory->products()->detach();
        });
    }

    public function getIsPlusCatHtmlAttribute()
    {
        if ($this->is_plus_cat) {
            return '<span class="label-success status-label">Yes</span>';
        }
        return '<span class="label-warning status-label">No</span>';
    }

}
