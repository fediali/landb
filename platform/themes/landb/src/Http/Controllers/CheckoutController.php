<?php

namespace Theme\Landb\Http\Controllers;

use App\Http\Controllers\Controller;

/*use Botble\Theme\Theme;*/

use App\Models\CardPreAuth;
use App\Models\InventoryHistory;
use App\Models\UserCart;
use App\Models\UserCartItem;
use App\Models\UserWishlist;
use App\Models\UserWishlistItems;
use BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Cart\CartItem;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Discount;
use Botble\Ecommerce\Models\DiscountCustomer;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Botble\Ecommerce\Supports\EcommerceHelper;
use Botble\Page\Models\Page;
use Botble\Page\Services\PageService;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Http\Requests\PayPalPaymentCallbackRequest;
use Botble\Payment\Models\Payment;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
use Botble\Payment\Services\Gateways\OmniPaymentService;
use Botble\Payment\Services\Gateways\PayPalPaymentService;
use Botble\Theme\Events\RenderingSingleEvent;
use Botble\Theme\Events\RenderingHomePageEvent;
use Botble\Theme\Events\RenderingSiteMapEvent;
use Carbon\Carbon;
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
    protected $omniPaymentService;
    protected $coupon_service;
    protected $promotion_service;

    public function __construct(PayPalPaymentService $payPalService, OrderInterface $orderRepository, OmniPaymentService $omniPaymentService, HandleApplyCouponService $applyCouponService, HandleApplyPromotionsService $applyPromotionsService)
    {
        $this->payPalService = $payPalService;
        $this->orderRepository = $orderRepository;
        $this->omniPaymentService = $omniPaymentService;
        $this->coupon_service = $applyCouponService;
        $this->promotion_service = $applyPromotionsService;
    }

    public function getCheckoutIndex($token)
    {

        $this->promotion_service->applyPromotionIfAvailable(auth('customer')->user()->getUserCart(), $token);
        $cart = Order::where('id', auth('customer')->user()->getUserCart())->with(['products' => function ($query) {
            $query->with(['product']);
        }])->first();


        if (!count($cart->products)) {
            return redirect()->route('public.products')->with('error', 'Cart is currently empty!');
        }

        foreach ($cart->products as $cartProduct) {
            if ($cartProduct->product->quantity < 1) {
                return redirect()->route('public.cart_index', ['discard' => 'true', 'item' => $cartProduct->id])->with('error', '"' . $cartProduct->product->name . '" is out of stock and is removed from cart!');
            } elseif ($cartProduct->product->quantity < $cartProduct->qty) {
                return redirect()->route('public.cart_index', ['discard' => 'false', 'item' => $cartProduct->id])->with('error', 'Required quantity of "' . $cartProduct->product->name . '" is out of stock. Only' . $cartProduct->product->quantity . ' left in stock!');
            }
        }

        if ($this->checkIfCouponExpired($cart->id)) {
            return redirect()->route('public.checkout_index', $token)->with('error', 'Coupon code is now invalid or expired!');
        }

        $user = Customer::where('id', auth('customer')->user()->id)->with(['details', 'shippingAddress', 'billingAddress', 'addresses'])->first();


        if ($user->card->count() > 0) {
            $omniId = $user->card()->whereNotNull('customer_omni_id')->get();
            foreach ($omniId as $item) {
                if ($item->customer_omni_id) {
                    $url = (env("OMNI_URL") . "customer/" . $item->customer_omni_id . "/payment-method");
                    list($card, $info) = omni_api($url);
                    $cards = collect(json_decode($card))->pluck('nickname', 'id')->push('Add New Card');
                    break;
                } else {
                    $cards = collect()->push('Add New Card');
                }
            }
        } else {
            $cards = collect()->push('Add New Card');
        }

        return Theme::scope('checkout', ['cart' => $cart, 'user_info' => $user, 'token' => $token, 'cards' => $cards])->render();
    }

    public function getUserCart()
    {
        $check = auth('customer')->user()->pendingOrder();
        if (!$check) {
            $cart = Order::create(['user_id' => auth('customer')->user()->id, 'amount' => 0, 'sub_total' => 0, 'is_finished' => 0]);
            return $cart->id;
        } else {
            return auth('customer')->user()->pendingOrderId();
        }
    }

    public function proceedPayment(Request $request)
    {

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

        $order = Order::with('products')->find($request->input('order_id'));
        if ($order->is_finished == 1) {
            return redirect()->route('public.products')->with('error', 'Order is already been placed');
        }
        $order->update(['notes' => $request->notes]);

        if (!isset(auth('customer')->user()->shippingAddress[0]) || !isset(auth('customer')->user()->billingAddress[0])) {
            return redirect()->back()->with('error', 'Shipping or Billing Address Not found!');
        }

        foreach ($order->products as $product) {
            $original = Product::find($product->product_id);
            if ($original->quantity == 0) {
                //dd($original);
                $product->delete();
                return redirect()->route('public.cart_index')->with('error', 'Product: "' . $original->name . '" is currently out of stock');
            } elseif ($original->quantity < $product->qty) {
                return redirect()->route('public.cart_index')->with('error', 'Product: "' . $original->name . '" have only ' . $original->quantity . ' quantity left');
            }
            update_product_quantity($product->product_id, $product->qty, 'dec');
        }
        $this->addOrderAddresses($order->id);

        switch ($request->input('payment_method')) {
            case PaymentMethodEnum::PAYPAL:

                $checkoutUrl = $this->payPalService->execute($request);
                if ($checkoutUrl) {
                    return redirect($checkoutUrl);
                }
                $data['error'] = true;
                $data['message'] = $this->payPalService->getErrorMessage();
                break;

            case PaymentMethodEnum::OMNI_PAYMENT:
                $charge = $this->charge($request);
                if ($charge) {
                    $chargeId = $this->omniPaymentService->execute($request);
                    $payment = Payment::where('charge_id', $chargeId)->first();
                    //dd($payment);
                    $pre = $this->checkIfPreOrder($order->id);
                    $this->checkDiscount($order->id);
                    $order = auth('customer')->user()->pendingOrder()->update(['is_finished' => 1, 'payment_id' => $payment->id, 'status'=> 'new', 'created_at' => Carbon::now()]);
                    return redirect()->route('public.order.success', ['id' => $payment->order_id]);
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
    )
    {
        $chargeId = $palPaymentService->afterMakePayment($request);
        $paymentStatus = $palPaymentService->getPaymentStatus($request);
        $token = OrderHelper::getOrderSessionToken();
        $order_id = $request->input('order_id');
        if (!$paymentStatus) {
            $this->revertProductQuantity($order_id);
            return redirect()->route('public.cart_index', ['payment' => 'false']);
        }
        $this->checkIfPreOrder($order_id);
        $this->checkDiscount($order_id);
        $order = $this->orderRepository->findById($order_id, ['address', 'products']);

        $payment = Payment::where('charge_id', $chargeId)->first();
        //dd($payment);
        $order->update(['is_finished' => 1, 'payment_id' => $payment->id,'status'=> 'new', 'created_at' => Carbon::now()]);


        OrderHelper::clearSessions($token);

        /*return $response
            ->setNextUrl(url('/'))
            ->setMessage(__('Checkout successfully!'));*/

        return redirect()->route('public.order.success', ['id' => $order_id]);
    }


    public function charge(Request $request)
    {

        $data = [
            'payment_method_id' => $request->payment_id,
            'meta'              => [
                'reference' => $request->order_id,
                'tax'       => 0,
                'subtotal'  => $request->sub_total,
                'lineItems' => []
            ],
            'total'             => 1,
            'pre_auth'          => 1
        ];
        $url = (env("OMNI_URL") . "charge/");
        list($response, $info) = omni_api($url, $data, 'POST');
        /* dd($response, $info);*/
        $status = $info['http_code'];

        if (floatval($status) == 200) {
            $response = json_decode($response, true);
//            $order['order_id'] = $request->order_id;
//            $order['transaction_id'] = $response['id'];
//            $order['response'] = json_encode($response);
//            $order['status'] = 0;
//            CardPreAuth::create($order);
            $status['status'] = 'new';
            Order::where('id', $request->order_id)->update($status);
        } else {
            $errors = [
                422 => 'The transaction didn\'t reach a gateway',
                400 => 'The transaction didn\'t reach a gateway but there weren\'t validation errors',
                401 => 'The account is not yet activated or ready to process payments.',
                500 => 'Unknown issue - Please contact Fattmerchant'
            ];
            $response = json_decode($response, true);
            $status = [];
            $status['transaction_error'] = @$response['message'];
            $status['status'] = 'Declined';
            Order::where('id', $request->order_id)->update($status);
//            CardPreAuth::updateOrCreate(['order_id' => $request->order_id, 'card_id' => $request->payment_id], ['response' => $response['message'], 'payment_status' => 'Declined']);
            return $errors;
        }

        //$response->setMessage('Payment Successfully');
        return true;
    }

    public function addOrderAddresses($id)
    {

        $shipping = auth('customer')->user()->shippingAddress;
        $billing = auth('customer')->user()->billingAddress;

        if (isset($shipping[0])) {
            OrderAddress::updateOrCreate(['order_id' => $id, 'type' => 'shipping'], [
                'name'     => $shipping[0]->first_name . ' ' . $shipping[0]->last_name,
                'phone'    => $shipping[0]->phone,
                'email'    => $shipping[0]->email,
                'country'  => $shipping[0]->country,
                'state'    => $shipping[0]->state,
                'city'     => $shipping[0]->city,
                'address'  => $shipping[0]->address,
                'zip_code' => $shipping[0]->zip_code,
            ]);
        }
        if (isset($billing[0])) {
            OrderAddress::updateOrCreate(['order_id' => $id, 'type' => 'billing'], [
                'name'     => $billing[0]->first_name . ' ' . $billing[0]->last_name,
                'phone'    => $billing[0]->phone,
                'email'    => $billing[0]->email,
                'country'  => $billing[0]->country,
                'state'    => $billing[0]->state,
                'city'     => $billing[0]->city,
                'address'  => $billing[0]->address,
                'zip_code' => $billing[0]->zip_code,
            ]);
        }
    }

    public function checkIfPreOrder($id)
    {
        $order = Order::where('id', $id)->with('products')->first();

        $preOrderId = null;
        $orderTotal = 0;
        $includesNormals = false;
        foreach ($order->products as $product) {
            $check = checkIfProductPreOrder($product->product_id);
            //dd($);
            if ($check) {
                if (is_null($preOrderId)) {
                    $token = OrderHelper::getOrderSessionToken();
                    $preOrder = Order::create([
                        'user_id'         => auth('customer')->user()->id,
                        'amount'          => 0,
                        'sub_total'       => 0,
                        'is_finished'     => 0,
                        'token'           => $token,
                        'tax_amount'      => 0,
                        'discount_amount' => 0,
                        'shipping_amount' => 0,
                        'currency_id'     => 1,
                        'status'          => 'pre-order',
                        'parent_order'    => $id,
                        'order_type'      => 'pre_order'
                    ]);
                    $preOrderId = $preOrder->id;
                    //dd($check, $preOrderId);
                }
                $orderTotal = $orderTotal + $product->price;
                OrderProduct::find($product->id)->update(['order_id' => $preOrderId]);
            } else {
                $includesNormals = true;
            }
            $parent = get_parent_product_by_variant($product->product_id);
            $logParam = [
                'parent_product_id' => $parent->id,
                'product_id'        => $product->product_id,
                'sku'               => $product->product->sku,
                'created_by'        => auth('customer')->user()->id,
                'reference'         => InventoryHistory::PROD_ORDER_QTY_DEDUCT
            ];
            log_product_history($logParam, false);
        }

        if (!is_null($preOrderId)) {
            if ($includesNormals) {
                Order::where('id', $preOrderId)->update(['amount' => $orderTotal, 'sub_total' => $orderTotal, 'is_finished' => 1, 'created_at' => Carbon::now()]);
                $current = Order::find($id);
                $current->update(['amount' => $current->amount - $orderTotal, 'sub_total' => $current->amount - $orderTotal]);
            } else {
                Order::where('id', $id)->update(['status' => 'pre-order', 'order_type' => 'pre_order']);
                OrderProduct::where('order_id', $preOrderId)->update(['order_id' => $id]);
                Order::find($preOrderId)->delete();
            }

        }

    }

    public function revertProductQuantity($orderId)
    {
        $order = Order::where('id', $orderId)->with(['products'])->first();
        if ($order) {
            foreach ($order->products as $orderProduct) {
                $product = Product::find($orderProduct->product_id);
                if ($product) {
                    $product->update(['quantity' => $product->quantity + $orderProduct->qty]);
                }
            }
        }
    }

    public function applyCoupon(Request $request)
    {
        $code = $request->coupon_code;
        if (!empty($code)) {
            $discountId = Discount::where('code', $code)->pluck('id')->first();
            if ($discountId) {
                $discounted = DiscountCustomer::where('discount_id', $discountId)->where('customer_id', auth('customer')->user()->id)->first();
                if ($discounted) {
                    return redirect()->back()->with('error', 'Coupon has already been used.');
                }
            }
            /*if(){}*/

            $applyCoupon = $this->coupon_service->execute($code);

            if (!$applyCoupon['error']) {
                $cart = Order::where('id', auth('customer')->user()->getUserCart())->first();
                if ($cart) {
                    if (empty($cart->coupon_code)) {
                        $cart->coupon_code = $code;
                        $cart->discount_amount = $cart->discount_amount + $applyCoupon['data']['discount_amount'];
                        $cart->amount = $cart->sub_total - $cart->discount_amount;
                        if ($cart->save()) {
                            return redirect()->back()->with('success', 'Coupon Applied Successfully');
                        }
                    }
                }
            } else {
                return redirect()->back()->with('error', $applyCoupon['message']);
            }
        }
        return redirect()->back()->with('error', 'Something went wrong or invalid Coupon code');
    }

    public function checkDiscount($id)
    {
        $order = Order::find($id);
        if ($order) {
            $coupon_code = $order->coupon_code;
            if (!empty($coupon_code)) {
                $discount = Discount::where('code', $coupon_code)->first();
                if ($discount) {
                    DiscountCustomer::create(['discount_id' => $discount->id, 'customer_id' => $order->user_id]);
                }
            }
        }
    }

    public function checkIfCouponExpired($id)
    {
        $order = Order::find($id);
        if ($order) {
            $coupon_code = $order->coupon_code;
            if (!empty($coupon_code)) {
                $applyCoupon = $this->coupon_service->execute($coupon_code);

                if ($applyCoupon['error']) {
                    $order->coupon_code = null;
                    $order->discount_amount = 0.00;
                    $order->amount = $order->sub_total;
                    $order->save();
                    return true;
                }
            }
        }
        return false;
    }


}
