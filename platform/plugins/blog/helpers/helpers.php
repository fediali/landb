<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Supports\SortItemsWithChildrenHelper;
use Botble\Blog\Repositories\Interfaces\CategoryInterface;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Blog\Repositories\Interfaces\TagInterface;
use Botble\Blog\Supports\PostFormat;
use Botble\Ecommerce\Models\ProductCategory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;

if (!function_exists('get_featured_posts')) {
    /**
     * @param int $limit
     * @param array $with
     * @return \Illuminate\Support\Collection
     */
    function get_featured_posts($limit, array $with = [])
    {
        return app(PostInterface::class)->getFeatured($limit, $with);
    }
}

if (!function_exists('get_latest_posts')) {
    /**
     * @param int $limit
     * @param array $excepts
     * @return \Illuminate\Support\Collection
     */
    function get_latest_posts($limit, $excepts = [], array $with = [])
    {
        return app(PostInterface::class)->getListPostNonInList($excepts, $limit, $with);
    }
}

if (!function_exists('get_related_posts')) {
    /**
     * @param int $id
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    function get_related_posts($id, $limit)
    {
        return app(PostInterface::class)->getRelated($id, $limit);
    }
}

if (!function_exists('get_posts_by_category')) {
    /**
     * @param int $categoryId
     * @param int $paginate
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    function get_posts_by_category($categoryId, $paginate = 12, $limit = 0)
    {
        return app(PostInterface::class)->getByCategory($categoryId, $paginate, $limit);
    }
}

if (!function_exists('get_posts_by_tag')) {
    /**
     * @param string $slug
     * @param int $paginate
     * @return \Illuminate\Support\Collection
     */
    function get_posts_by_tag($slug, $paginate = 12)
    {
        return app(PostInterface::class)->getByTag($slug, $paginate);
    }
}

if (!function_exists('get_posts_by_user')) {
    /**
     * @param int $authorId
     * @param int $paginate
     * @return \Illuminate\Support\Collection
     */
    function get_posts_by_user($authorId, $paginate = 12)
    {
        return app(PostInterface::class)->getByUserId($authorId, $paginate);
    }
}

if (!function_exists('get_all_posts')) {
    /**
     * @param boolean $active
     * @param int $perPage
     * @return \Illuminate\Support\Collection
     */
    function get_all_posts($active = true, $perPage = 12)
    {
        return app(PostInterface::class)->getAllPosts($perPage, $active);
    }
}

if (!function_exists('get_recent_posts')) {
    /**
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    function get_recent_posts($limit)
    {
        return app(PostInterface::class)->getRecentPosts($limit);
    }
}

if (!function_exists('get_featured_categories')) {
    /**
     * @param int $limit
     * @param array $with
     * @return \Illuminate\Support\Collection
     */
    function get_featured_categories($limit, array $with = [])
    {
        return app(CategoryInterface::class)->getFeaturedCategories($limit, $with);
    }
}

if (!function_exists('get_all_categories')) {
    /**
     * @param array $condition
     * @param array $with
     * @return \Illuminate\Support\Collection
     */
    function get_all_categories(array $condition = [], $with = [])
    {
        return app(CategoryInterface::class)->getAllCategories($condition, $with);
    }
}

if (!function_exists('get_all_tags')) {
    /**
     * @param boolean $active
     * @return \Illuminate\Support\Collection
     */
    function get_all_tags($active = true)
    {
        return app(TagInterface::class)->getAllTags($active);
    }
}

if (!function_exists('get_popular_tags')) {
    /**
     * @param integer $limit
     * @return \Illuminate\Support\Collection
     */
    function get_popular_tags($limit = 10)
    {
        return app(TagInterface::class)->getPopularTags($limit);
    }
}

if (!function_exists('get_popular_posts')) {
    /**
     * @param integer $limit
     * @param array $args
     * @return \Illuminate\Support\Collection
     */
    function get_popular_posts($limit = 10, array $args = [])
    {
        return app(PostInterface::class)->getPopularPosts($limit, $args);
    }
}

if (!function_exists('get_popular_categories')) {
    /**
     * @param integer $limit
     * @return \Illuminate\Support\Collection
     */
    function get_popular_categories($limit = 10)
    {
        return app(CategoryInterface::class)->getPopularCategories($limit);
    }
}

if (!function_exists('get_category_by_id')) {
    /**
     * @param integer $id
     * @return \Botble\Base\Models\BaseModel
     */
    function get_category_by_id($id)
    {
        return app(CategoryInterface::class)->getCategoryById($id);
    }
}

