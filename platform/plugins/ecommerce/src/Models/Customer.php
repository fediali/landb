<?php

namespace Botble\Ecommerce\Models;

use App\Models\CustomerAddress;
use App\Models\CustomerCard;
use App\Models\CustomerStoreLocator;
use App\Models\CustomerTaxCertificate;
use App\Models\MergeAccount;
use App\Models\UserCart;
use App\Models\UserWishlist;
use Botble\ACL\Models\Role;
use Botble\ACL\Models\User;
use Botble\Base\Supports\Avatar;
use Botble\Chating\Models\ChattingRecord;
use Botble\Ecommerce\Notifications\CustomerResetPassword;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use RvMedia;
use OrderHelper;

/**
 * @mixin \Eloquent
 */
class Customer extends Authenticatable
{
    use Notifiable;

    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'ec_customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    const VERIFIED = 1;
    const VERIFY = 0;
    const UNVERIFIED = 2;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'dob',
        'status',
        'login_status',
        'is_private',
        'is_text',
        'salesperson_id',
        'phone_validation_error',
        'last_visit',
        'document'
    ];
    protected $with = [
        'detail',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static $customerType = ['Western', 'Boho', 'Contemporary', 'Conservative', 'Other'];
    public static $hearUs = [
        '1' => 'Google',
        '2' => 'Social',
        '3' => 'LAShowroom',
        '4' => 'Fashiongo',
        '16' => 'Dallas Market',
        '19' => 'Atlanta Market',
        '11' => 'Other'
    ];
    public static $preferredCommunication = [
        '12' => 'Email',
        '13' => 'Phone',
        '14' => 'Email & Phone'
    ];

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomerResetPassword($token));
    }

    /**
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? RvMedia::getImageUrl($this->avatar, 'thumb') : (string)(new Avatar)->create($this->name)->toBase64();
    }

    /**
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function addresses()
    {
        return $this->hasMany(Address::class, 'customer_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'ec_discount_customers', 'customer_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    /* public function wishlist(): HasMany
     {
         return $this->hasMany(Wishlist::class, 'customer_id');
     }*/

    protected static function boot()
    {
        parent::boot();

        self::deleting(function (Customer $customer) {
            $customer->discounts()->detach();
            Review::where('customer_id', $customer->id)->delete();
            Wishlist::where('customer_id', $customer->id)->delete();
        });

        static::addGlobalScope('userScope', function (Builder $query) {
            if (isset(auth()->user()->roles[0])) {
                if (in_array(auth()->user()->roles[0]->slug, [Role::IN_PERSON_SALES])) {
                    $query->where('salesperson_id', auth()->user()->id);
                }
            }
        });
    }

    public function detail()
    {
        return $this->hasOne(CustomerDetail::class, 'customer_id');
    }

    public function cart()
    {
        return $this->hasOne(UserCart::class, 'user_id');
    }

    public function UserCartId()
    {
        $cart = $this->cart();
        if ($cart) {
            return $this->cart()->pluck('id')->first();
        } else {
            return null;
        }

    }

    public function details()
    {
        return $this->hasOne(CustomerDetail::class, 'customer_id');
    }

    public function taxCertificate()
    {
        return $this->hasOne(CustomerTaxCertificate::class, 'customer_id');
    }

    public function storeLocator()
    {
        return $this->hasOne(CustomerStoreLocator::class, 'customer_id');
    }

    public function shippingAddress()
    {
        $default = $this->addresses()->where('type', 'shipping')->where('is_default', 1)->get();
        if (count($default)) {
            return $this->addresses()->where('type', 'shipping')->where('is_default', 1);
        }
        return $this->addresses()->where('type', 'shipping');
    }

    public function billingAddress()
    {
        $default = $this->addresses()->where('type', 'billing')->where('is_default', 1)->get();
        if (count($default)) {
            return $this->addresses()->where('type', 'billing')->where('is_default', 1);
        }
        return $this->addresses()->where('type', 'billing');
    }

    public function wishlist()
    {
        return $this->hasOne(UserWishlist::class, 'user_id');
    }

    public function UserWishlistId()
    {
        return $this->wishlist()->pluck('id')->first();
    }

    public function pendingOrder()
    {
        return $this->orders()->where('user_id', $this->id)->where('is_finished', 0)->first();
    }

    public function pendingOrderId()
    {
        $cart = $this->pendingOrder();
        if ($cart) {
            return $cart->id;
        } else {
            return null;
        }

    }

    public function pendingPreOrder()
    {
        return $this->orders()->where('user_id', $this->id)->where('is_finished', 0)->first();
    }

    public function pendingPreOrderId()
    {
        $cart = $this->pendingPreOrder();
        if ($cart) {
            return $cart->id;
        } else {
            return null;
        }

    }

    public function card()
    {
        return $this->hasMany(CustomerCard::class, 'customer_id');
    }

    public function salesperson()
    {
        return $this->belongsTo(User::class, 'salesperson_id');
    }

    public function chat()
    {
        return $this->hasMany(ChattingRecord::class, 'customer_id');
    }

    public function merge()
    {
        return $this->hasMany(MergeAccount::class, 'user_id_one');
    }

    public function spend()
    {
        $spend = $this->orders()->where('user_id', $this->id)->sum('amount');
        return $spend;
    }

    public function abandonedProducts()
    {
        $abandoned = $this->pendingOrder();
        $abandoned  = !is_null($abandoned) ? $abandoned->products()->sum('qty') : 0;
        return [
            'abandoned' => $abandoned,
            'order_id' => $this->pendingOrderId()
        ];
    }

    public function latestOrder(){
      $date =  $this->orders()->where('is_finished' , 1)->latest()->pluck('created_at')->first();
      $date = !is_null($date) ? date('m/d/y', strtotime($date)) : '-';
      return $date;
    }

    public function getUserCart()
    {
      $check = $this->pendingOrder();
      $token = OrderHelper::getOrderSessionToken();

      if (!$check) {
        $cart = Order::create([
            'user_id'         => auth('customer')->user()->id,
            'salesperson_id'  => auth('customer')->user()->salesperson_id,
            'amount'          => 0,
            'sub_total'       => 0,
            'is_finished'     => 0,
            'token'           => $token,
            'tax_amount'      => 0,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'currency_id'     => 1,
        ]);
        return $cart->id;
      } else {
        return $this->pendingOrderId();
      }
    }

    /**
     * @return HasMany
     */
    public function histories()
    {
        return $this->hasMany(CustomerHistory::class, 'customer_id')->with(['user', 'customer']);
    }
}
