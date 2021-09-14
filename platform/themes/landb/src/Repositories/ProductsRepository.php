<?php

namespace Theme\Landb\Repositories;


use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Slug\Models\Slug;
use Carbon\Carbon;

class ProductsRepository
{
    private $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function getProductsByParams($params = [])
    {

        $is_featured = (isset($params['is_featured'])) ? true : false;
        $limit = (isset($params['limit'])) ? $params['limit'] : null;
        $array = (isset($params['array'])) ? true : false;
        $paginate = (isset($params['paginate'])) ? true : false;
        $simplePaginate = (isset($params['simplePaginate'])) ? true : false;
        $latest = (isset($params['latest'])) ? true : false;
        $first = (isset($params['first'])) ? true : false;
        $slug = (isset($params['slug'])) ? $params['slug'] : null;
        $category = (isset($params['category'])) ? true : false;
        $category_slug = (isset($params['category_slug'])) ? $params['category_slug'] : null;

        /*FILTERS*/

        $tag_slug = isset($_GET['t_slug']) ? $_GET['t_slug'] : null;
        $price_range = isset($_GET['price']) ? $_GET['price'] : null;
        $category_slug = isset($_GET['c_slug']) ? $_GET['c_slug'] : $category_slug;
        /*$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : $limit;*/
        $size_id = isset($_GET['size']) ? $_GET['size'] : null;
        $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : null;
        $price = isset($params['price_search']) ? $params['price_search'] : null;
        $search = isset($params['name_search']) ? $params['name_search'] : null;

        $category_id = $tag_id = null;
        if (!is_null($category_slug)) {
            $category = Slug::where('prefix', 'product-categories')->where('key', $category_slug)->first();
            if ($category) {
                $category_id = $category->reference_id;
            }
        }

        $tag_id = null;
        $pre_order = false;
        $pre_order_id = null;
        if ($category_slug == 'pre-order') {
            $tag_slug = $category_slug;
            $pre_order = true;
        }

        if ($pre_order) {
            $pre_order_id = Slug::where('prefix', 'product-tags')->where('key', 'pre-order')->pluck('reference_id')->first();
        }

        if (!is_null($tag_slug)) {
            $tag = Slug::where('prefix', 'product-tags')->where('key', $tag_slug)->first();
            if ($tag) {
                $tag_id = $tag->reference_id;
            }
        }
        $min_range = $max_range = null;
        if (!is_null($price_range)) {
            $ranges = explode('-', $price_range);
            $min_range = isset($ranges[0]) ? ((!empty($ranges[0])) ? (int)$ranges[0] : null) : null;
            $max_range = isset($ranges[1]) ? ((!empty($ranges[1])) ? (int)$ranges[1] : null) : null;
        }
        $sort_key = $sort_type = null;
        if (!is_null($sort_by)) {
            $sort_break = explode('-', $sort_by);
            $sort_key = isset($sort_break[0]) ? ((!empty($sort_break[0])) ? $sort_break[0] : null) : null;
            $sort_type = isset($sort_break[1]) ? ((!empty($sort_break[1])) ? $sort_break[1] : null) : null;
        }
        $data = $this->model->with(['category'])
            ->join('ec_product_variations as epv', 'epv.configurable_product_id', 'ec_products.id')
            ->join('ec_products as ep', 'epv.product_id', 'ep.id')
            ->when($category, function ($query) {
                $query->with(['category' => function ($que) {
                    $que->with('category_sizes');
                }]);
            })
            ->when($is_featured, function ($query) {
                $query->orderBy($this->model->getTable() . '.is_featured', 'desc');
            })
            ->when(!is_null($price), function ($query) use ($price) {
                $query->where('ec_products.price', $price);
            })
//            ->when(!is_null($slug), function ($query) use ($slug) {
//                $query->join('slugs', 'ec_products.id', 'slugs.reference_id')->where('slugs.key', $slug)->where('slugs.prefix', '=', 'products');
//            })
//            ->when(is_null($slug), function ($query) {
//                $query->join('slugs', 'ec_products.id', 'slugs.reference_id')->where('slugs.prefix', '=', 'products');
//            })
            ->when(!is_null($limit), function ($query) use ($limit) {
                $query->limit($limit);
            })
            ->when((!is_null($category_id) && !in_array($category_slug, ['new-arrival', 'new-arrivals', 'pre-order'])), function ($query) use ($category_id) {

                $category = ProductCategory::where('parent_id', $category_id)->pluck('id')->toArray();
                 array_push($category, $category_id);
                /* $query->where($this->model->getTable().'.category_id', $category_id);*/
                $query->join('ec_product_category_product as epcp', 'epcp.product_id', $this->model->getTable() . '.id')->whereIn('epcp.category_id', $category);
            })
            ->when((!is_null($category_id) && in_array($category_slug, ['new-arrival', 'new-arrivals', 'pre-order'])), function ($query) use ($category_id) {
                /* $query->where($this->model->getTable().'.category_id', $category_id);*/
                $date = Carbon::today()->subWeek();
                $query->where($this->model->getTable() . '.creation_date', '>=', $date);
            })
            ->when(!is_null($tag_id), function ($query) use ($tag_id) {
                $query->join('ec_product_tag_product as ptag', 'ptag.product_id', 'ec_products.id')->where('ptag.tag_id', $tag_id);
            })
            ->when(!$pre_order, function ($query) use ($pre_order_id) {
                $query->leftJoin('ec_product_tag_product as ptag1', 'ptag1.product_id', 'ec_products.id')->where('ptag1.tag_id', '=', $pre_order_id)->where('ep.quantity', '>', 0);
            })
            ->when(!is_null($price_range), function ($query) use ($min_range, $max_range) {
                if (!is_null($min_range)) {
                    $query->where($this->model->getTable() . '.price', '>=', $min_range);
                }
                if (!is_null($max_range)) {
                    $query->where($this->model->getTable() . '.price', '<=', $max_range);
                }
            })
            ->when(!is_null($search), function ($query) use ($search) {
              $query->whereRaw('ec_products.name LIKE "%' . $search . '%" || ec_products.sku LIKE "%' . $search . '%"');
            })
            ->when(!is_null($size_id), function ($query) use ($size_id) {
                $query->join('product_categories_sizes as psizes', 'psizes.product_category_id', 'ec_products.category_id')->where('category_size_id', $size_id);
            })
            ->when(is_null($sort_by), function ($query) {
                $query->orderBy($this->model->getTable() . '.created_at', 'desc');
            })
            ->when(!is_null($sort_by) && !is_null($sort_key) && !is_null($sort_type), function ($query) use ($sort_key, $sort_type) {
                $query->orderBy($this->model->getTable() . '.' . $sort_key, $sort_type);
            })->where($this->model->getTable() . '.status', BaseStatusEnum::ACTIVE)->where($this->model->getTable() . '.is_variation' , 0);
        $data = $data->select('ec_products.*')->groupBy('ec_products.id');
        if ($paginate) {
            $data = $data->simplePaginate(30);
        } elseif ($simplePaginate) {
            $data = $data->simplePaginate();
        } elseif ($first) {
            $data = $data->first();
        } else {
            $data = $data->get();
        }
        return $data;
    }

}
