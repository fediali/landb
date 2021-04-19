<?php
namespace Theme\Landb\Repositories;


use Botble\Ecommerce\Models\Product;

class ProductsRepository{
  private $model;
  public function __construct(Product $product) {
    $this->model = $product;
  }

  public function getProductsByParams($params = []){

    $is_featured = (isset($params['is_featured'])) ? true : false;
    $limit = (isset($params['limit'])) ? $params['limit'] : null;
    $array = (isset($params['array'])) ? true : false;
    $paginate = (isset($params['paginate'])) ? true : false;
    $simplePaginate = (isset($params['simplePaginate'])) ? true : false;
    $latest = (isset($params['latest'])) ? true : false;
    $first = (isset($params['first'])) ? true : false;
    $id = (isset($params['id'])) ? $params['id'] : null;
    $category = (isset($params['category'])) ? true : false;


    $data = $this->model->with(['productAttributeSets'])->where($this->model->getTable().'.quantity', '>', 0)
            ->when($category , function ($query){
                $query->with(['category' => function($que){
                  $que->with('category_sizes');
                }]);
            })
            ->when($is_featured , function ($query){
                $query->where($this->model->getTable().'.is_featured', 1);
            })
            ->when(!is_null($id) , function ($query) use ($id){
                $query->where('id', $id);
            })
            ->when(!is_null($limit) , function ($query) use ($limit){
                $query->limit($limit);
            })
            ->when($latest , function ($query){
                $query->latest();
            });

      if($paginate){
        $data = $data->paginate();
      }elseif($simplePaginate){
        $data = $data->simplePaginate();
      }elseif($first){
        $data = $data->first();
      }else{
        $data = $data->get();
      }
    return $data;
  }

}