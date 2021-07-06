<?php

namespace Botble\Base\Supports;


use Botble\Base\Models\BaseModel;

class CustomDashboardMenu extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'custom_dashboard_menu';

    protected $fillable = [
        'menu_id',
        'priority',
        'parent_id',
        'name',
        'icon',
        'url',
        'permissions',
        'status',
    ];

    protected $visible = [
        'id',
        'priority',
        'parent_id',
        'name',
        'icon',
        'url',
        'children',
        'permissions',
        'active',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $with = ['children'];

    protected $appends = ['active'];

    public function getUrlAttribute($value)
    {
        $url =  $value != '/' ? url('/admin',$value) : url('/admin');
        return urldecode($url);
    }

    public function getPermissionsAttribute($value)
    {
        return json_decode($value);
    }

    public function getActiveAttribute()
    {
        return false;
    }

    public function getIdAttribute()
    {
        return $this->menu_id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(CustomDashboardMenu::class, 'parent_id', 'menu_id')->groupBy('menu_id')->orderBy('priority');
    }
}