if (!function_exists('get_categories')) {
    /**
     * @param array $args
     * @return \Illuminate\Support\Collection|mixed
     */
    function get_categories(array $args = [])
    {
        $indent = Arr::get($args, 'indent', '——');

        $repo = app(CategoryInterface::class);

        $categories = $repo->getCategories(Arr::get($args, 'select', ['*']), [
            'categories.created_at' => 'DESC',
            'categories.is_default' => 'DESC',
            'categories.order'      => 'ASC',
        ]);

        $categories = sort_item_with_children($categories);

        foreach ($categories as $category) {
            $indentText = '';
            $depth = (int)$category->depth;
            for ($index = 0; $index < $depth; $index++) {
                $indentText .= $indent;
            }
            $category->indent_text = $indentText;
        }

        return $categories;
    }
}

if (!function_exists('get_categories_with_children')) {
    /**
     * @return \Illuminate\Support\Collection
     * @throws Exception
     */
    function get_categories_with_children()
    {
        $categories = app(CategoryInterface::class)
            ->getAllCategoriesWithChildren(['status' => BaseStatusEnum::PUBLISHED], [], ['id', 'name', 'parent_id']);
        $sortHelper = app(SortItemsWithChildrenHelper::class);
        $sortHelper
            ->setChildrenProperty('child_cats')
            ->setItems($categories);

        return $sortHelper->sort();
    }
}

if (!function_exists('register_post_format')) {
    /**
     * @param array $formats
     * @return void
     */
    function register_post_format(array $formats)
    {
        PostFormat::registerPostFormat($formats);
    }
}

if (!function_exists('get_post_formats')) {
    /**
     * @param bool $convertToList
     * @return array
     */
    function get_post_formats($convertToList = false)
    {
        return PostFormat::getPostFormats($convertToList);
    }
}


//Get User By Roles
if (!function_exists('get_designers')) {
    function get_designers()
    {
        return \App\Models\User::join('role_users', 'users.id', 'role_users.user_id')
            ->join('roles', 'role_users.role_id', 'roles.id')
            ->where('roles.slug', 'designer')
            ->pluck('users.username', 'users.id')->all();
    }
}

if (!function_exists('get_vendors')) {
    function get_vendors()
    {
        return \App\Models\User::join('role_users', 'users.id', 'role_users.user_id')
            ->join('roles', 'role_users.role_id', 'roles.id')
            ->where('roles.slug', 'vendor')
            ->pluck('users.username', 'users.id')->all();
    }
}
//Get User By Roles


//Get General Data
if (!function_exists('get_print_designs')) {
    function get_print_designs()
    {
        return \Botble\Printdesigns\Models\Printdesigns::all();
    }
}

if (!function_exists('get_seasons')) {
    function get_seasons()
    {
        return \Botble\Seasons\Models\Seasons::where('status', 'published')->pluck('name', 'id')->all();
    }
}

if (!function_exists('get_designs')) {
    function get_designs()
    {
        return \Botble\Printdesigns\Models\Printdesigns::where('status', 'published')->pluck('name', 'id')->all();
    }
}

if (!function_exists('get_fits')) {
    function get_fits()
    {
        return \Botble\Fits\Models\Fits::where('status', 'published')->pluck('name', 'id')->all();
    }
}

if (!function_exists('get_rises')) {
    function get_rises()
    {
        return \Botble\Rises\Models\Rises::where('status', 'published')->pluck('name', 'id')->all();
    }
}

if (!function_exists('get_fabrics')) {
    function get_fabrics()
    {
        return \Botble\Fabrics\Models\Fabrics::where('status', 'published')->pluck('name', 'id')->all();
    }
}

if (!function_exists('get_washes')) {
    function get_washes()
    {
        return \Botble\Wash\Models\Wash::where('status', 'published')->pluck('name', 'id')->all();
    }
}

if (!function_exists('get_category_sizes')) {
    function get_category_sizes()
    {
        return Botble\Categorysizes\Models\Categorysizes::where('status', 'published')->pluck('name', 'id')->all();
    }
}

if (!function_exists('get_product_units')) {
    function get_product_units()
    {
        return \Botble\Vendorproductunits\Models\Vendorproductunits::where('status', 'published')->pluck('name', 'id')->all();
    }
}

if (!function_exists('get_vendor_products')) {
    function get_vendor_products()
    {
        return \Botble\Vendorproducts\Models\Vendorproducts::where('status', 'published')->pluck('name', 'id')->all();
    }
}

