<?php

namespace Theme\Landb\Http\Controllers;

use App\Http\Controllers\Controller;
/*use Botble\Theme\Theme;*/

use App\Models\UserCart;
use App\Models\UserCartItem;
use App\Models\UserWishlist;
use App\Models\UserWishlistItems;
use BaseHelper;
use Botble\Ecommerce\Cart\CartItem;
use Botble\Ecommerce\Models\Product;
use Botble\Page\Models\Page;
use Botble\Page\Services\PageService;
use Botble\Theme\Events\RenderingSingleEvent;
use Botble\Theme\Events\RenderingHomePageEvent;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Theme\Landb\Repositories\ProductsRepository;
use Response;
use SeoHelper;
use SiteMapManager;
use SlugHelper;
use Theme;
use Cart;
/*use Botble\Theme\Http\Controllers\PublicController;*/

class WishlistController extends Controller
{

  public function getIndex(){
    $wishlist = UserWishlist::where('id', $this->getUserWishlist())->with(['wishlistItems' => function($query){
      $query->with(['product']);
    }])->first();
    //dd($wishlist);
    return Theme::scope('wishlist', ['wishlist' => $wishlist])->render();
  }

  public function getUserWishlist(){
    $check = auth('customer')->user()->UserWishlistId();
    if(!$check){
      $cart = UserWishlist::create(['user_id' => auth('customer')->user()->id]);
      return $cart->id;
    }else{
      return auth('customer')->user()->UserWishlistId();
    }
  }

  public function addToWishlist($id){
    $product = Product::find($id);
    if($product){
      $wishlistItem = $this->createWishlistItem($id);
      if($wishlistItem){
        return response()->json(['message' => 'Item added to wishlist'], 200);
      }else{
        return response()->json(['message' => 'Server Error'], 500);
      }
    }else{
      return response()->json(['message' => 'Product not found'], 404);
    }
  }

  public function createWishlistItem($id){
    $wishlistId = $this->getUserWishlist();
    $checkWishlist = UserWishlistItems::where('wishlist_id', $wishlistId)->where('product_id', $id)->first();
    if($checkWishlist){
      $update =  $checkWishlist->update(['quantity' => $checkWishlist->quantity+1 , 'status' => 'active']);
      if($update){
        return true;
      }
    }else{
      $create = UserWishlistItems::create([
          'wishlist_id' => $wishlistId,
          'product_id' => $id,
          'quantity' => 1
      ]);
      if($create){
        return true;
      }
    }
    return false;
  }

}