<?php

namespace Botble\Threadorders\Models;

use Botble\ACL\Models\User;
use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Fabrics\Models\Fabrics;
use Botble\Fits\Models\Fits;
use Botble\Rises\Models\Rises;
use Botble\Seasons\Models\Seasons;
use Botble\Thread\Models\Thread;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Threadorders extends BaseModel
{
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
        'pp_sample_date',
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
        'pp_sample',
        'pp_sample_size',
        'pp_sample_date',
        'material',
        'sleeve',
        'label',
        'shipping_method',
        'order_date',
        'ship_date',
        'cancel_date',
        'elastic_waste_pant',
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
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    protected $with = [];

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

    /**
     * @deprecated
     * @return BelongsTo
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class, 'thread_id')->withDefault();
    }

    /**
     * @deprecated
     * @return BelongsTo
     */
    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'designer_id')->withDefault();
    }

    /**
     * @deprecated
     * @return BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id')->withDefault();
    }

    /**
     * @deprecated
     * @return BelongsTo
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Seasons::class)->withDefault();
    }

    /**
     * @deprecated
     * @return BelongsTo
     */
    public function fit(): BelongsTo
    {
        return $this->belongsTo(Fits::class)->withDefault();
    }

    /**
     * @deprecated
     * @return BelongsTo
     */
    public function rise(): BelongsTo
    {
        return $this->belongsTo(Rises::class)->withDefault();
    }

    /**
     * @deprecated
     * @return BelongsTo
     */
    public function fabric(): BelongsTo
    {
        return $this->belongsTo(Fabrics::class, 'fabric_id');
    }
}
