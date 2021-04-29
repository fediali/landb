<?php

namespace Theme\Landb\Http\Controllers;

use App\Http\Controllers\Controller;
/*use Botble\Theme\Theme;*/
use BaseHelper;
use Botble\Page\Models\Page;
use Botble\Page\Services\PageService;
use Botble\Theme\Events\RenderingSingleEvent;
use Botble\Theme\Events\RenderingHomePageEvent;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Theme\Landb\Repositories\ProductsRepository;
use Response;
use SeoHelper;
use SiteMapManager;
use SlugHelper;
use Theme;
/*use Botble\Theme\Http\Controllers\PublicController;*/

class ProductsController extends Controller
{
  private $productRepo;

  public function __construct(ProductsRepository $productsRepo) {
    $this->productRepo = $productsRepo;
  }

  public function getIndex(){
    $data = [
        'products' => $this->productRepo->getProductsByParams(['latest' => true, 'paginate' => true, 'array' => true])
    ];
    //dd($data['products']);
    return Theme::scope('products', $data)->render();
  }

  public function productsByCategory($category){
    $data = [
        'products' => $this->productRepo->getProductsByParams(['latest' => true, 'paginate' => true, 'array' => true, 'category_slug' => $category])
    ];
    //dd($data['products']);
    return Theme::scope('products', $data)->render();
  }

  public  function getDetails($id){
    $data = [
        'product' => $this->productRepo->getProductsByParams(['first' => true, 'id' => $id, 'category' => true])
    ];
    //dd($data['product']);
    return Theme::scope('product', $data)->render();
  }
}
