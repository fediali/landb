<?php

namespace Botble\Ecommerce\Models;

use App\Models\CardPreAuth;
use Botble\ACL\Models\Role;
use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Botble\Payment\Models\Payment;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OrderHelper;
use App\Models\OrderImport;

class Order extends BaseModel
{
    use EnumCastable, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'ec_orders';

    /**
     * @var array
     */

    const LASHOWROOM = 1;
    const FASHIONGO = 2;
    const ORANGESHINE = 3;

    public static $MARKETPLACE = [
        self::LASHOWROOM  => 'LA SHOWROOM',
        self::FASHIONGO   => 'FASHIONGO',
        self::ORANGESHINE => 'ORANGE SHINE'
    ];

    const NORMAL = 'normal';
    const PRE_ORDER = 'pre_order';

    public static $ORDER_TYPES = [
        self::NORMAL    => 'Normal',
        self::PRE_ORDER => 'Pre Order',
    ];

    const ONLINE = 'online';
    const SALES = 'sales';
    const MOBILE = 'mobile';
    const IMPORT = 'import';

    public static $PLATFORMS = [
        self::ONLINE    => 'Online Order',
        self::SALES => 'Sales Rep\'s Order',
        self::MOBILE => 'Mobile Order',
        self::IMPORT => 'Imported Order',
    ];

    protected $fillable = [
        'status',
        'user_id',
        'amount',
        'currency_id',
        'tax_amount',
        'shipping_method',
        'shipping_option',
        'shipping_amount',
        'description',
        'coupon_code',
        'discount_amount',
        'sub_total',
        'is_confirmed',
        'discount_description',
        'is_finished',
        'token',
        'payment_id',
        'order_type',
        'editing_by',
        'editing_started_at',
        'order_card',
        'salesperson_id',
        'temp_sales_rep',
        'sales_commission_amount',
        'sales_commission_percent',
        'tracking_no',
        'platform',
        'parent_order',
        'notes',
        'po_number'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'status'          => OrderStatusEnum::class,
        'shipping_method' => ShippingMethodEnum::class,
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        self::deleting(function (Order $order) {
            app(ShipmentInterface::class)->deleteBy(['order_id' => $order->id]);
            Shipment::where('order_id', $order->id)->delete();
            OrderHistory::where('order_id', $order->id)->delete();
            OrderProduct::where('order_id', $order->id)->delete();
            OrderAddress::where('order_id', $order->id)->delete();
            app(PaymentInterface::class)->deleteBy(['order_id' => $order->id]);
        });

        static::addGlobalScope('userScope', function (Builder $query) {
            if (isset(auth()->user()->roles[0])) {
                if (in_array(auth()->user()->roles[0]->slug, [Role::ONLINE_SALES, Role::IN_PERSON_SALES])) {
                    //$query->join('ec_customers AS ecc', 'ecc.id', 'ec_orders.user_id');
                    $query->where('ec_orders.salesperson_id', auth()->user()->id);
                    //$query->orWhere('ec_customers.salesperson_id', auth()->user()->id);
                }
            }
        });
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Customer::class, 'user_id', 'id')->withDefault();
    }

    /**
     * @return mixed
     */
    public function getUserNameAttribute()
    {
        return $this->user->name;
    }

    /**
     * @return HasOne
     */
    public function address()
    {
        return $this->hasOne(OrderAddress::class, 'order_id')->withDefault();
    }

    public function shippingAddress()
    {
      return $this->hasOne(OrderAddress::class, 'order_id')->where('type', 'shipping');
    }

    public function billingAddress()
    {
      return $this->hasOne(OrderAddress::class, 'order_id')->where('type', 'billing');
    }

    /**
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id')->with(['product']);
    }

    /**
     * @return HasMany
     */
    public function shipment_verified_products()
    {
        return $this->hasMany(OrderProductShipmentVerify::class, 'order_id');
    }

    /**
     * @return HasMany
     */
    public function histories()
    {
        return $this->hasMany(OrderHistory::class, 'order_id')->with(['user', 'order']);
    }

    /**
     * @return array|null|string
     */
    public function getShippingMethodNameAttribute()
    {
        return OrderHelper::getShippingMethod(
            $this->attributes['shipping_method'],
            $this->attributes['shipping_option']
        );
    }

    /**
     * @return HasOne
     */
    public function shipment()
    {
        return $this->hasOne(Shipment::class)->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id')->withDefault();
    }

    /**
     * @return bool
     */
    public function canBeCanceled()
    {
        return in_array($this->status, [OrderStatusEnum::PENDING, OrderStatusEnum::PROCESSING]);
    }

    public function import()
    {
        return $this->hasOne(OrderImport::class, 'order_id');
    }

    public function getOrderTypeHtmlAttribute()
    {
        if ($this->order_type == self::PRE_ORDER) {
            return '<span class="label-warning status-label">Pre Order</span>';
        } else {
            return '<span class="label-primary status-label">Normal</span>';
        }
    }

    public function preauth()
    {
        return $this->hasOne(CardPreAuth::class, 'order_id');
    }

    public function salesperson()
    {
        return $this->belongsTo(User::class, 'salesperson_id');
    }

}
