<?php

namespace Theme\Landb\Http\Controllers;

use App\Http\Controllers\Controller;
/*use Botble\Theme\Theme;*/

use App\Models\UserCart;
use App\Models\UserCartItem;
use App\Models\UserWishlist;
use App\Models\UserWishlistItems;
use BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Cart\CartItem;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Supports\EcommerceHelper;
use Botble\Page\Models\Page;
use Botble\Page\Services\PageService;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Http\Requests\PayPalPaymentCallbackRequest;
use Botble\Payment\Services\Gateways\PayPalPaymentService;
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

class CheckoutController extends Controller
{
  protected $payPalService;
  protected $orderRepository;
  public function __construct(PayPalPaymentService $payPalService, OrderInterface $orderRepository) {
    $this->payPalService = $payPalService;
    $this->orderRepository = $orderRepository;
  }

  public function getCheckoutIndex($token){
    $cart = $cart = Order::where('id', $this->getUserCart())->with(['products' => function($query){
              $query->with(['product']);
            }])->first();

    $user = Customer::where('id', auth('customer')->user()->id)->with(['details', 'shippingAddress', 'billingAddress'])->first();

    return Theme::scope('checkout', ['cart' => $cart, 'user_info' => $user, 'token' => $token])->render();
  }

  public function getUserCart(){
    $check = auth('customer')->user()->pendingOrder();
    if(!$check){
      $cart = Order::create(['user_id' => auth('customer')->user()->id, 'amount' => 0, 'sub_total' => 0, 'is_finished' => 0]);
      return $cart->id;
    }else{
      return auth('customer')->user()->pendingOrderId();
    }
  }

  public function proceedPayment(Request $request){
    $returnUrl = $request->input('return_url');

    $currency = $request->input('currency', config('plugins.payment.payment.currency'));
    $currency = strtoupper($currency);

    $data = [
        'error'    => false,
        'message'  => false,
        'amount'   => $request->input('amount'),
        'currency' => $currency,
        'type'     => $request->input('payment_method'),
    ];
    switch ($request->input('payment_method')) {
      case PaymentMethodEnum::PAYPAL:
        $checkoutUrl = $this->payPalService->execute($request);
        if ($checkoutUrl) {
          return redirect($checkoutUrl);
        }

        $data['error'] = true;
        $data['message'] = $this->payPalService->getErrorMessage();
        break;
      default:
        $data = apply_filters(PAYMENT_FILTER_AFTER_POST_CHECKOUT, $data, $request);
        break;
    }
    if ($data['error']) {
      return redirect()->back()->with('error_msg', $data['message'])->withInput($request->input());
    }

    $callbackUrl = $request->input('callback_url') . '?' . http_build_query($data);

    return redirect()->to($callbackUrl)->with('success_msg', trans('plugins/payment::payment.checkout_success'));
  }

  public function getCheckoutSuccess($token, BaseHttpResponse $response)
  {

    $order = $this->orderRepository->getFirstBy(compact('token'), [], ['address', 'products']);

    if ($token !== session('tracked_start_checkout') || !$order) {
      return $response->setNextUrl(url('/'));
    }

    OrderHelper::clearSessions($token);

    return view('plugins/ecommerce::orders.thank-you', compact('order'));
  }

  public function getPayPalStatus(
      PayPalPaymentCallbackRequest $request,
      PayPalPaymentService $palPaymentService,
      BaseHttpResponse $response
  ) {
    $palPaymentService->afterMakePayment($request);
    $token = $token = OrderHelper::getOrderSessionToken();
    $order = $this->orderRepository->getFirstBy(compact('token'), [], ['address', 'products']);
    if ($token !== session('tracked_start_checkout') || !$order) {
      return $response->setNextUrl(url('/'));
    }
    $order->update(['status' => 'processed', 'is_confirmed' => 1]);
    OrderHelper::clearSessions($token);

    return $response
        ->setNextUrl(url('/'))
        ->setMessage(__('Checkout successfully!'));
  }

}