if (!function_exists('get_vendor_order_statuses')) {
    function get_vendor_order_statuses()
    {
        $statuses = \Botble\Vendororderstatuses\Models\Vendororderstatuses::where('status', 'published')->pluck('name')->all();
        $arr = [];
        foreach ($statuses as $status) {
            $arr[] = ['value' => strtolower($status), 'text' => ucwords($status)];
        }
        return $arr;
    }
}

if (!function_exists('get_reg_product_categories_custom')) {
    function get_reg_product_categories_custom()
    {
        return \Botble\Ecommerce\Models\ProductCategory::where('status', 'published')->where('is_plus_cat', 0)->pluck('name', 'id')->all();
    }
}

if (!function_exists('get_plu_product_categories_custom')) {
    function get_plu_product_categories_custom()
    {
        return \Botble\Ecommerce\Models\ProductCategory::where('status', 'published')->where('is_plus_cat', 1)->pluck('name', 'id')->all();
    }
}
//Get General Data


//Thread Related Functions
if (!function_exists('get_thread_comments')) {
    function get_thread_comments($id)
    {
        return \App\Models\ThreadComment::where('thread_id', $id)->with(['user'])->get();
    }
}

if (!function_exists('get_thread_variations')) {
    function get_thread_variations($id)
    {
        return \App\Models\ThreadVariation::where('thread_id', $id)->with(['printdesign', 'fabrics', 'wash', 'trim'])->get();
    }
}

if (!function_exists('generate_unique_attr_id')) {
    function generate_unique_attr_id()
    {
        $number = mt_rand(1000000000, 9999999999);
        if (attr_id_exist($number)) {
            generate_unique_attr_id();
        }
        return $number;
    }
}

if (!function_exists('attr_id_exist')) {
    function attr_id_exist($num)
    {
        return \Illuminate\Support\Facades\DB::table('ec_product_variations')->where('configurable_product_id', $num)->exists();
    }
}

if (!function_exists('get_total_designs')) {
    function get_total_designs($id)
    {
        return \Botble\Thread\Models\Thread::where('designer_id', $id)->count();
    }
}

if (!function_exists('get_approved_designs')) {
    function get_approved_designs($id)
    {
        return \Botble\Thread\Models\Thread::where('designer_id', $id)->where('status', 'approved')->count();
    }
}

if (!function_exists('generate_thread_sku')) {
    function generate_thread_sku($catId, $designerId, $designerInitial, $isPlus = false)
    {

        $category = ProductCategory::where('id', $catId)->value('sku_initial');
        $categoryCnt = DB::table('category_designer_count')->where(['user_id' => $designerId, 'product_category_id' => $catId])->value('count') + 1;
        $category_sku = strtoupper($designerInitial . $category . $categoryCnt);
        DB::table('category_designer_count')->updateOrInsert(['user_id' => $designerId, 'product_category_id' => $catId], ['user_id' => $designerId, 'product_category_id' => $catId, 'count' => $categoryCnt]);

        if ($isPlus) {
            $category_sku .= '-X';
        }

        return $category_sku;
    }
}
//Thread Related Functions

//Product Pack Count
if (!function_exists('quantity_calculate')) {
    function quantityCalculate($id)
    {
        $category = ProductCategory::where('id', $id)->first();
        $totalQuantity = 0;
        foreach ($category->category_sizes as $cat) {
            $quan = substr($cat->name, strpos($cat->name, "-") + 1);
            $totalQuantity += $quan;
        }
        return $totalQuantity;
    }
}

//Notifications
if (!function_exists('generate_notification')) {
    function generate_notification($message, $from, $to, $url)
    {
        $notify = new \App\Models\Notification();
        $notify->message = $message;
        $notify->from = $from;
        $notify->to = $to;
        $notify->url = $url;
        return $notify->save();
    }
}

if (!function_exists('get_user_notifications')) {
    function get_user_notifications()
    {
        return \App\Models\Notification::where('to', \Illuminate\Support\Facades\Auth::user()->id)->latest()->limit(10)->get();
    }
}
//Notifications


//Utils
if (!function_exists('parse_date')) {
    function parse_date($date)
    {
        return date('d F, Y', strtotime($date));
    }
}

if (!function_exists('get_barcode')) {
    function get_barcode()
    {
        $code = '00' . rand(00000000000, 99999999999);
        $aa = new DNS1D();
        $image = $aa->getBarcodePNG($code, 'C39', 2, 33);
        $name = 'products_barcode/' . $code . '.' . 'jpg';
        Storage::put($name, base64_decode($image));
        return ['upc' => $code, 'barcode' => $name];
    }
}
//Utils
