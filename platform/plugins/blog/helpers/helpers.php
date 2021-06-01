<?php

use App\Models\InventoryHistory;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Supports\SortItemsWithChildrenHelper;
use Botble\Blog\Repositories\Interfaces\CategoryInterface;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Blog\Repositories\Interfaces\TagInterface;
use Botble\Blog\Supports\PostFormat;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Orderstatuses\Models\Orderstatuses;
use Botble\Paymentmethods\Models\Paymentmethods;
use Carbon\Carbon;
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

if (!function_exists('get_designers_for_thread')) {
    function get_designers_for_thread()
    {
        return \App\Models\User::join('role_users', 'users.id', 'role_users.user_id')
            ->join('roles', 'role_users.role_id', 'roles.id')
            ->whereIn('roles.slug', ['designer', 'admin', 'product-developmentquality-control', 'design-manager'])
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

if (!function_exists('get_salesperson')) {
    function get_salesperson()
    {
        return \App\Models\User::join('role_users', 'users.id', 'role_users.user_id')
            ->join('roles', 'role_users.role_id', 'roles.id')
            ->whereIn('roles.slug', [\Botble\ACL\Models\Role::ONLINE_SALES, \Botble\ACL\Models\Role::IN_PERSON_SALES])
            ->pluck('users.username', 'users.id')->all();
    }
}

if (!function_exists('get_private_customers')) {
    function get_private_customers()
    {
        return \Botble\Ecommerce\Models\Customer::where('is_private', 1)->pluck('name', 'id')->all();
    }
}

if (!function_exists('get_photographers')) {
    function get_photographers()
    {
        return \App\Models\User::join('role_users', 'users.id', 'role_users.user_id')
            ->join('roles', 'role_users.role_id', 'roles.id')
            ->where('roles.slug', 'photographer')
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

if (!function_exists('get_category_sizes_by_id')) {
    function get_category_sizes_by_id($id = null)
    {
        if (!is_null($id)) {
            $get = ProductCategory::with('category_sizes')->find($id);
            return $get;
        } else {
            return null;
        }

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
    function generate_notification($type, $data)
    {
        try {
            $notification = array();
            $notifiable = array();
            $designer = [];
            $vendor = [];
            if ($type == 'thread_created') {
                $creator = \Botble\ACL\Models\User::find($data->designer_id);
                $notification = array();
                $notification['sender_id'] = $data->designer_id;
                $notification['url'] = route('thread.details', $data->id);
                $notification['action'] = $type;
                $notification['ref_id'] = $data->id;
                $notification['message'] = 'A new thread has been created by ' . $creator->first_name;
                $notification['url'] = route('thread.details', $data->id);

                if (!empty($data->vendor_id)) {
                    $vendor[] = $data->vendor_id;
                }

                $notifiable[] = get_design_manager();

            } elseif ($type == 'thread_updated') {
                $notification = array();
                $notification['sender_id'] = \Illuminate\Support\Facades\Auth::user()->id;
                $notification['url'] = route('thread.details', $data->id);
                $notification['action'] = $type;
                $notification['ref_id'] = $data->id;
                $notification['message'] = 'A thread has been updated by ' . \Illuminate\Support\Facades\Auth::user()->first_name;
                $notification['url'] = route('thread.details', $data->id);

                if (!empty($data->vendor_id)) {
                    $vendor[] = $data->vendor_id;
                }
                if ($notification['sender_id'] != $data->designer_id) {
                    $designer[] = $data->designer_id;
                }
                if (!get_design_manager()->contains($notification['sender_id'])) {
                    $notifiable[] = get_design_manager();
                }
            } elseif ($type == 'thread_status_updated') {
                $notification = array();
                $notification['sender_id'] = \Illuminate\Support\Facades\Auth::user()->id;
                $notification['url'] = route('thread.details', $data->id);
                $notification['action'] = $type;
                $notification['ref_id'] = $data->id;
                $notification['message'] = \Illuminate\Support\Facades\Auth::user()->first_name . ' has updated the thread status to ' . $data->status;
                $notification['url'] = route('thread.details', $data->id);

                if (!empty($data->vendor_id)) {
                    $vendor[] = $data->vendor_id;
                }
                if ($notification['sender_id'] != $data->designer_id) {
                    $designer[] = $data->designer_id;
                }

                if (!get_design_manager()->contains($notification['sender_id'])) {
                    $notifiable[] = get_design_manager();
                }

            } elseif ($type == 'thread_discussion') {
                $notification = array();
                $notification['sender_id'] = \Illuminate\Support\Facades\Auth::user()->id;
                $notification['url'] = route('thread.details', $data->id);
                $notification['action'] = $type;
                $notification['ref_id'] = $data->id;
                $notification['message'] = \Illuminate\Support\Facades\Auth::user()->first_name . ' added a comment to the discussion in "' . $data->name . '" thread';
                $notification['url'] = route('thread.details', $data->id);

                if (!empty($data->vendor_id)) {
                    $vendor[] = $data->vendor_id;
                }
                if ($notification['sender_id'] != $data->designer_id) {
                    $designer[] = $data->designer_id;
                }
                if (!get_design_manager()->contains($notification['sender_id'])) {
                    $notifiable[] = get_design_manager();
                }
            } elseif ($type == 'pre_order_max_qty') {
                $notification = array();
                $notification['sender_id'] = auth()->user()->id;
                $notification['url'] = route('products.edit', $data->id);
                $notification['action'] = $type;
                $notification['ref_id'] = $data->id;
                $notification['message'] = $data->name . ' has reached the max qty of order.';
                $notifiable[] = get_design_manager();
            }

            $notify = new \App\Models\Notification();
            $notification_data = $notify->create($notification);
            $other = array_merge($vendor, $designer);
            if ($notification_data) {
                notify_users($notifiable, $notification_data, $data, $other);
            }
        } catch (Exception $e) {
            return $e;
        }

    }
}

if (!function_exists('get_user_notifications')) {
    function get_user_notifications()
    {
        return \App\Models\UserNotifications::where('user_id', \Illuminate\Support\Facades\Auth::user()->id)->latest()->get();
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

if (!function_exists('get_design_manager')) {
    function get_design_manager()
    {
//        return \App\Models\User::join('role_users', 'users.id', 'role_users.user_id')
//            ->join('roles', 'role_users.role_id', 'roles.id')
//            ->where('roles.slug', 'design-manager')
//            ->pluck('users.id');

        $manager = DB::table('role_users')->leftJoin('roles', 'roles.id', 'role_users.role_id')
            ->whereIn('roles.slug', ['design-manager', 'product-developmentquality-control', 'admin'])
//            ->where('roles.slug', 'design-manager')
            ->pluck('user_id');

        return $manager;
//        if ($manager) {
//            return $manager->id;
//        } else {
//            return null;
//        }
    }
}

if (!function_exists('notify_users')) {
    function notify_users($notifiables, $notification, $resource_data, $other = null)
    {
        //Todo Refactor
        if ($other != null) {
            foreach ($other as $item) {
                $user_notification['notification_id'] = $notification->id;
                $user_notification['user_id'] = $item;
                $noti = \App\Models\UserNotifications::create($user_notification);
                if ($noti) {
                    broadcast(new \App\Events\NotifyManager($item, $notification, $resource_data));
                }
            }
        }
        if (count($notifiables)) {
            foreach ($notifiables as $notifiable) {

                foreach ($notifiable as $key => $value) {

                    $user_notification['notification_id'] = $notification->id;
                    $user_notification['user_id'] = $value;
                    $noti = \App\Models\UserNotifications::create($user_notification);
                    if ($noti) {
                        broadcast(new \App\Events\NotifyManager($value, $notification, $resource_data));
                    }
                }

            }

        }

    }
}

if (!function_exists('create_customer')) {
    function create_customer($data)
    {
        $customer = DB::table('ec_customers')->insert($data);
        return $customer;
    }
}

if (!function_exists('omni_api')) {
    function omni_api($url, $data = [], $type = 'GET')
    {
        $curl = curl_init();
        $request = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $type,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . env('OMNI_SANDBOX_TOKEN')]
        ];

        if ($type == 'POST') {
            $request[CURLOPT_POSTFIELDS] = json_encode($data);
            $request[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        }

        curl_setopt_array($curl, $request);

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        return [$response, $info];
    }
}

if (!function_exists('get_order_statuses')) {
    function get_order_statuses()
    {
        $pl = [];
        $statuses = Orderstatuses::where('status', 'published')->get();
        foreach ($statuses as $status) {
            $pl[] = [
                'value' => strtolower($status->name),
                'text'  => strtoupper($status->name),
            ];
        }
        return $pl;
    }
}

if (!function_exists('get_payment_methods')) {
    function get_payment_methods()
    {
        $payment_methods = Paymentmethods::where('status', 'published')->get()->toArray();
        return $payment_methods;
    }
}

if (!function_exists('set_product_oos_date')) {
    function set_product_oos_date($orderId, $product, $qty, $preQty)
    {
        if ($product->is_variation && $product->quantity < 1) {
            if (!$product->oos_date) {
                DB::table('ec_products')->where('id', $product->id)->update(['oos_date' => Carbon::now()]);
                $getParentProdId = ProductVariation::where('product_id', $product->id)->value('configurable_product_id');
                DB::table('ec_products')->where('id', $getParentProdId)->update(['oos_date' => Carbon::now()]);
                $logParam = [
                    'parent_product_id' => $getParentProdId,
                    'product_id'        => $product->id,
                    'sku'               => $product->sku,
                    'quantity'          => $qty,
                    'new_stock'         => $product->quantity,
                    'old_stock'         => $preQty,
                    'order_id'          => $orderId,
                    'created_by'        => auth()->user()->id,
                    'reference'         => InventoryHistory::PROD_OSS
                ];
                log_product_history($logParam);
            }
        } else {
            DB::table('ec_products')->where('id', $product->id)->update(['oos_date' => NULL]);
        }
    }
}

if (!function_exists('log_product_history')) {
    function log_product_history($params)
    {
        InventoryHistory::create([
            'parent_product_id' => isset($params['parent_product_id']) ? $params['parent_product_id'] : NULL,
            'product_id'        => isset($params['product_id']) ? $params['product_id'] : NULL,
            'sku'               => isset($params['sku']) ? $params['sku'] : NULL,

            'quantity'  => isset($params['quantity']) ? $params['quantity'] : NULL,
            'new_stock' => isset($params['new_stock']) ? $params['new_stock'] : NULL,
            'old_stock' => isset($params['old_stock']) ? $params['old_stock'] : NULL,

            'order_id'        => isset($params['order_id']) ? $params['order_id'] : NULL,
            'inventory_id'    => isset($params['inventory_id']) ? $params['inventory_id'] : NULL,
            'thread_order_id' => isset($params['thread_order_id']) ? $params['thread_order_id'] : NULL,

            'created_by' => auth()->user()->id,
            'reference'  => isset($params['reference']) ? $params['reference'] : NULL
        ]);
    }
}

if (!function_exists('get_pvt_cat_size_qty')) {
    function get_pvt_cat_size_qty($threadId, $prodCatId, $catSizeId)
    {
        return \Botble\Thread\Models\ThreadPvtCatSizesQty::where(['thread_id' => $threadId, 'product_category_id' => $prodCatId, 'category_size_id' => $catSizeId])->value('qty');
    }
}
//Utils
