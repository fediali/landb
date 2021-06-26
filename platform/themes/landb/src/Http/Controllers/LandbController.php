<?php

namespace Theme\Landb\Http\Controllers;

use App\Http\Controllers\Controller;
/*use Botble\Theme\Theme;*/
use BaseHelper;
use Botble\Page\Models\Page;
use Botble\Page\Services\PageService;
use Botble\SimpleSlider\Models\SimpleSlider;
use Botble\Theme\Events\RenderingSingleEvent;
use Botble\Theme\Events\RenderingHomePageEvent;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Theme\Landb\Repositories\ProductsRepository;
use Response;
use SeoHelper;
use SiteMapManager;
use SlugHelper;
use Theme;
/*use Botble\Theme\Http\Controllers\PublicController;*/

class LandbController extends Controller
{
  private $productRepo;
  protected $homeSliderKey = 'home-slider';

  public function __construct(ProductsRepository $productsRepo) {
    $this->productRepo = $productsRepo;
  }

  public function getIndex(){

    $data = [
        'home_featured' => $this->productRepo->getProductsByParams(['is_featured' => true, 'limit' => 15, 'array' => true]),
        'latest_collection'=> $this->productRepo->getProductsByParams(['latest' => true, 'limit' => 20, 'array' => true]),
        'slider' => $this->getHomeSlideshow()
    ];
    //dd($data['latest_collection']);
   return Theme::scope('index', $data)->render();
  }

  public function orderSuccess(){
    return Theme::scope('orderSuccess', [])->render();
  }

  public function getHomeSlideshow(){
    $data = SimpleSlider::where('key' ,  $this->homeSliderKey)->with(['sliderItems'])->first();
    return $data;
  }
}
