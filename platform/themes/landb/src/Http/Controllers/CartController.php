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

class CartController extends Controller
{
  private $user;
  public function __construct() {
    $this->user = auth('customer')->user();
  }

  public function getIndex(){
    $cart = UserCart::where('id', $this->getUserCart())->with(['cartItems' => function($query){
      $query->with(['product']);
    }])->first();
    return Theme::scope('cart', ['cart' => $cart])->render();
  }

  public function createCart(Request $request){
    $data = $request->all();
    $product = Product::find($data['product_id']);
    if($product){
      $cartItem = $this->createCartItem($data, $product);
      if($cartItem){
        return response()->json(['message' => 'Item added to cart'], 200);
      }else{
        return response()->json(['message' => 'Server Error'], 500);
      }
    }else{
      return response()->json(['message' => 'Product not found'], 404);
    }
  }

  public function getUserCart(){
    $check = auth('customer')->user()->UserCartId();
    if(!$check){
      $cart = UserCart::create(['user_id' => auth('customer')->user()->id]);
      return $cart->id;
    }else{
      return auth('customer')->user()->UserCartId();
    }
  }

  public function createCartItem($data, $product){
    $cartId = $this->getUserCart();
    $checkCart = UserCartItem::where('cart_id', $cartId)->where('product_id', $product->id)->first();
    if($checkCart){
     $update =  $checkCart->update(['quantity' => $checkCart->quantity+$data['quantity'] , 'status' => 'active']);
     if($update){
       return true;
     }
    }else{
      $create = UserCartItem::create([
          'cart_id' => $cartId,
          'product_id' => $product->id,
          'quantity' => $data['quantity'],
          'price' => $product->price
      ]);
      if($create){
        return true;
      }
    }
    return false;
  }

  public function updateCartQuanity(Request $request){
    $data = $request->all();
    if(!empty($data['id'])){
      if($data['quantity'] == 0){
        $update = UserCartItem::where('id', $data['id'])->delete();
      }else{
        $update = UserCartItem::where('id', $data['id'])->update(['quantity' => $data['quantity']]);
      }
      if($update){
        return response()->json(['message' => 'Cart Updated'], 200);
      }else{
        return response()->json(['message' => 'Server Error'], 500);
      }
    }else{
      return response()->json(['message' => 'Cart Item not found'], 404);
    }

  }


}