<?php

namespace Theme\Landb\Http\Controllers;

use App\Http\Controllers\Controller;
/*use Botble\Theme\Theme;*/

use Botble\Ecommerce\Models\Order;
use Response;
use SeoHelper;
use SiteMapManager;
use SlugHelper;
use Theme;
use Cart;
/*use Botble\Theme\Http\Controllers\PublicController;*/

class OrderController extends Controller {

  public function index($id) {
    $order = Order::with(['payment', 'shippingAddress', 'billingAddress', 'products' => function($query){
      $query->with(['product']);
    }])->find($id);
//dd($order);
    if(!$order){
      abort('404');
    }
    return Theme::scope('orderSuccess', ['order' => $order])->render();
  }

  public function success($id) {

    $order = Order::with(['payment', 'shippingAddress', 'billingAddress', 'products' => function($query){
      $query->with(['product']);
    }])->find($id);
//dd($order);
    if(!$order){
      abort('404');
    }
    return Theme::scope('orderSuccess', ['order' => $order])->render();
  }

}