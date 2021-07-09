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
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
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
use OrderHelper;

/*use Botble\Theme\Http\Controllers\PublicController;*/

class CartController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = auth('customer')->user();
    }

    public function getIndex(Request $request)
    {
        if ($request->has('discard')) {
            if ($request->discard == 'true') {
                if ($request->has('item')) {
                    OrderProduct::find($request->item)->delete();
                }
            }
        }
        $cart = Order::where('id', $this->getUserCart())->with(['products' => function ($query) {
            $query->with(['product']);
        }])->first();
        if (!count($cart->products)) {
            return redirect()->route('public.products')->with('error', 'Cart is currently empty!');
        }
        $token = OrderHelper::getOrderSessionToken();
        return Theme::scope('cart', ['cart' => $cart])->render();
    }

    public function createCart(Request $request)
    {
        $data = $request->all();
        $product = Product::find($data['product_id']);
        if ($product && $product->quantity >= $data['quantity']) {
            $cart_status = $this->createCartItem($data, $product);
            //dd($cart_status);
            if (empty($cart_status)) {
                return response()->json(['message' => 'Product added to cart successfully'], 200);
            } else {
                return response()->json(['message' => $cart_status], 500);
            }
        } else {
            return response()->json(['message' => 'Required quantity is currently out of stock!'], 403);
        }
    }

    public function getUserCart()
    {
        $check = auth('customer')->user()->pendingOrder();
        $token = OrderHelper::getOrderSessionToken();
        if (!$check) {
            $cart = Order::create([
                'user_id'         => auth('customer')->user()->id,
                'salesperson_id'  => auth('customer')->user()->salesperson,
                'amount'          => 0,
                'sub_total'       => 0,
                'is_finished'     => 0,
                'token'           => $token,
                'tax_amount'      => 0,
                'discount_amount' => 0,
                'shipping_amount' => 0,
                'currency_id'     => 1,
            ]);
            return $cart->id;
        } else {
            return auth('customer')->user()->pendingOrderId();
        }
    }

    public function createCartItem($data, $product)
    {
        $cartId = $this->getUserCart();
        $checkCart = OrderProduct::where('order_id', $cartId)->where('product_id', $product->id)->first();
        if ($checkCart) {
            if ($product->quantity < $checkCart->qty + $data['quantity']) {
                return 'Required quantity is currently out of stock!';
            }
            $update = $checkCart->update(['qty' => $checkCart->qty + $data['quantity']]);
            if ($update) {
                /*update_product_quantity($product->id, $data['quantity'], 'inc');*/
                $this->getOrderAndUpdateAmount();
                return '';
            }
        } else {
            $create = OrderProduct::create([
                'order_id'     => $cartId,
                'product_id'   => $product->id,
                'qty'          => $data['quantity'],
                'price'        => $product->price,
                'tax_amount'   => 0,
                'product_name' => $product->name,
            ]);
            if ($create) {
                /*update_product_quantity($product->id, $data['quantity'], 'inc');*/
                $this->getOrderAndUpdateAmount();
                return '';
            }
        }
        return 'Server Error!';
    }

    public function updateCartQuanity(Request $request)
    {
        $data = $request->all();
        if (!empty($data['id'])) {
            $orderProduct = OrderProduct::where('id', $data['id'])->first();
            $product = Product::find($orderProduct->product_id);
            if ($product) {
                if ($data['action'] == 'inc' && $product->quantity < $orderProduct->qty + 1) {
                    return response()->json(['message' => 'Product is out of stock'], 403);
                }
                if ($data['quantity'] == 0) {
                    $update = $orderProduct->delete();
                } else {
                    $update = $orderProduct->update(['qty' => $data['quantity']]);
                    $this->getOrderAndUpdateAmount();
                }
                if ($update) {
                    /*update_product_quantity($orderProduct->product_id, 1, $data['action']);*/
                    return response()->json(['message' => 'Cart Updated successfully'], 200);
                } else {
                    return response()->json(['message' => 'Server Error'], 500);
                }
            } else {
                return response()->json(['message' => 'Product not Found'], 500);
            }
        } else {
            return response()->json(['message' => 'Cart Item not found'], 404);
        }

    }

    public function getOrderAndUpdateAmount()
    {
        $orderId = $this->getUserCart();
        $products = OrderProduct::where('order_id', $orderId)->get();
        $amount = 0;

        foreach ($products as $product) {
            $amount = $amount + ($product->qty * $product->price);
        }
        Order::find($orderId)->update([
            'sub_total' => $amount,
            'amount'    => $amount
        ]);
    }

    public function deleteCartItem($id)
    {
        $orderProduct = OrderProduct::where('id', $id)->first();

        if ($orderProduct) {
            $update = $orderProduct->delete();
            $this->getOrderAndUpdateAmount();
            if ($update) {
                return redirect()->back()->with('success', 'Cart Item deleted successfully!');
            } else {
                return redirect()->back()->with('error', 'Something went wrong!');
            }
        } else {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    public function clearCart()
    {
        $orderId = $this->getUserCart();
        $products = OrderProduct::where('order_id', $orderId)->delete();
        if ($products) {
            $this->getOrderAndUpdateAmount();
            return redirect()->route('public.products')->with('success', 'Cart cleared successfully!');
        } else {
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }
}
