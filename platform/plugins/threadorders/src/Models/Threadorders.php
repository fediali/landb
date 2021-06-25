<?php

namespace Botble\Threadorders\Models;

use App\Models\InventoryHistory;
use Botble\ACL\Models\User;
use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Fabrics\Models\Fabrics;
use Botble\Fits\Models\Fits;
use Botble\Rises\Models\Rises;
use Botble\Seasons\Models\Seasons;
use Botble\Thread\Models\Thread;
use Botble\Vendorproducts\Models\Vendorproducts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Threadorders extends BaseModel
{
    use SoftDeletes;
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'threadorders';

    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = [
        //'pp_sample_date',
        'order_date',
        'ship_date',
        'cancel_date',
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'thread_id',
        'designer_id',
        'vendor_id',
        'season_id',
        'order_no',
        'order_status',
        'thread_status',
        //'pp_sample',
        //'pp_sample_size',
        //'pp_sample_date',
        'material',
        'sleeve',
        'label',
        'shipping_method',
        'order_date',
        'ship_date',
        'cancel_date',
        'elastic_waste_pant',
        'vendor_product_id',
        'is_denim',
        'inseam',
        'fit_id',
        'rise_id',
        'fabric_id',
        'fabric_print_direction',
        'reg_pack_qty',
        'plus_pack_qty',
        //'spec_file',
        'business_id',
        'created_by',
        'updated_by',
        'status',
        'created_at',
        'updated_at',
        'is_pieces',
        'pvt_customer_id',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    protected $with = [];

    protected $appends = ['thread_order_has_pushed'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('userScope', function (Builder $query) {
            if (isset(auth()->user()->roles[0])) {
                if (auth()->user()->roles[0]->slug == 'vendor') {
                    $query->where('vendor_id', auth()->user()->id);
                }
            }
        });
    }

    public function vendor_product()
    {
        return $this->belongsTo(Vendorproducts::class, 'vendor_product_id', 'id');
    }

    /**
     * @return BelongsTo
     * @deprecated
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'thread_id')->withDefault();
    }

    /**
     * @return BelongsTo
     * @deprecated
     */
    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'designer_id')->withDefault();
    }

    /**
     * @return BelongsTo
     * @deprecated
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id')->withDefault();
    }

    /**
     * @return BelongsTo
     * @deprecated
     */
    public function pvt_customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'pvt_customer_id')->withDefault();
    }

    /**
     * @return BelongsTo
     * @deprecated
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Seasons::class)->withDefault();
    }

    /**
     * @return BelongsTo
     * @deprecated
     */
    public function fit(): BelongsTo
    {
        return $this->belongsTo(Fits::class)->withDefault();
    }

    /**
     * @return BelongsTo
     * @deprecated
     */
    public function rise(): BelongsTo
    {
        return $this->belongsTo(Rises::class)->withDefault();
    }

    /**
     * @return BelongsTo
     * @deprecated
     */
    public function fabric(): BelongsTo
    {
        return $this->belongsTo(Fabrics::class, 'fabric_id');
    }

    public function threadOrderVariations($type = false)
    {
        return DB::table('thread_order_variations')
            ->select('thread_order_variations.*', 'ec_product_categories.name AS cat_name', 'vendorproductunits.name AS unit_name', 'printdesigns.file AS design_file')
            ->join('ec_product_categories', 'ec_product_categories.id', 'thread_order_variations.product_category_id')
            ->leftJoin('vendorproductunits', 'vendorproductunits.id', 'thread_order_variations.product_unit_id')
            ->leftJoin('printdesigns', 'printdesigns.id', 'thread_order_variations.print_design_id')
            ->where('thread_order_id', $this->id)
            ->when($type, function ($q) use ($type) {
                $q->where('category_type', $type);
            })
            ->orderBy('category_type')
            ->get();
    }

    public function getThreadOrderHasPushedAttribute()
    {
        $check = InventoryHistory::join('ec_products', 'ec_products.id', 'inventory_history.parent_product_id')
            ->where('thread_order_id', $this->id)
            ->where('ec_products.status', BaseStatusEnum::PUBLISHED)
            /*->where('reference', '!=', InventoryHistory::PROD_REORDER)*/
            ->value('inventory_history.id');
        if ($check) {
            return true;
        }
        return false;
    }

}
