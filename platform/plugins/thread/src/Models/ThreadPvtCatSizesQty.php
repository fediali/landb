<?php

namespace Botble\Thread\Models;

use Botble\Base\Models\BaseModel;
use Botble\Categorysizes\Models\Categorysizes;
use Botble\Ecommerce\Models\ProductCategory;

class ThreadPvtCatSizesQty extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'thread_pvt_cat_sizes_qty';

    /**
     * @var array
     */
    protected $fillable = [
        'thread_id',
        'product_category_id',
        'category_size_id',
        'qty',
    ];

    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function category_size()
    {
        return $this->belongsTo(Categorysizes::class, 'category_size_id');
    }

}
