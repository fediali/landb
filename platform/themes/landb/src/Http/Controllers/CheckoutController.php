<?php

namespace Theme\Landb\Http\Controllers;

use App\Http\Controllers\Controller;
/*use Botble\Theme\Theme;*/

use App\Models\CardPreAuth;
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
use Botble\Payment\Models\Payment;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
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
  protected $bankTransferPaymentService;
  public function __construct(PayPalPaymentService $payPalService, OrderInterface $orderRepository, BankTransferPaymentService $bankTransferPaymentService) {
    $this->payPalService = $payPalService;
    $this->orderRepository = $orderRepository;
    $this->bankTransferPaymentService = $bankTransferPaymentService;
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

      case PaymentMethodEnum::BANK_TRANSFER:
        $charge = $this->charge($request);
        if($charge){
          $chargeId = $this->bankTransferPaymentService->execute($request);
          $payment = Payment::where('charge_id' , $chargeId)->first();
          //dd($payment);
          $order = auth('customer')->user()->pendingOrder()->update(['is_finished' => 1, 'payment_id' => $payment->id]);
          return Theme::scope('orderSuccess')->render();
        }
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
    $chargeId = $palPaymentService->afterMakePayment($request);
    $token = OrderHelper::getOrderSessionToken();
    $order = $this->orderRepository->getFirstBy(compact('token'), [], ['address', 'products']);
    if ($token !== session('tracked_start_checkout') || !$order) {
      return $response->setNextUrl(url('/'));
    }
    $payment = Payment::where('charge_id' , $chargeId)->first();
    //dd($payment);
    $order->update(['is_finished' => 1, 'payment_id' => $payment->id]);
    OrderHelper::clearSessions($token);

    /*return $response
        ->setNextUrl(url('/'))
        ->setMessage(__('Checkout successfully!'));*/

    return Theme::scope('orderSuccess')->render();
  }


  public function charge(Request $request)
  {
    $data = [
        'payment_method' => $request->payment_method,
        'meta'              => [
            'reference' => $request->order_id,
            'tax'       => 0,
            'subtotal'  => $request->sub_total,
            'lineItems' => []
        ],
        'total'             => $request->amount,
        'pre_auth'          => 1
    ];
    $url = (env("OMNI_URL") . "charge/");
    list($response, $info) = omni_api($url, $data, 'POST');

    $status = $info['http_code'];

    if (floatval($status) == 200) {
      $response = json_decode($response, true);
      $order['order_id'] = $request->order_id;
      $order['transaction_id'] = $response['id'];
      $order['response'] = json_encode($response);
      $order['status'] = 0;
      CardPreAuth::create($order);
    } else {
      $errors = [
          422 => 'The transaction didn\'t reach a gateway',
          400 => 'The transaction didn\'t reach a gateway but there weren\'t validation errors',
          401 => 'The account is not yet activated or ready to process payments.',
          500 => 'Unknown issue - Please contact Fattmerchant'
      ];
      return $errors;
    }

    //$response->setMessage('Payment Successfully');
    return true;
  }

}