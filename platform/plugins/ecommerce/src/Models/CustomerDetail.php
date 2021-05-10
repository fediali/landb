<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Supports\Avatar;
use Botble\Ecommerce\Notifications\CustomerResetPassword;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use RvMedia;

/**
 * @mixin \Eloquent
 */
class CustomerDetail extends Authenticatable
{
    use Notifiable;

    /**
     * @var string
     */
    protected $table = 'ec_customer_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'sales_tax_id',
        'first_name',
        'last_name',
        'business_phone',
        'company',
        'customer_type',
        'store_facebook',
        'store_instagram',
        'mortar_address',
        'newsletter',
        'hear_us',
        'comments',
        'type',
        'phone',
        'preferred_communication',
        'events_attended',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */


}
