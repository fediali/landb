<?php

namespace Botble\Thread\Models;

use App\Models\ThreadVariation;
use Botble\ACL\Models\User;
use Botble\Base\Traits\EnumCastable;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Blog\Models\Category;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Fabrics\Models\Fabrics;
use Botble\Fits\Models\Fits;
use Botble\Printdesigns\Models\Printdesigns;
use Botble\Rises\Models\Rises;
use Botble\Seasons\Models\Seasons;
use Botble\Threadorders\Models\Threadorders;
use Botble\Vendorproducts\Models\Vendorproducts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Thread extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'threads';

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
        'designer_id',
        'vendor_id',
        'season_id',
        //'order_no',
        'order_status',
        'thread_status',
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
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    protected $with = [];

    protected $appends = ['thread_has_order'];

    public const NEW = 'new';
    public const REORDER = 'reorder';
    public const CANCEL = 'cancel';

    public static $order_statuses = [
        self::NEW     => self::NEW,
        self::REORDER => self::REORDER,
        self::CANCEL  => self::CANCEL,
    ];

    public const SPECIAL = 'special';
    public const PRIVATE = 'private';

    public static $thread_statuses = [
        self::NEW     => self::NEW,
        self::REORDER => self::REORDER,
        self::SPECIAL => self::SPECIAL,
        self::PRIVATE => self::PRIVATE,
    ];

    public const YES = 'yes';
    public const NO = 'no';

    public static $statuses = [
        self::YES => self::YES,
        self::NO  => self::NO,
    ];

    public const AIR = 'air';
    public const UCL = 'ucl';
    public const SEA = 'sea';

    public static $shipping_methods = [
        self::AIR => self::AIR,
        self::UCL => self::UCL,
        self::SEA => self::SEA,
    ];

    public const REGULAR = 'regular';
    public const PLUS = 'plus';

    public static $category_types = [
        self::REGULAR => self::REGULAR,
        self::PLUS    => self::PLUS,
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('userScope', function (Builder $query) {
            if (isset(auth()->user()->roles[0])) {
                if (auth()->user()->roles[0]->slug == 'designer') {
                    $query->where('designer_id', auth()->user()->id);
                } else if (auth()->user()->roles[0]->slug == 'vendor') {
                    $query->where(['vendor_id' => auth()->user()->id, 'status' => 'published']);
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
    public function season(): BelongsTo
    {
        return $this->belongsTo(Seasons::class)->withDefault();
    }

    /**
     * @return BelongsToMany
     */
    public function regular_product_categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'categories_threads', 'thread_id', 'product_category_id')
            ->where('category_type', self::REGULAR)
            ->withPivot('sku', 'category_type', 'product_unit_id', 'per_piece_qty');
    }

    /**
     * @return BelongsToMany
     */
    public function plus_product_categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'categories_threads', 'thread_id', 'product_category_id')
            ->where('category_type', self::PLUS)
            ->withPivot('sku', 'category_type', 'product_unit_id', 'per_piece_qty');
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

    public function spec_files()
    {
        return $this->hasMany(ThreadSpecFile::class, 'thread_id');
    }

    public function getThreadVariationsAttribute()
    {
        return ThreadVariation::where('thread_id', $this->id)->where('status', 'active')->get();
    }

    public function getThreadHasOrderAttribute()
    {
        $check = Threadorders::where('thread_id', $this->id)->where('order_status', self::NEW)->value('id');
        if ($check) {
            return true;
        }
        return false;
    }

}
