<?php

namespace Botble\Ecommerce\Models;

use App\Models\CustomerAddress;
use App\Models\CustomerCard;
use App\Models\UserCart;
use App\Models\UserWishlist;
use Botble\Base\Supports\Avatar;
use Botble\Ecommerce\Notifications\CustomerResetPassword;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use RvMedia;

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
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'dob',
        'status',
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

    public function shippingAddress()
    {
        return $this->addresses()->where('type', 'shipping');
    }

    public function billingAddress()
    {

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

    public function card()
    {
        return $this->hasMany(CustomerCard::class, 'customer_id');
    }
}
