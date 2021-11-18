<?php

namespace Botble\Ecommerce\Http\Controllers;

use App\Events\OrderEdit;
use App\Imports\OrderImportFile;
use App\Mail\OrderCreate;
use App\Models\CardPreAuth;
use App\Models\CustomerCard;
use App\Models\InventoryHistory;
use App\Models\OrderImport;
use App\Models\OrderImportUpload;
use App\Models\OrderSplitPayment;
use Assets;
use Botble\Accountingsystem\Models\Accountingsystem;
use Botble\ACL\Models\Role;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Http\Requests\AddressRequest;
use Botble\Ecommerce\Http\Requests\ApplyCouponRequest;
use Botble\Ecommerce\Http\Requests\CreateOrderRequest;
use Botble\Ecommerce\Http\Requests\CreateShipmentRequest;
use Botble\Ecommerce\Http\Requests\RefundRequest;
use Botble\Ecommerce\Http\Requests\UpdateOrderRequest;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\CustomerDetail;
use Botble\Ecommerce\Models\Discount;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\OrderProductShipmentVerify;
use Botble\Ecommerce\Models\OrderRefundProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\UserSearch;
use Botble\Ecommerce\Models\UserSearchItem;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\DiscountInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderAddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Botble\Ecommerce\Repositories\Interfaces\StoreLocatorInterface;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Botble\Ecommerce\Services\HandleShippingFeeService;
use Botble\Ecommerce\Tables\OrderIncompleteTable;
use Botble\Ecommerce\Tables\OrderTable;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Carbon\Carbon;
use EmailHandler;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use OrderHelper;
use PayPal\Api\BillingInfo;
use PayPal\Api\Currency;
use PayPal\Api\Invoice;
use PayPal\Api\InvoiceAddress;
use PayPal\Api\InvoiceItem;
use PayPal\Api\MerchantInfo;
use PayPal\Api\Notification;
use PayPal\Api\PaymentTerm;
use PayPal\Api\Phone;
use PayPal\Api\ShippingInfo;
use RvMedia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;


class OrderController extends BaseController
{
    /**
     * @var OrderInterface
     */
    protected $orderRepository;

    /**
     * @var CustomerInterface
     */
    protected $customerRepository;

    /**
     * @var OrderHistoryInterface
     */
    protected $orderHistoryRepository;

    /**
     * @var ProductInterface
     */
    protected $productRepository;

    /**
     * @var ShipmentInterface
     */
    protected $shipmentRepository;

    /**
     * @var OrderHistoryInterface
     */
    protected $orderAddressRepository;

    /**
     * @var PaymentInterface
     */
    protected $paymentRepository;

    /**
     * @var StoreLocatorInterface
     */
    protected $storeLocatorRepository;

    /**
     * @var OrderProductInterface
     */
    protected $orderProductRepository;

    /**
     * @var AddressInterface
     */
    protected $addressRepository;

    protected $promotion_service;

    /**
     * @var DiscountInterface
     */
    protected $discountRepository;

    /**
     * @param OrderInterface $orderRepository
     * @param CustomerInterface $customerRepository
     * @param OrderHistoryInterface $orderHistoryRepository
     * @param ProductInterface $productRepository
     * @param ShipmentInterface $shipmentRepository
     * @param OrderAddressInterface $orderAddressRepository
     * @param PaymentInterface $paymentRepository
     * @param StoreLocatorInterface $storeLocatorRepository
     * @param OrderProductInterface $orderProductRepository
     * @param AddressInterface $addressRepository
     */
    public function __construct(
        OrderInterface $orderRepository,
        CustomerInterface $customerRepository,
        OrderHistoryInterface $orderHistoryRepository,
        ProductInterface $productRepository,
        ShipmentInterface $shipmentRepository,
        OrderAddressInterface $orderAddressRepository,
        PaymentInterface $paymentRepository,
        StoreLocatorInterface $storeLocatorRepository,
        OrderProductInterface $orderProductRepository,
        AddressInterface $addressRepository,
        HandleApplyPromotionsService $applyPromotionsService,
        DiscountInterface $discountRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->productRepository = $productRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->paymentRepository = $paymentRepository;
        $this->storeLocatorRepository = $storeLocatorRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->addressRepository = $addressRepository;
        $this->promotion_service = $applyPromotionsService;
        $this->discountRepository = $discountRepository;
    }

    /**
     * @param OrderTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function index(OrderTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/ecommerce::order.name'));
        return $dataTable->renderTable();
    }

    /**
     * @return Factory|View
     */
    public function create()
    {
        Assets::addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/libraries/jquery.textarea_autosize.js',
                'vendor/core/plugins/ecommerce/js/order-create.js',
            ])
            ->addScripts(['blockui', 'input-mask']);

        page_title()->setTitle(trans('plugins/ecommerce::order.create'));
        // return view('plugins/ecommerce::orders.create');
        return view('plugins/ecommerce::orders.create-back');
    }

    /**
     * @param CreateOrderRequest $request
     * @param BaseHttpResponse $response
     */
    public function store(CreateOrderRequest $request, BaseHttpResponse $response)
    {
        $condition = [];
        $meta_condition = [];

        $curOrderProdIds = [];
        foreach ($request->input('products', []) as $productItem) {
            $product = $this->productRepository->findById(Arr::get($productItem, 'id'));
            if (!$product) {
                continue;
            }
            $demandQty = Arr::get($productItem, 'quantity', 1);
//
//            if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES) {
//                $stockQty = $product->online_sales_qty;
//            } elseif (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES) {
//                $stockQty = $product->in_person_sales_qty;
//            } else {

            $stockQty = $product->quantity;

//            }

            if ($request->input('order_type') != Order::PRE_ORDER) {
                if ($request->input('order_id') && $request->input('order_id') > 0) {
                    $getOrderProdQty = OrderProduct::where('order_id', $request->input('order_id'))->where('product_id', $product->id)->value('qty');
                    $stockQty += $getOrderProdQty;
                }
                if ($stockQty < $demandQty) {
                    return $response->setCode(406)->setError()->setMessage($product->sku . ' is not available in this Qty!');
                }
            }

            $curOrderProdIds[] = $product->id;

            if ($request->input('order_id') && $request->input('order_id') > 0) {

                $order = Order::where('id', $request->input('order_id'))->first();

                $getParentProdId = ProductVariation::where('product_id', $product->id)->value('configurable_product_id');
                $getOrderProd = OrderProduct::where('order_id', $request->input('order_id'))->where('product_id', $product->id)->first();
                if ($getOrderProd && $demandQty != $getOrderProd->qty) {
                    $this->orderHistoryRepository->createOrUpdate([
                        'action'      => 'order_product_qty_changed',
                        'description' => 'Order product ' . $getOrderProd->product_name . ' qty changed from ' . $getOrderProd->qty . ' to ' . $demandQty . ' by %user_name%.',
                        'order_id'    => $request->input('order_id'),
                        'user_id'     => Auth::user()->getKey(),
                    ], []);

                    if ($request->input('order_type') != Order::PRE_ORDER && $order->order_type == Order::NORMAL) {
                        if ($demandQty > $getOrderProd->qty) {
                            $diff = $demandQty - $getOrderProd->qty;
                            $logParam = [
                                'parent_product_id' => $getParentProdId,
                                'product_id'        => $product->id,
                                'sku'               => $product->sku,
                                'quantity'          => $diff,
                                'new_stock'         => $product->quantity - $diff,
                                'old_stock'         => $product->quantity,
                                'order_id'          => $request->input('order_id'),
                                'created_by'        => Auth::user()->id,
                                'reference'         => InventoryHistory::PROD_ORDER_QTY_DEDUCT
                            ];
                            log_product_history($logParam);
                        } elseif ($demandQty < $getOrderProd->qty) {
                            $diff = $getOrderProd->qty - $demandQty;
                            $logParam = [
                                'parent_product_id' => $getParentProdId,
                                'product_id'        => $product->id,
                                'sku'               => $product->sku,
                                'quantity'          => $diff,
                                'new_stock'         => $product->quantity + $diff,
                                'old_stock'         => $product->quantity,
                                'order_id'          => $request->input('order_id'),
                                'created_by'        => Auth::user()->id,
                                'reference'         => InventoryHistory::PROD_ORDER_QTY_ADD
                            ];
                            log_product_history($logParam);
                        }
                    } elseif ($request->input('order_type') != Order::PRE_ORDER && $order->order_type == Order::PRE_ORDER) {
                        $logParam = [
                            'parent_product_id' => $getParentProdId,
                            'product_id'        => $product->id,
                            'sku'               => $product->sku,
                            'quantity'          => $demandQty,
                            'new_stock'         => $product->quantity - $demandQty,
                            'old_stock'         => $product->quantity,
                            'order_id'          => $order->id,
                            'created_by'        => Auth::user()->id,
                            'reference'         => InventoryHistory::PROD_ORDER_QTY_DEDUCT
                        ];
                        log_product_history($logParam);
                    }

                } elseif (!$getOrderProd) {
                    if ($request->input('order_type') != Order::PRE_ORDER) {
                        $logParam = [
                            'parent_product_id' => $getParentProdId,
                            'product_id'        => $product->id,
                            'sku'               => $product->sku,
                            'quantity'          => $demandQty,
                            'new_stock'         => $product->quantity - $demandQty,
                            'old_stock'         => $product->quantity,
                            'order_id'          => $request->input('order_id'),
                            'created_by'        => Auth::user()->id,
                            'reference'         => InventoryHistory::PROD_ORDER_QTY_DEDUCT
                        ];
                        log_product_history($logParam);
                    }
                }

                if ($getOrderProd && $getOrderProd->price != Arr::get($productItem, 'sale_price', 1)) {
                    $this->orderHistoryRepository->createOrUpdate([
                        'action'      => 'product_price_change_on_order',
                        'description' => $product->name . ' product price change in order from $' . $getOrderProd->price . ' to $' . Arr::get($productItem, 'sale_price', 1) . ' by %user_name%.',
                        'order_id'    => $request->input('order_id'),
                        'user_id'     => Auth::user()->getKey(),
                    ], []);
                }
            }

        }

        if ($request->input('order_id') && $request->input('order_id') > 0) {

            /*$this->orderHistoryRepository->createOrUpdate([
                'action'      => 'order_update',
                'description' => 'Order updated by %user_name%.',
                'order_id'    => $request->input('order_id'),
                'user_id'     => Auth::user()->getKey(),
            ], []);*/

            $condition = ['id' => $request->input('order_id')];
            $meta_condition = ['order_id' => $request->input('order_id')];

            $order = Order::where('id', $request->input('order_id'))->first();

            if ($order->order_type != $request->input('order_type')) {
                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'order_type_changed',
                    'description' => 'Order type changes from ' . $order->order_type . ' to ' . $request->input('order_type') . ' by %user_name%.',
                    'order_id'    => $request->input('order_id'),
                    'user_id'     => Auth::user()->getKey(),
                ], []);

                if ($request->input('order_type') == Order::PRE_ORDER) {
                    $order_products = OrderProduct::where('order_id', $request->input('order_id'))->get();
                    foreach ($order_products as $order_product) {
                        $getParentProdId = ProductVariation::where('product_id', $order_product->product_id)->value('configurable_product_id');
                        $logParam = [
                            'parent_product_id' => $getParentProdId,
                            'product_id'        => $order_product->product_id,
                            'sku'               => $order_product->product->sku,
                            'quantity'          => $order_product->qty,
                            'new_stock'         => $order_product->product->quantity + $order_product->qty,
                            'old_stock'         => $order_product->product->quantity,
                            'order_id'          => $request->input('order_id'),
                            'created_by'        => Auth::user()->id,
                            'reference'         => InventoryHistory::PROD_ORDER_QTY_ADD
                        ];
                        log_product_history($logParam);
                    }
                }
            }

            if ($order->order_type != Order::PRE_ORDER) {
                $order_products = OrderProduct::where('order_id', $request->input('order_id'))->get();
                foreach ($order_products as $order_product) {
                    $this->productRepository
                        ->getModel()
                        ->where('id', $order_product->product_id)
                        ->where('with_storehouse_management', 1)
                        ->increment('quantity', $order_product->qty);

                    updateOldSystemProdQty($order_product->product, $order_product->qty, $order->id, true);
                }
            }

            $prevOrderProdIds = OrderProduct::where('order_id', $request->input('order_id'))->pluck('product_id')->all();

            foreach ($prevOrderProdIds as $prevOrderProdId) {
                if (!in_array($prevOrderProdId, $curOrderProdIds)) {
                    $getOrderProd = OrderProduct::where('order_id', $request->input('order_id'))->where('product_id', $prevOrderProdId)->first();
                    $getParentProdId = ProductVariation::where('product_id', $getOrderProd->product_id)->value('configurable_product_id');
                    $logParam = [
                        'parent_product_id' => $getParentProdId,
                        'product_id'        => $getOrderProd->product_id,
                        'sku'               => $getOrderProd->product->sku,
                        'quantity'          => $getOrderProd->qty,
                        'new_stock'         => $getOrderProd->product->quantity,
                        'old_stock'         => $getOrderProd->product->quantity - $getOrderProd->qty,
                        'order_id'          => $request->input('order_id'),
                        'created_by'        => Auth::user()->id,
                        'reference'         => InventoryHistory::PROD_ORDER_QTY_ADD
                    ];
                    log_product_history($logParam);
                }
            }

            OrderProduct::where('order_id', $request->input('order_id'))->delete();

            if (!check_array_equal($curOrderProdIds, $prevOrderProdIds)) {
                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'product_change_on_order',
                    'description' => 'Product(s) change in order by %user_name%.',
                    'order_id'    => $order->id,
                    'user_id'     => Auth::user()->getKey(),
                ], []);
            }

            if ($order->discount_amount != $request->input('discount_amount')) {
                if ($request->input('discount_amount') > $order->discount_amount) {
                    $this->orderHistoryRepository->createOrUpdate([
                        'action'      => 'add_discount_on_order',
                        'description' => '$' . $request->input('discount_amount') . ' discount added on order by %user_name%.',
                        'order_id'    => $order->id,
                        'user_id'     => Auth::user()->getKey(),
                    ], []);
                } elseif ($request->input('discount_amount') < $order->discount_amount) {
                    $this->orderHistoryRepository->createOrUpdate([
                        'action'      => 'remove_discount_on_order',
                        'description' => '$' . $request->input('discount_amount') . ' discount removed from order by %user_name%.',
                        'order_id'    => $order->id,
                        'user_id'     => Auth::user()->getKey(),
                    ], []);
                }
            }

            if ($order->user_id != $request->input('customer_id')) {
                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'order_customer_changed',
                    'description' => 'Customer changed in order by %user_name%.',
                    'order_id'    => $request->input('order_id'),
                    'user_id'     => Auth::user()->getKey(),
                ], []);
            }

            if ($order->payment->payment_channel->label() != strtolower($request->input('payment_method'))) {
                if ($order->payment->payment_channel->label() == 'cash') {
                    Accountingsystem::where('order_id', $order->id)->delete();
                }
                if (strtolower($request->input('payment_method')) == 'cash') {
                    $accountData = [
                        'money'       => 'in',
                        'description' => 'IN: Order ID #' . $order->id . ' / In-Store Complete / Cash',
                        'amount'      => ($request->input('amount') + $request->input('shipping_amount') - $request->input('discount_amount')),
                        'order_id'    => $order->id,
                        'created_by'  => auth()->user()->id,
                        'updated_by'  => auth()->user()->id,
                    ];
                    Accountingsystem::create($accountData);
                }
            } elseif (strtolower($request->input('payment_method')) == 'cash') {
                Accountingsystem::where('order_id', $order->id)->delete();
                $accountData = [
                    'money'       => 'in',
                    'description' => 'IN: Order ID #' . $order->id . ' / In-Store Complete / Cash',
                    'amount'      => ($request->input('amount') + $request->input('shipping_amount') - $request->input('discount_amount')),
                    'order_id'    => $order->id,
                    'created_by'  => auth()->user()->id,
                    'updated_by'  => auth()->user()->id,
                ];
                Accountingsystem::create($accountData);
            }
        }

        $request->merge([
            'amount'               => $request->input('amount') + $request->input('shipping_amount') - $request->input('discount_amount'),
            'currency_id'          => get_application_currency_id(),
            'user_id'              => $request->input('customer_id') ?? 0,
            'shipping_method'      => $request->input('shipping_method', ShippingMethodEnum::DEFAULT),
            'shipping_option'      => $request->input('shipping_option'),
            'shipping_amount'      => $request->input('shipping_amount'),
            'tax_amount'           => session('tax_amount', 0),
            'sub_total'            => $request->input('amount'),
            'coupon_code'          => $request->input('coupon_code'),
            'discount_amount'      => $request->input('discount_amount'),
            'discount_description' => $request->input('discount_description'),
            'description'          => $request->input('note'),
            'is_confirmed'         => 1,
            //'status' => OrderStatusEnum::NEW_ORDER,
            'order_type'           => $request->input('order_type'),
            'notes'                => $request->input('customer_notes'),
            'order_card'           => $request->input('order_card'),
            'platform'             => isset($order->platform) ? $order->platform : 'back-office'
        ]);

        if (!$request->input('order_id', 0)) {
            $request['status'] = OrderStatusEnum::NEW_ORDER;
        }

        $order = $this->orderRepository->createOrUpdate($request->input(), $condition);

        if ($order) {

            if (!$request->input('order_id', 0)) {
                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'create_order_from_payment_page',
                    'description' => trans('plugins/ecommerce::order.create_order_from_payment_page'),
                    'order_id'    => $order->id,
                ], []);

                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'create_order',
                    'description' => trans('plugins/ecommerce::order.new_order',
                        ['order_id' => get_order_code($order->id)]),
                    'order_id'    => $order->id,
                ], []);

                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'confirm_order',
                    'description' => trans('plugins/ecommerce::order.order_was_verified_by'),
                    'order_id'    => $order->id,
                    'user_id'     => Auth::user()->getKey(),
                ], []);

                if (strtolower($request->input('payment_method')) == 'cash') {
                    $accountData = [
                        'money'       => 'in',
                        'description' => 'IN: Order ID #' . $order->id . ' / In-Store Complete / Cash',
                        'amount'      => $order->amount,
                        'order_id'    => $order->id,
                        'created_by'  => auth()->user()->id,
                        'updated_by'  => auth()->user()->id,
                    ];
                    Accountingsystem::create($accountData);
                }
            }

            if ($order->discount_amount > 0 && !$request->input('order_id', 0)) {
                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'add_discount_on_order',
                    'description' => '$' . $order->discount_amount . ' discount added on order by %user_name%.',
                    'order_id'    => $order->id,
                    'user_id'     => Auth::user()->getKey(),
                ], []);
            }

            $payment = $this->paymentRepository->createOrUpdate([
                'amount'          => $order->amount,
                'currency'        => get_application_currency()->title,
                'payment_channel' => $request->input('payment_method'), //$order->payment->payment_channel,
                'paypal_email'    => $request->input('paypal_email'),
                'status'          => $request->input('payment_status', PaymentStatusEnum::PENDING),
                'payment_type'    => 'confirm',
                'order_id'        => $order->id,
                'charge_id'       => Str::upper(Str::random(10)),
            ], $meta_condition);

            $order->payment_id = $payment->id;

            $order->editing_by = NULL;
            $order->editing_started_at = NULL;

            $order->is_finished = 1;
            $order->salesperson_id = $order->salesperson_id ? $order->salesperson_id : auth()->user()->id;

            $order->save();

            if ($order->user_id && $payment->paypal_email) {
                Customer::where('id', $order->user_id)->update(['paypal_email' => $payment->paypal_email]);
            }

            if ($request->input('payment_status') === PaymentStatusEnum::COMPLETED) {
                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'confirm_payment',
                    'description' => trans('plugins/ecommerce::order.payment_was_confirmed_by', [
                        'money' => format_price($order->amount, $order->currency_id),
                    ]),
                    'order_id'    => $order->id,
                    'user_id'     => Auth::user()->getKey(),
                ], []);
            }

            if ($request->input('customer_address.name')) {
                $meta_condition['type'] = 'shipping';
                $this->orderAddressRepository->createOrUpdate([
                    'customer_address_id' => $request->input('customer_address.id'),
                    'name'                => $request->input('customer_address.name'),
                    'phone'               => $request->input('customer_address.phone'),
                    'email'               => $request->input('customer_address.email'),
                    'state'               => $request->input('customer_address.state'),
                    'city'                => $request->input('customer_address.city'),
                    'zip_code'            => $request->input('customer_address.zip_code'),
                    'country'             => $request->input('customer_address.country'),
                    'address'             => $request->input('customer_address.address'),
                    'order_id'            => $order->id,
                    'type'                => 'shipping',
                ], $meta_condition);
            } elseif ($request->input('customer_id')) {
                $customer = $this->customerRepository->findById($request->input('customer_id'));
                $this->orderAddressRepository->createOrUpdate([
                    'name'     => $customer->name,
                    'phone'    => $customer->phone,
                    'email'    => $customer->email,
                    'order_id' => $order->id,
                ], $meta_condition);
            }

            if ($request->input('customer_billing_address.name')) {
                $meta_condition['type'] = 'billing';
                $this->orderAddressRepository->createOrUpdate([
                    'customer_address_id' => $request->input('customer_billing_address.id'),
                    'name'                => $request->input('customer_billing_address.name'),
                    'phone'               => $request->input('customer_billing_address.phone'),
                    'email'               => $request->input('customer_billing_address.email'),
                    'state'               => $request->input('customer_billing_address.state'),
                    'city'                => $request->input('customer_billing_address.city'),
                    'zip_code'            => $request->input('customer_billing_address.zip_code'),
                    'country'             => $request->input('customer_billing_address.country'),
                    'address'             => $request->input('customer_billing_address.address'),
                    'order_id'            => $order->id,
                    'type'                => 'billing',
                ], $meta_condition);
            }
            /*if ($request->input('billing_address')) {
                $address = $this->addressRepository->findById($request->input('billing_address'));
                $this->orderAddressRepository->createOrUpdate([
                    'customer_address_id' => $address->id,
                    'name' => $address->name,
                    'phone' => $address->phone,
                    'email' => $address->email,
                    'state' => $address->state,
                    'city' => $address->city,
                    'zip_code' => $address->zip_code,
                    'country' => $address->country,
                    'address' => $address->address,
                    'order_id' => $order->id,
                    'type' => 'billing',
                ], $meta_condition);
            }*/

            foreach ($request->input('products', []) as $productItem) {
                $product = $this->productRepository->findById(Arr::get($productItem, 'id'));
                if (!$product) {
                    continue;
                }

                $data = [
                    'order_id'      => $order->id,
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_sizes' => $product->sizes,
                    'qty'           => floor(Arr::get($productItem, 'quantity', 1)),
                    'weight'        => $product->weight,
                    'price'         => Arr::get($productItem, 'sale_price', 1), //$product->front_sale_price,
                    'tax_amount'    => 0,
                    'options'       => [],
                ];

                $this->orderProductRepository->create($data);

                if (!$request->input('order_id', 0)) {
                    if ($product->front_sale_price != $data['price']) {
                        $this->orderHistoryRepository->createOrUpdate([
                            'action'      => 'product_price_change_on_order',
                            'description' => $product->name . ' product price change in order from $' . $product->front_sale_price . ' to $' . $data['price'] . ' by %user_name%.',
                            'order_id'    => $order->id,
                            'user_id'     => Auth::user()->getKey(),
                        ], []);
                    }
                }

                $preQty = $product->quantity;

                if ($order->order_type == Order::NORMAL) {
                    $orderedQty = Arr::get($productItem, 'quantity', 1);
                    $this->productRepository
                        ->getModel()
                        ->where('id', $product->id)
                        ->where('with_storehouse_management', 1)
                        ->where('quantity', '>', 0)
                        ->decrement('quantity', $orderedQty);

                    updateOldSystemProdQty($product, $orderedQty, $order->id);

                    if (!$request->input('order_id', 0)) {
                        $getParentProdId = ProductVariation::where('product_id', $product->id)->value('configurable_product_id');
                        $logParam = [
                            'parent_product_id' => $getParentProdId,
                            'product_id'        => $product->id,
                            'sku'               => $product->sku,
                            'quantity'          => $data['qty'],
                            'new_stock'         => $product->quantity - $data['qty'],
                            'old_stock'         => $product->quantity,
                            'order_id'          => $order->id,
                            'created_by'        => Auth::user()->id,
                            'reference'         => InventoryHistory::PROD_ORDER_QTY_DEDUCT
                        ];
                        log_product_history($logParam);
                    }

                    /*if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES || @auth()->user()->roles[0]->slug == Role::ADMIN) {
                        $this->productRepository
                            ->getModel()
                            ->where('id', $product->id)
                            ->where('with_storehouse_management', 1)
                            ->where('online_sales_qty', '>', 0)
                            ->decrement('online_sales_qty', Arr::get($productItem, 'quantity', 1));
                    }
                    if (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES || @auth()->user()->roles[0]->slug == Role::ADMIN) {
                        $this->productRepository
                            ->getModel()
                            ->where('id', $product->id)
                            ->where('with_storehouse_management', 1)
                            ->where('in_person_sales_qty', '>', 0)
                            ->decrement('in_person_sales_qty', Arr::get($productItem, 'quantity', 1));
                    }*/

                    $product = $this->productRepository->findById(Arr::get($productItem, 'id'));
                    set_product_oos_date($order->id, $product, Arr::get($productItem, 'quantity', 1), $preQty);
                }

                if ($order->order_type == Order::PRE_ORDER) {
                    $pre_order_max_qty = get_ecommerce_setting('pre_order_max_qty');
                    $preOrderQty = OrderProduct::join('ec_orders', 'ec_orders.id', 'ec_order_product.order_id')
                        ->where('product_id', $product->id)
                        ->where('order_type', Order::PRE_ORDER)
                        ->whereNotIn('status', [OrderStatusEnum::CANCELED, OrderStatusEnum::PENDING])
                        ->sum('qty');

                    if ($preOrderQty >= $pre_order_max_qty) {
                        generate_notification('pre_order_max_qty', $product);
                    }
                }

            }

            $applyPromotion = true;
            if ($order->coupon_code) {
                $checkCoupon = Discount::where('code', $order->coupon_code)->value('can_use_with_promotion');
                if (!$checkCoupon) {
                    $applyPromotion = false;
                }
            }

            $promotions = $this->discountRepository->getAvailablePromotions();
            foreach ($promotions as $promotion) {
                if ($promotion->target == 'category') {
                    $applyPromotion = false;
                }
            }

            if ($applyPromotion) {
                $this->promotion_service->applyPromotionIfAvailable($order->id);
            }

            //Mail::to($order->user->email)->send(new OrderCreate($order));
        }

        return $response
            ->setData($order)
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        Assets::addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/libraries/jquery.textarea_autosize.js',
                'vendor/core/plugins/ecommerce/js/order.js',
            ])
            ->addScripts(['blockui', 'input-mask']);

        $order = $this->orderRepository
            ->getModel()
            ->where('id', $id)
            ->with(['products', 'user'])
            ->firstOrFail();

        page_title()->setTitle(trans('plugins/ecommerce::order.edit_order', ['code' => get_order_code($id)]));

        $weight = 0;
        foreach ($order->products as $product) {
            if ($product && $product->weight) {
                $weight += $product->weight;
            }
        }

        $cards = [
            '0' => 'Add New Card'
        ];
        $defaultStore = get_primary_store_locator();
        $salesRep = get_salesperson();

        if ($order->user->card->count() > 0) {
            $omniId = $order->user->card()->whereNotNull('customer_omni_id')->get();
            foreach ($omniId as $item) {
                if ($item->customer_omni_id) {
                    $url = (env("OMNI_URL") . "customer/" . $item->customer_omni_id . "/payment-method");
                    list($card, $info) = omni_api($url);
                    $cards = collect(json_decode($card))->pluck('nickname', 'id')->push('Add New Card');
                }
            }
        }

//        if (!$order->user->card->isEmpty()) {
//            $omniId = $order->user->card()->whereNotNull('customer_omni_id')->value('customer_omni_id');
//            if ($omniId) {
//                $url = (env("OMNI_URL") . "customer/" . $omniId . "/payment-method");
//                list($card, $info) = omni_api($url);
//                $cards = collect(json_decode($card))->pluck('nickname', 'id')->push('Add New Card');
//            }
//        }

        if (isset($_GET['debug'])) {
            dd($order, $cards, $order->payment);
        }

        $split_payments = [];
        $paidAmount = 0;
        if ($order->split_payment) {
            $split_payments = $order->split_payments->pluck('amount', 'payment_type')->all();
            $paidAmount = $order->split_payments->where('status', 'paid')->sum('amount');
        }

        return view('plugins/ecommerce::orders.edit', compact('order', 'weight', 'defaultStore', 'cards', 'salesRep', 'split_payments', 'paidAmount'));
    }


    /**
     * @param int $id
     * @param UpdateOrderRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, UpdateOrderRequest $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->createOrUpdate($request->input(), ['id' => $id]);

        event(new UpdatedContentEvent(ORDER_MODULE_SCREEN_NAME, $request, $order));

        return $response
            ->setPreviousUrl(route('orders.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy($id, Request $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($id);

        try {
            $this->orderRepository->deleteBy(['id' => $id]);
            event(new DeletedContentEvent(ORDER_MODULE_SCREEN_NAME, $request, $order));
            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $order = $this->orderRepository->findOrFail($id);
            $this->orderRepository->delete($order);
            event(new DeletedContentEvent(ORDER_MODULE_SCREEN_NAME, $request, $order));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param int $orderId
     * @return BinaryFileResponse
     */
    public function getGenerateInvoice($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);
        $invoice = OrderHelper::generateInvoice($order);

        return response()->download($invoice)->deleteFileAfterSend();
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postConfirm(Request $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($request->input('order_id'));
        $order->is_confirmed = 1;
        if ($order->status == OrderStatusEnum::PENDING) {
            $order->status = OrderStatusEnum::PROCESSING;
        }

        $this->orderRepository->createOrUpdate($order);

        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'confirm_order',
            'description' => trans('plugins/ecommerce::order.order_was_verified_by'),
            'order_id'    => $order->id,
            'user_id'     => Auth::user()->getKey(),
        ]);

        $payment = $this->paymentRepository->getFirstBy(['order_id' => $order->id]);

        if ($payment) {
            $payment->user_id = Auth::user()->getKey();
            $payment->save();
        }

        $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
        if ($mailer->templateEnabled('order_confirm')) {
            OrderHelper::setEmailVariables($order);
            $mailer->sendUsingTemplate(
                'order_confirm',
                $order->user->email ? $order->user->email : $order->address->email
            );
        }

        return $response->setMessage(trans('plugins/ecommerce::order.confirm_order_success'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postResendOrderConfirmationEmail($id, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($id);
        $result = OrderHelper::sendOrderConfirmationEmail($order);

        if (!$result) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/ecommerce::order.error_when_sending_email'));
        }

        return $response->setMessage(trans('plugins/ecommerce::order.sent_confirmation_email_success'));
    }

    /**
     * @param int $orderId
     * @param HandleShippingFeeService $shippingFeeService
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|Factory|View
     * @throws Throwable
     */
    public function getShipmentForm(
        $orderId,
        HandleShippingFeeService $shippingFeeService,
        Request $request,
        BaseHttpResponse $response
    )
    {
        $order = $this->orderRepository->findOrFail($orderId);

        $weight = 0;
        if ($request->has('weight')) {
            $weight = $request->input('weight');
        } else {
            foreach ($order->products as $product) {
                if ($product && $product->weight) {
                    $weight += $product->weight;
                }
            }
        }

        $weight = $weight > 0.1 ? $weight : 0.1;

        $shippingData = [
            'address'     => $order->address->address,
            'country'     => $order->address->country,
            'state'       => $order->address->state,
            'city'        => $order->address->city,
            'weight'      => $weight,
            'order_total' => $order->amount,
        ];

        $shipping = $shippingFeeService->execute($shippingData);

        $storeLocators = $this->storeLocatorRepository->allBy(['is_shipping_location' => true]);

        if ($request->has('view')) {
            return view('plugins/ecommerce::orders.shipment-form',
                compact('order', 'weight', 'shipping', 'storeLocators'));
        }

        return $response->setData(view('plugins/ecommerce::orders.shipment-form',
            compact('order', 'weight', 'shipping', 'storeLocators'))->render());
    }

    /**
     * @param int $id
     * @param CreateShipmentRequest $request
     * @param BaseHttpResponse $response
     * @param ShipmentHistoryInterface $shipmentHistoryRepository
     * @return BaseHttpResponse
     * @throws FileNotFoundException
     * @throws Throwable
     */
    public function postCreateShipment(
        $id,
        CreateShipmentRequest $request,
        BaseHttpResponse $response,
        ShipmentHistoryInterface $shipmentHistoryRepository
    )
    {
        $order = $this->orderRepository->findOrFail($id);
        $order->order_completion_date = Carbon::now();
        $order->save();

        $result = $response;
        $products = [];
        $weight = 0;
        foreach ($order->products as $orderProduct) {
            $products[] = [
                'name'     => $orderProduct->product_name,
                'weight'   => $orderProduct->weight ?? 0.1,
                'quantity' => $orderProduct->qty,
            ];
            $weight += $orderProduct->weight ?? 0.1;
        }

        $weight = $weight > 0.1 ? $weight : 0.1;

        $shipment = [
            'order_id'   => $order->id,
            'user_id'    => Auth::user()->getKey(),
            'weight'     => $weight,
            'note'       => $request->input('note'),
            'cod_amount' => $request->input('cod_amount') ?? ($order->payment->status !== PaymentStatusEnum::COMPLETED ? $order->amount : 0),
            'cod_status' => 'pending',
            'type'       => $request->input('method'),
            // 'status'     => ShippingStatusEnum::DELIVERING,
            'status'     => ShippingStatusEnum::PICKING,
            'price'      => $order->shipping_amount,
            'store_id'   => $request->input('store_id'),
        ];

        $store = $this->storeLocatorRepository->findById($request->input('store_id'));

        if (!$store) {
            $defaultStore = $this->storeLocatorRepository->getFirstBy(['is_primary' => true]);
            $shipment['store_id'] = $defaultStore ?? ($defaultStore ? $defaultStore->id : null);
        }

        switch ($request->input('method')) {
            default:
                $result = $result->setMessage(trans('plugins/ecommerce::order.order_was_sent_to_shipping_team'));
                break;
        }

        if (!$result->isError()) {
            $this->orderRepository->createOrUpdate([
                'status'          => OrderStatusEnum::DELIVERING,
                'shipping_method' => $request->input('method'),
                'shipping_option' => $request->input('option'),
            ], compact('id'));

            $shipment = $this->shipmentRepository->createOrUpdate($shipment);

            $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
            if ($mailer->templateEnabled('customer_delivery_order')) {
                OrderHelper::setEmailVariables($order);
                $mailer->sendUsingTemplate(
                    'customer_delivery_order',
                    $order->user->email ? $order->user->email : $order->address->email
                );
            }

            $this->orderHistoryRepository->createOrUpdate([
                'action'      => 'create_shipment',
                'description' => $result->getMessage() . ' ' . trans('plugins/ecommerce::order.by_username'),
                'order_id'    => $id,
                'user_id'     => Auth::user()->getKey(),
            ]);

            $shipmentHistoryRepository->createOrUpdate([
                'action'      => 'create_from_order',
                'description' => trans('plugins/ecommerce::order.shipping_was_created_from'),
                'shipment_id' => $shipment->id,
                'order_id'    => $id,
                'user_id'     => Auth::user()->getKey(),
            ]);
        }

        return $result;
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postCancelShipment($id, BaseHttpResponse $response)
    {
        $shipment = $this->shipmentRepository->createOrUpdate(['status' => ShippingStatusEnum::CANCELED],
            compact('id'));

        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'cancel_shipment',
            'description' => trans('plugins/ecommerce::order.shipping_was_canceled_by'),
            'order_id'    => $shipment->order_id,
            'user_id'     => Auth::user()->getKey(),
        ]);

        return $response
            ->setData([
                'status'      => ShippingStatusEnum::CANCELED,
                'status_text' => ShippingStatusEnum::CANCELED()->label(),
            ])
            ->setMessage(trans('plugins/ecommerce::order.shipping_was_canceled_success'));
    }

    /**
     * @param int $id
     * @param AddressRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postUpdateShippingAddress($id, AddressRequest $request, BaseHttpResponse $response)
    {
        $address = $this->orderAddressRepository->createOrUpdate($request->input(), compact('id'));

        if (!$address) {
            abort(404);
        }

        return $response
            ->setData([
                'line'   => view('plugins/ecommerce::orders.shipping-address.line', compact('address'))->render(),
                'detail' => view('plugins/ecommerce::orders.shipping-address.detail', compact('address'))->render(),
            ])
            ->setMessage(trans('plugins/ecommerce::order.update_shipping_address_success'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postCancelOrder($id, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($id);

        if ($order->status != OrderStatusEnum::CANCELED) {
            $this->orderRepository->createOrUpdate(['status' => OrderStatusEnum::CANCELED, 'is_confirmed' => true], compact('id'));

            $this->orderHistoryRepository->createOrUpdate([
                'action'      => 'cancel_order',
                'description' => trans('plugins/ecommerce::order.order_was_canceled_by'),
                'order_id'    => $order->id,
                'user_id'     => Auth::user()->getKey(),
            ]);

            if ($order->order_type != Order::PRE_ORDER) {
                $order_products = OrderProduct::where('order_id', $order->id)->get();
                foreach ($order_products as $order_product) {
                    $getParentProdId = ProductVariation::where('product_id', $order_product->product_id)->value('configurable_product_id');
                    $logParam = [
                        'parent_product_id' => $getParentProdId,
                        'product_id'        => $order_product->product_id,
                        'sku'               => $order_product->product->sku,
                        'quantity'          => $order_product->qty,
                        'new_stock'         => $order_product->product->quantity + $order_product->qty,
                        'old_stock'         => $order_product->product->quantity,
                        'order_id'          => $order->id,
                        'created_by'        => Auth::user()->id,
                        'reference'         => InventoryHistory::PROD_ORDER_QTY_ADD
                    ];
                    log_product_history($logParam);

                    $this->productRepository
                        ->getModel()
                        ->where('id', $order_product->product_id)
                        ->where('with_storehouse_management', 1)
                        ->increment('quantity', $order_product->qty);
                }
            }

            $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
            if ($mailer->templateEnabled('customer_cancel_order')) {
                OrderHelper::setEmailVariables($order);
                $mailer->sendUsingTemplate(
                    'customer_cancel_order',
                    $order->user->email ? $order->user->email : $order->address->email
                );
            }
        }

        return $response->setMessage(trans('plugins/ecommerce::order.customer.messages.cancel_success'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws FileNotFoundException
     * @throws Throwable
     */
    public function postConfirmPayment($id, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($id, ['payment']);

        if ($order->status === OrderStatusEnum::PENDING) {
            $order->status = OrderStatusEnum::PROCESSING;
        }

        $this->orderRepository->createOrUpdate($order);

        $payment = $order->payment;

        $payment->status = PaymentStatusEnum::COMPLETED;

        $this->paymentRepository->createOrUpdate($payment);

        $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
        if ($mailer->templateEnabled('order_confirm_payment')) {
            OrderHelper::setEmailVariables($order);
            $mailer->sendUsingTemplate(
                'order_confirm_payment',
                $order->user->email ? $order->user->email : $order->address->email
            );
        }

        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'confirm_payment',
            'description' => trans('plugins/ecommerce::order.payment_was_confirmed_by', [
                'money' => format_price($order->amount),
            ]),
            'order_id'    => $order->id,
            'user_id'     => Auth::user()->getKey(),
        ]);

        return $response->setMessage(trans('plugins/ecommerce::order.confirm_payment_success'));
    }

    /**
     * @param int $id
     * @param RefundRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postRefund($id, RefundRequest $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($id);
        $this->orderRepository->createOrUpdate(['status' => OrderStatusEnum::REFUND],
            compact('id'));

        if ($request->input('refund_amount') > ($order->payment->amount - $order->payment->refunded_amount)) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/ecommerce::order.refund_amount_invalid', [
                    'price' => format_price($order->payment->amount - $order->payment->refunded_amount,
                        get_application_currency()),
                ]));
        }

        $hasError = false;
        foreach ($request->input('products', []) as $productId => $quantity) {
            $orderProduct = $this->orderProductRepository->getFirstBy([
                'product_id' => $productId,
                'order_id'   => $id,
            ]);
            if ($quantity > ($orderProduct->qty - $orderProduct->restock_quantity)) {
                $hasError = true;
                $response
                    ->setError()
                    ->setMessage(trans('plugins/ecommerce::order.number_of_products_invalid'));
                break;
            }
        }

        if ($hasError) {
            return $response;
        }

        $payment = $order->payment;
        if (!$payment) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/ecommerce::order.cannot_found_payment_for_this_order'));
        }

        $payment->refunded_amount += $request->input('refund_amount');
        if ($payment->refunded_amount == $payment->amount) {
            $payment->status = PaymentStatusEnum::REFUNDED;
        }
        $payment->refund_note = $request->input('refund_note');
        $this->paymentRepository->createOrUpdate($payment);

        foreach ($request->input('products', []) as $productId => $quantity) {
            $product = $this->productRepository->findById($productId);
            if ($product && $product->with_storehouse_management) {
                $product->quantity += $quantity;
                $this->productRepository->createOrUpdate($product);
            }

            $orderProduct = $this->orderProductRepository->getFirstBy([
                'product_id' => $productId,
                'order_id'   => $id,
            ]);
            if ($orderProduct) {
                $orderProduct->restock_quantity += $quantity;
                $this->orderProductRepository->createOrUpdate($orderProduct);
            }
        }

        if ($request->input('refund_amount', 0) > 0) {
            $this->orderHistoryRepository->createOrUpdate([
                'action'      => 'refund',
                'description' => trans('plugins/ecommerce::order.refund_success_with_price', [
                    'price' => format_price($request->input('refund_amount')),
                ]),
                'order_id'    => $order->id,
                'user_id'     => Auth::user()->getKey(),
                'extras'      => json_encode([
                    'amount' => $request->input('refund_amount'),
                    'method' => $payment->payment_channel ?? PaymentMethodEnum::COD,
                ]),
            ]);
        }

        return $response->setMessage(trans('plugins/ecommerce::order.refund_success'));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param HandleShippingFeeService $shippingFeeService
     * @return BaseHttpResponse
     */
    public function getAvailableShippingMethods(
        Request $request,
        BaseHttpResponse $response,
        HandleShippingFeeService $shippingFeeService
    )
    {
        $weight = 0;
        $orderAmount = 0;

        foreach ($request->input('products', []) as $productId) {
            $product = $this->productRepository->findById($productId);
            if ($product) {
                $weight += $product->weight;
                $orderAmount += $product->front_sale_price;
            }
        }

        $weight = $weight > 0.1 ? $weight : 0.1;

        $shippingData = [
            'address'     => $request->input('address'),
            'country'     => $request->input('country'),
            'state'       => $request->input('state'),
            'city'        => $request->input('city'),
            'weight'      => $weight,
            'order_total' => $orderAmount,
        ];

        $shipping = $shippingFeeService->execute($shippingData);

        $result = [];
        foreach ($shipping as $key => $shippingItem) {
            foreach ($shippingItem as $subKey => $subShippingItem) {
                $result[$key . ';' . $subKey . ';' . $subShippingItem['price']] = [
                    'name'  => $subShippingItem['name'],
                    'price' => format_price($subShippingItem['price'], null, true),
                ];
            }
        }

        return $response->setData($result);
    }

    /**
     * @param ApplyCouponRequest $request
     * @param HandleApplyCouponService $handleApplyCouponService
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postApplyCoupon(
        ApplyCouponRequest $request,
        HandleApplyCouponService $handleApplyCouponService,
        BaseHttpResponse $response
    )
    {
        $result = $handleApplyCouponService->applyCouponWhenCreatingOrderFromAdmin($request);

        if ($result['error']) {
            return $response
                ->setError()
                ->withInput()
                ->setMessage($result['message']);
        }

        return $response
            ->setData(Arr::get($result, 'data', []))
            ->setMessage(trans('plugins/ecommerce::order.applied_coupon_success',
                ['code' => $request->input('coupon_code')]));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse|Factory|View
     */
    public function editOrder($id, Request $request, BaseHttpResponse $response)
    {
        if (!$id) {
            return $response
                ->setError()
                ->setNextUrl(route('orders.index'))
                ->setMessage(trans('plugins/ecommerce::order.order_is_not_existed'));
        }

        // page_title()->setTitle(trans('plugins/ecommerce::order.reorder'));
        page_title()->setTitle('Edit Order');

        $order = $this->orderRepository->findById($id);

        event(new OrderEdit(auth()->user(), $order));

        if (!$order) {
            return $response
                ->setError()
                ->setNextUrl(route('orders.index'))
                ->setMessage(trans('plugins/ecommerce::order.order_is_not_existed'));
        }

        $editing_started_at = Carbon::parse($order->editing_started_at);
        if ($order->editing_by && $order->editing_by != auth()->user()->id && $editing_started_at->diffInSeconds(Carbon::now()) <= 60 * 5) {
            return $response
                ->setError()
                ->setNextUrl(route('orders.index'))
                ->setMessage('This is order already in editing by ' . auth()->user()->getFullName());
        }

        $productIds = $order->products->pluck('product_id')->all();

        $products = $this->productRepository
            ->getModel()
            ->whereIn('id', $productIds)
            ->get();

        foreach ($products as &$availableProduct) {
            $availableProduct->image_url = RvMedia::getImageUrl(Arr::first($availableProduct->images) ?? null, null, false, RvMedia::getDefaultImage());
            $availableProduct->price = $availableProduct->front_sale_price;
            $availableProduct->product_name = $availableProduct->name;
            $availableProduct->product_link = route('products.edit', $availableProduct->id);
            $availableProduct->select_qty = 1;
            $availableProduct->product_id = $availableProduct->id;
            $orderProduct = $order->products->where('product_id', $availableProduct->id)->first();
            if ($orderProduct) {
                $availableProduct->select_qty = $orderProduct->qty;
                $availableProduct->price = $orderProduct->price;
            }
            foreach ($availableProduct->variations as &$variation) {
                $variation->per_piece_price = $variation->price = $variation->product->front_sale_price;
                $variation->packQty = 0;
                $variation->packSizes = '';
                if ($variation->product->sku && !str_contains($variation->product->sku, 'single')) {
                    $variation->packQty = $availableProduct->prod_pieces ? $availableProduct->prod_pieces : packProdQtyCalculate($variation->product->category_id);
                    $variation->packSizes = packProdSizes($variation->product->category_id);
                    if ($variation->packQty) {
                        $variation->per_piece_price = round($variation->price / $variation->packQty, 2);
                    }
                }
                foreach ($variation->variationItems as &$variationItem) {
                    $variationItem->attribute_title = $variationItem->attribute->title;
                }
            }
        }

        $customer = null;
        $customerAddresses = [];
        $customerBillingAddresses = [];
        $customerOrderNumbers = 0;
        if ($order->user_id) {
            $customer = $this->customerRepository->findById($order->user_id);
            if ($customer) {
                $customerOrderNumbers = $customer->orders()->count();
                $customer->avatar = (string)$customer->avatar_url;
                $customerAddresses = $customer->addresses->toArray();
                $customerBillingAddresses = $customer->billing_addresses->toArray();
            }
        }
        $customerAddress = $order->address;
        $customerBillingAddress = $order->billingAddress;

        Assets::addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/libraries/jquery.textarea_autosize.js',
                'vendor/core/plugins/ecommerce/js/order-create.js',
            ])
            ->addScripts(['blockui', 'input-mask']);

        // $order->editing_by = auth()->user()->id;
        // $order->editing_started_at = Carbon::now();
        $order->save();


        $cards = [
            '0' => 'Add New Card'
        ];
        if ($order->user->card->count() > 0) {
            $omniId = $order->user->card()->whereNotNull('customer_omni_id')->get();
            foreach ($omniId as $item) {
                if ($item->customer_omni_id) {
                    $url = (env("OMNI_URL") . "customer/" . $item->customer_omni_id . "/payment-method");
                    list($card, $info) = omni_api($url);
                    $cards = collect(json_decode($card))->pluck('nickname', 'id')->push('Add New Card');
                }
            }
        }

//        if (!$order->user->card->isEmpty()) {
//            $omniId = $order->user->card()->whereNotNull('customer_omni_id')->value('customer_omni_id');
//            if ($omniId) {
//                $url = (env("OMNI_URL") . "customer/" . $omniId . "/payment-method");
//                list($card, $info) = omni_api($url);
//                $cards = collect(json_decode($card))->pluck('nickname', 'id')->push('Add New Card');
//            }
//        }


        return view('plugins/ecommerce::orders.reorder', compact(
            'order',
            'products',
            'productIds',
            'customer',
            'customerAddresses',
            'customerBillingAddresses',
            'customerAddress',
            'customerBillingAddress',
            'customerOrderNumbers',
            'cards'
        ));
    }

    public function getReorder(Request $request, BaseHttpResponse $response)
    {
        if (!$request->input('order_id')) {
            return $response
                ->setError()
                ->setNextUrl(route('orders.index'))
                ->setMessage(trans('plugins/ecommerce::order.order_is_not_existed'));
        }

        page_title()->setTitle(trans('plugins/ecommerce::order.reorder'));

        $order = $this->orderRepository->findById($request->input('order_id'));

        if (!$order) {
            return $response
                ->setError()
                ->setNextUrl(route('orders.index'))
                ->setMessage(trans('plugins/ecommerce::order.order_is_not_existed'));
        }

        $productIds = $order->products->pluck('product_id')->all();

        $products = $this->productRepository
            ->getModel()
            ->whereIn('id', $productIds)
            ->get();

        foreach ($products as &$availableProduct) {
            $availableProduct->image_url = RvMedia::getImageUrl(Arr::first($availableProduct->images) ?? null, null,
                false, RvMedia::getDefaultImage());
            $availableProduct->price = $availableProduct->front_sale_price;
            $availableProduct->product_name = $availableProduct->name;
            $availableProduct->product_link = route('products.edit', $availableProduct->id);
            $availableProduct->select_qty = 1;
            $availableProduct->product_id = $availableProduct->id;
            $orderProduct = $order->products->where('product_id', $availableProduct->id)->first();
            if ($orderProduct) {
                $availableProduct->select_qty = $orderProduct->qty;
            }
            foreach ($availableProduct->variations as &$variation) {
                $variation->price = $variation->product->front_sale_price;
                foreach ($variation->variationItems as &$variationItem) {
                    $variationItem->attribute_title = $variationItem->attribute->title;
                }
            }
        }

        $customer = null;
        $customerAddresses = [];
        $customerBillingAddresses = [];
        $customerOrderNumbers = 0;
        if ($order->user_id) {
            $customer = $this->customerRepository->findById($order->user_id);
            $customer->avatar = (string)$customer->avatar_url;

            if ($customer) {
                $customerOrderNumbers = $customer->orders()->count();
            }

            $customerAddresses = $customer->addresses->toArray();
        }
        $customerAddress = $order->address;

        Assets::addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/libraries/jquery.textarea_autosize.js',
                'vendor/core/plugins/ecommerce/js/order-create.js',
            ])
            ->addScripts(['blockui', 'input-mask']);

        return view('plugins/ecommerce::orders.reorder', compact(
            'order',
            'products',
            'productIds',
            'customer',
            'customerAddresses',
            'customerAddress',
            'customerBillingAddresses',
            'customerOrderNumbers'
        ));
    }

    /**
     * @param OrderIncompleteTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function getIncompleteList(OrderIncompleteTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/ecommerce::order.incomplete_order'));

        return $dataTable->renderTable();
    }

    /**
     * @param int $id
     * @return Factory|View
     */
    public function getViewIncompleteOrder($id)
    {
        page_title()->setTitle(trans('plugins/ecommerce::order.incomplete_order'));

        Assets::addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/libraries/jquery.textarea_autosize.js',
                'vendor/core/plugins/ecommerce/js/order-incomplete.js',
            ]);

        $order = $this->orderRepository
            ->getModel()
            ->where('id', $id)
            ->where('is_finished', 0)
            ->with(['products', 'user'])
            ->firstOrFail();

        return view('plugins/ecommerce::orders.view-incomplete-order', compact('order'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function postSendOrderRecoverEmail($id, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($id);
        try {
            $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
            if ($mailer->templateEnabled('order_recover')) {
                OrderHelper::setEmailVariables($order);

                $mailer->sendUsingTemplate(
                    'order_recover',
                    $order->user->email ? $order->user->email : $order->address->email
                );
            }
            return $response->setMessage(trans('plugins/ecommerce::order.sent_email_incomplete_order_success'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/ecommerce::order.error_when_sending_email'));
        }
    }

    public function import(Request $request)
    {
        $import = null;
        $import_errors = $request->import_errors;
        if ($request->import) {
            $importOrder = OrderImport::where('order_import_upload_id', $request->import)->pluck('order_id');
            $import = Order::whereIN('id', $importOrder)->get();
        }
        return view('plugins/ecommerce::order-import.create', compact('import', 'import_errors'));
    }

    public function importOrder(Request $request, BaseHttpResponse $response)
    {
        //TODO Refactor the code
        if ($request->hasfile('file')) {
            // $type = strtolower($request['file']->getClientOriginalExtension());
            // $image = str_replace(' ', '_', rand(1, 100) . '_' . substr(microtime(), 2, 7)) . '.' . $type;
            $move = $request->file('file')->move(public_path('storage/importorders'), $request->file('file')->getClientOriginalName());
            $order = Excel::toCollection(new OrderImportFile(), $move);
//            $filecheck = OrderImportUpload::where('file', $move)->first();
//            if ($filecheck != null) {
//                return $response
//                    ->setError()
//                    ->setMessage('File Already Exist');
//            }
            $upload = OrderImportUpload::create(['file' => $move]);

            $errors = [];
            $notFoundSkus = '';
            $orderProduct = 0;

            if ($request->market_place == Order::LASHOWROOM) {
                foreach ($order as $od) {
                    foreach ($od as $row) {

                        $newError = [];

                        if (!isset($row['po'])) {
                            return $response
                                ->setError()
                                ->setMessage('Wrong File Selected');
                        }

                        $customer = Customer::where(['phone' => $row['phone_number']])->first();
                        if ($customer == null) {
                            //creating Customer
                            $data['name'] = $row['business_contact_name'];
                            $data['email'] = str_replace(' ', '', $row['business_contact_name']) . '@lashowroomcustomer.com';
                            $data['phone'] = $row['phone_number'];
                            $data['password'] = bcrypt(rand(00000000, 99999999));
                            $data['status'] = BaseStatusEnum::DECLINED;
                            $customer = Customer::create($data);
                            $detail['customer_id'] = $customer['id'];
                            $detail['company'] = $row['business_company_name'];
                            $detail['type'] = Order::LASHOWROOM;

                            CustomerDetail::create($detail);

                            //creating address
                            $baddress['address'] = $row['billing_address'];
                            $baddress['city'] = $row['billing_city'];
                            $baddress['state'] = $row['billing_state'];
                            $baddress['zip_code'] = $row['billing_zip_code'];
                            $baddress['customer_id'] = $customer['id'];
                            $baddress['phone'] = $row['phone_number'];
                            $baddress['country'] = $row['billing_county'];
                            $baddress['name'] = $row['business_contact_name'];
                            $baddress['type'] = 'billing';

                            $billing = Address::create($baddress);

                            $saddress['address'] = $row['shipping_address'];
                            $saddress['city'] = $row['shipping_city'];
                            $saddress['state'] = $row['shipping_state'];
                            $saddress['zip_code'] = $row['shipping_zip_code'];
                            $saddress['country'] = $row['shipping_country'];
                            $saddress['customer_id'] = $customer['id'];
                            $saddress['phone'] = $row['phone_number'];
                            $saddress['name'] = $row['shipping_contact_name'];

                            $shipping = Address::create($saddress);
                        }

                        $orderQuantity = 0;
                        $checkProdQty = false;

                        //Finding Product For Order
                        $prodSKU = $row['style_no'];
//                        if (!str_contains($prodSKU, 'pack-all')) {
//                            $prodSKU .= '-pack-all';
//                        }
                        $product = Product::where(['sku' => $prodSKU, 'status' => BaseStatusEnum::ACTIVE])->where(['ptype' => 'R'])->latest()->first();
                        if ($product) {
                            //count pack quantity for product
                            //$pack = quantityCalculate($product['category_id']);
                            $pack = ($product->prod_pieces) ? $product->prod_pieces : quantityCalculate($product['category_id']);
                            if (!$pack) {
                                return $response
                                    ->setError()
                                    ->setMessage('Please add Pieces in Product ' . $row['style']);
                            }
                            $orderQuantity = $row['original_qty'] / $pack;

//                            if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES) {
//                                if ($orderQuantity <= $product->online_sales_qty) {
//                                    $checkProdQty = true;
//                                } elseif ($product->online_sales_qty > 0) {
//                                    $remQty = $orderQuantity - $product->online_sales_qty;
//                                    $orderQuantity = $product->online_sales_qty;
//                                    $errors[] = $row['style_no'] . ' product is short in ' . $remQty . ' quantity.';
//                                }
//                            } elseif (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES) {
//                                if ($orderQuantity <= $product->in_person_sales_qty) {
//                                    $checkProdQty = true;
//                                } elseif ($product->in_person_sales_qty > 0) {
//                                    $remQty = $orderQuantity - $product->in_person_sales_qty;
//                                    $orderQuantity = $product->in_person_sales_qty;
//                                    $errors[] = $row['style_no'] . ' product is short in ' . $remQty . ' quantity.';
//                                }
//                            } else {
                            if ($orderQuantity <= $product->quantity) {
                                $checkProdQty = true;
                            } elseif ($product->quantity > 0) {
                                $remQty = $orderQuantity - $product->quantity;
                                $orderQuantity = $product->quantity;
                                /*$errors[] = */
                                $newError[] = $row['style_no'] . ' product is short in ' . $remQty . ' quantity.';
                                $notFoundSkus .= $row['style_no'] . ', ';
                            }
//                            }

                        } else {
                            /*$errors[] = */
                            $newError[] = $row['style_no'] . ' product is not found.';
                            $notFoundSkus .= $row['style_no'] . ', ';
                        }

                        if (!$checkProdQty && $product) {
                            /*$errors[] = */
                            $newError[] = $row['style_no'] . ' product is out of stock.';
                            $notFoundSkus .= $row['style_no'] . ', ';
                        }

                        $orderPo = DB::table('ec_order_import')->where('po_number', $row['po'])->first();
                        if ($orderPo != null && $product && $checkProdQty) {
                            $detail['order_id'] = $orderPo->order_id;
                            $detail['qty'] = $orderQuantity;
                            $detail['price'] = intval(str_replace(['$', ','], '', $row['sub_total'])) / $orderQuantity;
                            $detail['product_id'] = $product->id;
                            $detail['product_name'] = $product->name;
                            $orderProduct = OrderProduct::create($detail);
                            //import record
                        } else /*if ($product)*/ {
                            $iorder['user_id'] = $customer->id;
                            $iorder['amount'] = str_replace(['$', ','], '', $row['original_amount']);;
                            $iorder['sub_total'] = str_replace(['$', ','], '', $row['sub_total']);;
                            $iorder['currency_id'] = 1;
                            $iorder['is_confirmed'] = 1;
                            $iorder['is_finished'] = 1;
                            $iorder['discount_amount'] = str_replace(['$', ','], '', $row['discount']);;
                            $iorder['shipping_amount'] = str_replace(['$', ','], '', $row['shipping_cost']);;
                            $iorder['tax_amount'] = 0;
                            $iorder['platform'] = 'online';
                            $iorder['salesperson_id'] = @auth()->user()->id;
                            $iorder['status'] = OrderStatusEnum::PROCESSING;
                            $iorder['order_type'] = Order::$IMPORT_ORDER_TYPES[$row['order_status']];
                            $importOrder = Order::create($iorder);
                            if ($importOrder && $product && $checkProdQty) {
                                $this->addOrderImportHistory($importOrder->id, Order::$MARKETPLACE[Order::LASHOWROOM]);
                                $detail['order_id'] = $importOrder->id;
                                $detail['qty'] = $orderQuantity;
                                $detail['price'] = intval(str_replace(['$', ','], '', $row['sub_total'])) / $orderQuantity;
                                $detail['product_id'] = $product->id;
                                $detail['product_name'] = $product->name;
                                $orderProduct = OrderProduct::create($detail);
                                if ($importOrder) {
                                    $orderInfo['order_id'] = $importOrder->id;
                                    $orderInfo['po_number'] = $row['po'];
                                    $orderInfo['order_date'] = $row['order_date'];
                                    $orderInfo['type'] = Order::LASHOWROOM;
                                    $orderInfo['order_import_upload_id'] = $upload->id;
                                    $orderInfo['not_found_skus'] = $notFoundSkus;
                                    OrderImport::create($orderInfo);
                                }
                            }
                        }

                        if (isset($importOrder->id) && count($errors)) {
                            $errors[$importOrder->id][] = implode('/', $newError);
                        } elseif (isset($orderPo->order_id) && count($errors)) {
                            $errors[$orderPo->order_id][] = implode('/', $newError);
                        }


                        if (isset($importOrder->id)) {
                            $this->updateOrderTotal($importOrder->id, false);
                        } elseif (isset($orderPo->order_id)) {
                            $this->updateOrderTotal($orderPo->order_id, false);
                        }


                        if ($product && $orderProduct && $orderProduct->order->order_type == Order::NORMAL) {

                            $this->productRepository
                                ->getModel()
                                ->where('id', $product->id)
                                ->where('with_storehouse_management', 1)
                                ->where('quantity', '>', 0)
                                ->decrement('quantity', $orderQuantity);
                            updateOldSystemProdQty($product, $orderQuantity, $orderPo->order_id);

                            /*if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES || @auth()->user()->roles[0]->slug == Role::ADMIN) {
                                $this->productRepository
                                    ->getModel()
                                    ->where('id', $product->id)
                                    ->where('with_storehouse_management', 1)
                                    ->where('online_sales_qty', '>', 0)
                                    ->decrement('online_sales_qty', $orderQuantity);
                            }
                            if (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES || @auth()->user()->roles[0]->slug == Role::ADMIN) {
                                $this->productRepository
                                    ->getModel()
                                    ->where('id', $product->id)
                                    ->where('with_storehouse_management', 1)
                                    ->where('in_person_sales_qty', '>', 0)
                                    ->decrement('in_person_sales_qty', $orderQuantity);
                            }*/

                            $productN = $this->productRepository->findById($product->id);
                            set_product_oos_date($orderProduct->order_id, $productN, $orderQuantity, $product->quantity);

                            $getParentProdId = ProductVariation::where('product_id', $product->id)->value('configurable_product_id');
                            $logParam = [
                                'parent_product_id' => $getParentProdId,
                                'product_id'        => $product->id,
                                'sku'               => $product->sku,
                                'quantity'          => $orderProduct->qty,
                                'new_stock'         => $product->quantity - $orderProduct->qty,
                                'old_stock'         => $product->quantity,
                                'order_id'          => $orderProduct->order_id,
                                'created_by'        => Auth::user()->id,
                                'reference'         => InventoryHistory::PROD_ORDER_QTY_DEDUCT
                            ];
                            log_product_history($logParam);

                        }

                        if ($orderProduct && $orderProduct->order->order_type == Order::PRE_ORDER) {
                            $pre_order_max_qty = get_ecommerce_setting('pre_order_max_qty');
                            $preOrderQty = OrderProduct::join('ec_orders', 'ec_orders.id', 'ec_order_product.order_id')
                                ->where('product_id', $product->id)
                                ->where('order_type', Order::PRE_ORDER)
                                ->whereNotIn('status', [OrderStatusEnum::CANCELED, OrderStatusEnum::PENDING])
                                ->sum('qty');

                            if ($preOrderQty >= $pre_order_max_qty) {
                                generate_notification('pre_order_max_qty', $product);
                            }
                        }

                    }
                }
            } elseif ($request->market_place == Order::ORANGESHINE) {
                foreach ($order as $od) {
                    foreach ($od as $row) {

                        $newError = [];

                        if (!isset($row['invoice'])) {
                            return $response
                                ->setError()
                                ->setMessage('Wrong File Selected');
                        }

                        if ($row['payment'] == 'PayPal') {
                            $customer = Customer::where(['phone' => $row['shipping_phone']])->first();
                        } else {
                            $customer = Customer::where(['phone' => $row['billing_phone']])->first();
                        }
                        if ($customer == null) {
                            //creating Customer
                            $data['name'] = $row['company'];
                            $data['email'] = str_replace(' ', '', $row['company']) . '@orangeshine.com';
                            $data['phone'] = ($row['payment'] == 'PayPal') ? $row['shipping_phone'] : $row['billing_phone'];
                            $data['password'] = bcrypt(rand(00000000, 99999999));
                            $data['status'] = BaseStatusEnum::DECLINED;
                            $customer = Customer::create($data);
                            $detail['customer_id'] = $customer['id'];
                            $detail['company'] = $row['company'];
                            $detail['type'] = Order::ORANGESHINE;

                            CustomerDetail::create($detail);
                            if ($row['payment'] != 'PayPal') {
                                $baddress['address'] = $row['billing_address'];
                                $baddress['city'] = $row['billing_city'];
                                $baddress['state'] = $row['billing_state'];
                                $baddress['zip_code'] = $row['billing_zip'];
                                $baddress['customer_id'] = $customer['id'];
                                $baddress['phone'] = $row['billing_phone'];
                                $baddress['country'] = $row['billing_country'];
                                $baddress['name'] = $row['company'];
                                $baddress['type'] = 'billing';
                                $billing = Address::create($baddress);
                            }
                            //creating address

                            $saddress['address'] = $row['shipping_address'];
                            $saddress['city'] = $row['shipping_city'];
                            $saddress['state'] = $row['shipping_state'];
                            $saddress['zip_code'] = $row['shipping_zip'];
                            $saddress['country'] = $row['shipping_country'];
                            $saddress['customer_id'] = $customer['id'];
                            $saddress['phone'] = $row['shipping_phone'];
                            $saddress['name'] = $row['shipping_company_name'];
                            $baddress['type'] = 'shipping';
                            $shipping = Address::create($saddress);
                        }

                        $orderQuantity = 0;
                        $checkProdQty = false;
                        //Finding Product For Order
                        $prodSKU = $row['style'];
//                        if (!str_contains($prodSKU, 'pack-all')) {
//                            $prodSKU .= '-pack-all';
//                        }

                        $product = Product::where(['sku' => $prodSKU, 'status' => BaseStatusEnum::ACTIVE])->where(['ptype' => 'R'])->latest()->first();
                        if ($product) {

                            //count pack quantity for product
//                            $pack = quantityCalculate($product['category_id']);
                            $pack = ($product->prod_pieces) ? $product->prod_pieces : quantityCalculate($product['category_id']);
                            if (!$pack) {
                                return $response
                                    ->setError()
                                    ->setMessage('Please add Peices in Product ' . $row['style']);
                            }
                            $orderQuantity = $row['total_qty'] / $pack;

//                            if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES) {
//                                if ($orderQuantity <= $product->online_sales_qty) {
//                                    $checkProdQty = true;
//                                } elseif ($product->online_sales_qty > 0) {
//                                    $remQty = $orderQuantity - $product->online_sales_qty;
//                                    $orderQuantity = $product->online_sales_qty;
//                                    $errors[] = $row['style'] . ' product is short in ' . $remQty . ' quantity.';
//                                }
//                            } elseif (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES) {
//                                if ($orderQuantity <= $product->in_person_sales_qty) {
//                                    $checkProdQty = true;
//                                } elseif ($product->in_person_sales_qty > 0) {
//                                    $remQty = $orderQuantity - $product->in_person_sales_qty;
//                                    $orderQuantity = $product->in_person_sales_qty;
//                                    $errors[] = $row['style'] . ' product is short in ' . $remQty . ' quantity.';
//                                }
//                            } else {
                            if ($orderQuantity <= $product->quantity) {
                                $checkProdQty = true;
                            } elseif ($product->quantity > 0) {
                                $remQty = $orderQuantity - $product->quantity;
                                $orderQuantity = $product->quantity;
                                /*$errors[] = */
                                $newError[] = $row['style'] . ' product is short in ' . $remQty . ' quantity.';
                                $notFoundSkus .= $row['style'] . ', ';
                            }
//                            }

                        } else {
                            /*$errors[] = */
                            $newError[] = $row['style'] . ' product is not found.';
                            $notFoundSkus .= $row['style'] . ', ';
                        }

                        if (!$checkProdQty && $product) {
                            /*$errors[] = */
                            $newError[] = $row['style'] . ' product is out of stock.';
                            $notFoundSkus .= $row['style'] . ', ';
                        }

                        $orderPo = DB::table('ec_order_import')->where('po_number', $row['invoice'])->first();
                        if ($orderPo != null && $product && $checkProdQty) {
                            $detail['order_id'] = $orderPo->order_id;
                            $detail['qty'] = $orderQuantity;
                            $detail['price'] = str_replace(['$', ','], '', $row['sub_total']) / $orderQuantity;
                            $detail['product_id'] = $product->id;
                            $detail['product_name'] = $product->name;
                            $orderProduct = OrderProduct::create($detail);
                            //import record
                        } else /*if ($product)*/ {
                            $iorder['user_id'] = $customer->id;
                            $iorder['amount'] = str_replace(['$', ','], '', $row['order_amt']);;
                            $iorder['currency_id'] = 1;
                            $iorder['is_confirmed'] = 1;
                            $iorder['is_finished'] = 1;
                            $iorder['discount_amount'] = 0;
                            $iorder['shipping_amount'] = 0;
                            $iorder['tax_amount'] = 0;
                            $iorder['platform'] = 'online';
                            $iorder['salesperson_id'] = @auth()->user()->id;
                            $iorder['status'] = OrderStatusEnum::PROCESSING;
                            $iorder['order_type'] = Order::$IMPORT_ORDER_TYPES[$row['status']];
                            $importOrder = Order::create($iorder);
                            if ($importOrder && $product && $checkProdQty) {
                                $this->addOrderImportHistory($importOrder->id, Order::$MARKETPLACE[Order::ORANGESHINE]);
                                $detail['order_id'] = $importOrder->id;
                                $detail['qty'] = $orderQuantity;
                                $detail['price'] = str_replace(['$', ','], '', $row['sub_total']) / $orderQuantity;
                                $detail['product_id'] = $product->id;
                                $detail['product_name'] = $product->name;
                                $orderProduct = OrderProduct::create($detail);
                                if ($importOrder) {
                                    $orderInfo['order_id'] = $importOrder->id;
                                    $orderInfo['po_number'] = $row['invoice'];
                                    $orderInfo['order_date'] = $row['order_date'];
                                    $orderInfo['type'] = Order::ORANGESHINE;
                                    $orderInfo['order_import_upload_id'] = $upload->id;
                                    $orderInfo['not_found_skus'] = $notFoundSkus;
                                    OrderImport::create($orderInfo);
                                }
                            }
                        }

                        if (isset($importOrder->id) && count($errors)) {
                            $errors[$importOrder->id][] = implode('/', $newError);
                        } elseif (isset($orderPo->order_id) && count($errors)) {
                            $errors[$orderPo->order_id][] = implode('/', $newError);
                        }


                        if (isset($importOrder->id)) {
                            $this->updateOrderTotal($importOrder->id, false);
                        } elseif (isset($orderPo->order_id)) {
                            $this->updateOrderTotal($orderPo->order_id, false);
                        }


                        if ($product && $orderProduct && $orderProduct->order->order_type == Order::NORMAL) {
                            $this->productRepository
                                ->getModel()
                                ->where('id', $product->id)
                                ->where('with_storehouse_management', 1)
                                ->where('quantity', '>', 0)
                                ->decrement('quantity', $orderQuantity);
                            updateOldSystemProdQty($product, $orderQuantity, $orderPo->order_id);

                            /*if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES || @auth()->user()->roles[0]->slug == Role::ADMIN) {
                                $this->productRepository
                                    ->getModel()
                                    ->where('id', $product->id)
                                    ->where('with_storehouse_management', 1)
                                    ->where('online_sales_qty', '>', 0)
                                    ->decrement('online_sales_qty', $orderQuantity);
                            }
                            if (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES || @auth()->user()->roles[0]->slug == Role::ADMIN) {
                                $this->productRepository
                                    ->getModel()
                                    ->where('id', $product->id)
                                    ->where('with_storehouse_management', 1)
                                    ->where('in_person_sales_qty', '>', 0)
                                    ->decrement('in_person_sales_qty', $orderQuantity);
                            }*/

                            $productN = $this->productRepository->findById($product->id);
                            set_product_oos_date($orderProduct->order_id, $productN, $orderQuantity, $product->quantity);

                            $getParentProdId = ProductVariation::where('product_id', $product->id)->value('configurable_product_id');
                            $logParam = [
                                'parent_product_id' => $getParentProdId,
                                'product_id'        => $product->id,
                                'sku'               => $product->sku,
                                'quantity'          => $orderProduct->qty,
                                'new_stock'         => $product->quantity - $orderProduct->qty,
                                'old_stock'         => $product->quantity,
                                'order_id'          => $orderProduct->order_id,
                                'created_by'        => Auth::user()->id,
                                'reference'         => InventoryHistory::PROD_ORDER_QTY_DEDUCT
                            ];
                            log_product_history($logParam);

                        }

                        if ($orderProduct && $orderProduct->order->order_type == Order::PRE_ORDER) {
                            $pre_order_max_qty = get_ecommerce_setting('pre_order_max_qty');
                            $preOrderQty = OrderProduct::join('ec_orders', 'ec_orders.id', 'ec_order_product.order_id')
                                ->where('product_id', $product->id)
                                ->where('order_type', Order::PRE_ORDER)
                                ->whereNotIn('status', [OrderStatusEnum::CANCELED, OrderStatusEnum::PENDING])
                                ->sum('qty');

                            if ($preOrderQty >= $pre_order_max_qty) {
                                generate_notification('pre_order_max_qty', $product);
                            }
                        }

                    }
                }
            } else {
                foreach ($order as $od) {
                    foreach ($od as $row) {
                        $newError = [];

                        if (!isset($row['ponumber'])) {
                            return $response
                                ->setError()
                                ->setMessage('Wrong File Selected');
                        }

                        $customer = Customer::where(['phone' => $row['phonenumber']])->first();
                        if ($customer == null) {
                            //creating Customer
                            $data['name'] = $row['companyname'];
                            $data['email'] = str_replace(' ', '', $row['companyname']) . '@fashiongo.com';
                            $data['phone'] = $row['phonenumber'];
                            $data['password'] = bcrypt(rand(00000000, 99999999));
                            $data['status'] = BaseStatusEnum::DECLINED;
                            $customer = Customer::create($data);
                            $detail['customer_id'] = $customer['id'];
                            $detail['company'] = $row['companyname'];
                            $detail['type'] = Order::FASHIONGO;

                            CustomerDetail::create($detail);

                            $baddress['address'] = $row['billingstreet'];
                            $baddress['city'] = $row['billingcity'];
                            $baddress['state'] = $row['billingstate'];
                            $baddress['zip_code'] = $row['billingzipcode'];
                            $baddress['customer_id'] = $customer['id'];
                            $baddress['phone'] = $row['phonenumber'];
                            $baddress['country'] = $row['billingcountry'];
                            $baddress['name'] = $row['companyname'];
                            $baddress['type'] = 'billing';
                            $billing = Address::create($baddress);

                            //creating address

                            $saddress['address'] = $row['shippingstreet'];
                            $saddress['city'] = $row['shippingcity'];
                            $saddress['state'] = $row['shippingstate'];
                            $saddress['zip_code'] = $row['shippingzipcode'];
                            $saddress['country'] = $row['shippingcountry'];
                            $saddress['customer_id'] = $customer['id'];
                            $saddress['phone'] = $row['phonenumber'];
                            $saddress['name'] = $row['companyname'];
                            $baddress['type'] = 'shipping';
                            $shipping = Address::create($saddress);

                        }

                        $orderQuantity = 0;
                        $checkProdQty = false;
                        //Finding Product For Order
                        $prodSKU = $row['styleno'];
//                        if (!str_contains($prodSKU, 'pack-all')) {
//                            $prodSKU .= '-pack-all';
//                        }
                        $product = Product::where(['sku' => $prodSKU, 'status' => BaseStatusEnum::ACTIVE])->where(['ptype' => 'R'])->latest()->first();

                        if ($product) {
                            //count pack quantity for product
//                            $pack = quantityCalculate($product['category_id']);
                            $pack = ($product->prod_pieces) ? $product->prod_pieces : quantityCalculate($product['category_id']);
                            if (!$pack) {
                                return $response
                                    ->setError()
                                    ->setMessage('Please add Peices in Product ' . $row['style']);
                            }
                            $orderQuantity = $row['totalqty'] / $pack;

//                            if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES) {
//                                if ($orderQuantity <= $product->online_sales_qty) {
//                                    $checkProdQty = true;
//                                } elseif ($product->online_sales_qty > 0) {
//                                    $remQty = $orderQuantity - $product->online_sales_qty;
//                                    $orderQuantity = $product->online_sales_qty;
//                                    $errors[] = $row['styleno'] . ' product is short in ' . $remQty . ' quantity.';
//                                }
//                            } elseif (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES) {
//                                if ($orderQuantity <= $product->in_person_sales_qty) {
//                                    $checkProdQty = true;
//                                } elseif ($product->in_person_sales_qty > 0) {
//                                    $remQty = $orderQuantity - $product->in_person_sales_qty;
//                                    $orderQuantity = $product->in_person_sales_qty;
//                                    $errors[] = $row['styleno'] . ' product is short in ' . $remQty . ' quantity.';
//                                }
//                            } else {
                            if ($orderQuantity <= $product->quantity) {
                                $checkProdQty = true;
                            } elseif ($product->quantity > 0) {
                                $remQty = $orderQuantity - $product->quantity;
                                $orderQuantity = $product->quantity;
                                /*$errors[] = */
                                $newError[] = $row['styleno'] . ' product is short in ' . $remQty . ' quantity.';
                                $notFoundSkus .= $row['styleno'] . ', ';
                            }
//                            }

                        } else {
                            /*$errors[] = */
                            $newError[] = $row['styleno'] . ' product is not found.';
                            $notFoundSkus .= $row['styleno'] . ', ';
                        }

                        if (!$checkProdQty && $product) {
                            /*$errors[] = */
                            $newError[] = $row['styleno'] . ' product is out of stock.';
                            $notFoundSkus .= $row['styleno'] . ', ';
                        }

                        $orderPo = DB::table('ec_order_import')->where('po_number', $row['ponumber'])->first();

                        if ($orderPo != null && $product && $checkProdQty) {
                            $detail['order_id'] = $orderPo->order_id;
                            $detail['qty'] = $orderQuantity;
                            $detail['price'] = str_replace(['$', ','], '', $row['subtotal']) / $orderQuantity;
                            $detail['product_id'] = $product->id;
                            $detail['product_name'] = $product->name;
                            $orderProduct = OrderProduct::create($detail);
                            //import record
                        } else /*if ($product)*/ {
                            $iorder['user_id'] = $customer->id;
                            $iorder['amount'] = $iorder['sub_total'] = str_replace(['$', ','], '', $row['totalamount']);;
                            $iorder['currency_id'] = 1;
                            $iorder['is_confirmed'] = 1;
                            $iorder['is_finished'] = 1;
                            $iorder['discount_amount'] = 0;
                            $iorder['shipping_amount'] = 0;
                            $iorder['platform'] = 'online';
                            $iorder['tax_amount'] = 0;
                            $iorder['salesperson_id'] = @auth()->user()->id;
                            $iorder['status'] = OrderStatusEnum::PROCESSING;
                            $iorder['order_type'] = Order::$IMPORT_ORDER_TYPES[$row['orderstatus']];
                            $importOrder = Order::create($iorder);
                            if ($importOrder && $product && $checkProdQty) {
                                $this->addOrderImportHistory($importOrder->id, Order::$MARKETPLACE[Order::FASHIONGO]);
                                $detail['order_id'] = $importOrder->id;
                                $detail['qty'] = $orderQuantity;
                                $detail['price'] = str_replace(['$', ','], '', $row['subtotal']) / $orderQuantity;
                                $detail['product_id'] = $product->id;
                                $detail['product_name'] = $product->name;
                                $orderProduct = OrderProduct::create($detail);
                                if ($importOrder) {
                                    $orderInfo['order_id'] = $importOrder->id;
                                    $orderInfo['po_number'] = $row['ponumber'];
                                    $orderInfo['order_date'] = $row['orderdate'];
                                    $orderInfo['type'] = Order::FASHIONGO;
                                    $orderInfo['order_import_upload_id'] = $upload->id;
                                    $orderInfo['not_found_skus'] = $notFoundSkus;
                                    OrderImport::create($orderInfo);
                                }
                            }
                        }

                        if (isset($importOrder->id) && count($newError)) {
                            $errors[$importOrder->id][] = implode('/', $newError);
                        } elseif (isset($orderPo->order_id) && count($newError)) {
                            $errors[$orderPo->order_id][] = implode('/', $newError);
                        }


                        if (isset($importOrder->id)) {
                            $this->updateOrderTotal($importOrder->id, false);
                        } elseif (isset($orderPo->order_id)) {
                            $this->updateOrderTotal($orderPo->order_id, false);
                        }


                        if ($product && $orderProduct && $orderProduct->order->order_type == Order::NORMAL) {
                            $this->productRepository
                                ->getModel()
                                ->where('id', $product->id)
                                ->where('with_storehouse_management', 1)
                                ->where('quantity', '>', 0)
                                ->decrement('quantity', $orderQuantity);
                            updateOldSystemProdQty($product, $orderQuantity, $orderPo->order_id);

                            /*if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES || @auth()->user()->roles[0]->slug == Role::ADMIN) {
                                $this->productRepository
                                    ->getModel()
                                    ->where('id', $product->id)
                                    ->where('with_storehouse_management', 1)
                                    ->where('online_sales_qty', '>', 0)
                                    ->decrement('online_sales_qty', $orderQuantity);
                            }
                            if (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES || @auth()->user()->roles[0]->slug == Role::ADMIN) {
                                $this->productRepository
                                    ->getModel()
                                    ->where('id', $product->id)
                                    ->where('with_storehouse_management', 1)
                                    ->where('in_person_sales_qty', '>', 0)
                                    ->decrement('in_person_sales_qty', $orderQuantity);
                            }*/

                            $productN = $this->productRepository->findById($product->id);
                            set_product_oos_date($orderProduct->order_id, $productN, $orderQuantity, $product->quantity);

                            $getParentProdId = ProductVariation::where('product_id', $product->id)->value('configurable_product_id');
                            $logParam = [
                                'parent_product_id' => $getParentProdId,
                                'product_id'        => $product->id,
                                'sku'               => $product->sku,
                                'quantity'          => $orderProduct->qty,
                                'new_stock'         => $product->quantity - $orderProduct->qty,
                                'old_stock'         => $product->quantity,
                                'order_id'          => $orderProduct->order_id,
                                'created_by'        => Auth::user()->id,
                                'reference'         => InventoryHistory::PROD_ORDER_QTY_DEDUCT
                            ];
                            log_product_history($logParam);

                        }

                        if ($product && $orderProduct && $orderProduct->order->order_type == Order::PRE_ORDER) {
                            $pre_order_max_qty = get_ecommerce_setting('pre_order_max_qty');
                            $preOrderQty = OrderProduct::join('ec_orders', 'ec_orders.id', 'ec_order_product.order_id')
                                ->where('product_id', $product->id)
                                ->where('order_type', Order::PRE_ORDER)
                                ->whereNotIn('status', [OrderStatusEnum::CANCELED, OrderStatusEnum::PENDING])
                                ->sum('qty');

                            if ($preOrderQty >= $pre_order_max_qty) {
                                generate_notification('pre_order_max_qty', $product);
                            }
                        }

                    }
                }
            }

        } else {
            return 'not supported';
        }

        return redirect(route('orders.import', ['import' => $upload->id, 'import_errors' => $errors]));
    }

    public function addOrderImportHistory($orderId, $sheet)
    {
        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'import_order',
            'description' => 'This Order has been imported from ' . $sheet . '  by %user_name%.',
            'order_id'    => $orderId,
            'user_id'     => Auth::user()->getKey(),
        ], []);
        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'create_order',
            'description' => trans('plugins/ecommerce::order.new_order',
                ['order_id' => get_order_code($orderId)]),
            'order_id'    => $orderId,
        ], []);
        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'confirm_order',
            'description' => trans('plugins/ecommerce::order.order_was_verified_by'),
            'order_id'    => $orderId,
            'user_id'     => Auth::user()->getKey(),
        ], []);
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
            'total'             => $request->amount,
            'pre_auth'          => 1
        ];

        $orderProducts = OrderProduct::where('order_id', $request->order_id)->get()->toArray();
        foreach ($orderProducts as $p) {
            $data['meta']['lineItems'][] = [
                'price'    => $p['price'],
                'quantity' => $p['qty'],
                'item'     => $p['product_name'],
            ];
        }

        $url = (env("OMNI_URL") . "charge/");
        list($response, $info) = omni_api($url, $data, 'POST');

        $status = $info['http_code'];

        if (floatval($status) == 200) {
            $response = json_decode($response, true);
            $order['order_id'] = $request->order_id;
            $order['card_id'] = $request->payment_id;
            $order['transaction_id'] = $response['id'];
            $order['response'] = json_encode($response);
            $order['status'] = 0;
            $order['payment_status'] = 0;
            CardPreAuth::create($order);
        } else {
            $errors = [
                422 => 'The transaction didn\'t reach a gateway',
                400 => 'The transaction didn\'t reach a gateway but there weren\'t validation errors',
                401 => 'The account is not yet activated or ready to process payments.',
                500 => 'Unknown issue - Please contact Fattmerchant'
            ];
            $response = json_decode($response, true);
            $status = [];
            $status['transaction_error'] = $response['message'];
            $status['status'] = 'Declined';
            Order::where('id', $request->order_id)->update($status);
            CardPreAuth::updateOrCreate(['order_id' => $request->order_id, 'card_id' => $request->payment_id], ['response' => $response['message'], 'payment_status' => 'Declined']);
            return back();
        }

        //$response->setMessage('Payment Successfully');
        return back();
    }

    public function capture(Request $request)
    {
        $data = [
            'total' => $request->amount,
        ];
        $url = (env("OMNI_URL") . "transaction/" . $request->transaction_id . "/capture");
        list($response, $info) = omni_api($url, $data, 'POST');
        $status = $info['http_code'];
        if (floatval($status) == 200) {
            $order['status'] = 1;
            $order['payment_status'] = 1;
            CardPreAuth::where('transaction_id', $request->transaction_id)->update($order);

            $getCard = CardPreAuth::where('transaction_id', $request->transaction_id)->first();
            if ($getCard) {
                OrderSplitPayment::where(['order_id' => $getCard->order_id, 'payment_type' => 'card_' . $getCard->card_id])->update(['status' => 'paid']);
            }

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
        return back();
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     */
    public function changeStatus(Request $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($request->input('pk'));
        $requestData['status'] = $request->input('value');
        $requestData['updated_by'] = auth()->user()->id;

        if ($order->status == OrderStatusEnum::CANCELED && $requestData['status'] != OrderStatusEnum::CANCELED) {
            if ($order->order_type != Order::PRE_ORDER) {
                $order_products = OrderProduct::where('order_id', $order->id)->get();
                foreach ($order_products as $order_product) {
                    $getParentProdId = ProductVariation::where('product_id', $order_product->product_id)->value('configurable_product_id');
                    $logParam = [
                        'parent_product_id' => $getParentProdId,
                        'product_id'        => $order_product->product_id,
                        'sku'               => $order_product->product->sku,
                        'quantity'          => $order_product->qty,
                        'new_stock'         => $order_product->product->quantity - $order_product->qty,
                        'old_stock'         => $order_product->product->quantity,
                        'order_id'          => $order->id,
                        'created_by'        => Auth::user()->id,
                        'reference'         => InventoryHistory::PROD_ORDER_QTY_DEDUCT
                    ];
                    log_product_history($logParam);

                    $this->productRepository
                        ->getModel()
                        ->where('id', $order_product->product_id)
                        ->where('with_storehouse_management', 1)
                        ->decrement('quantity', $order_product->qty);
                }
            }
        }

        $order->fill($requestData);

        event(new UpdatedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $order));
        $this->orderRepository->createOrUpdate($order);

        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'order_status_changed',
            'description' => 'Order status changed to ' . $requestData['status'] . ' by %user_name%.',
            'order_id'    => $order->id,
            'user_id'     => Auth::user()->getKey(),
        ], []);


        if ($requestData['status'] == 'shipping complete') {
            $order->order_completion_date = Carbon::now();
            $order->save();
        } elseif ($requestData['status'] == 'in store complete') {
            $order->order_completion_date = Carbon::now();
            $order->save();
        }


//        $getOldSystemStatus = newToOldStatus($requestData['status']);
////        DB::connection('mysql2')->table('hw_orders')->where('order_id', $order->id)->update(['hw_orders.status' => $getOldSystemStatus]);

        return $response;
    }

    public function verifyOrderProductShipment($orderId, $prodId, $prodQty, Request $request, BaseHttpResponse $response)
    {
        if ($prodId && $prodQty) {
            $product = $this->productRepository->findById($prodId);
            $demandQty = $prodQty;
            if ($product->quantity >= $demandQty) {
                $where = ['order_id' => $orderId, 'product_id' => $prodId];
                $data = $where;
                $data['is_verified'] = 1;
                $data['created_by'] = auth()->user()->id;
                OrderProductShipmentVerify::updateOrCreate($where, $data);
            }
        }
        return redirect()->back();
    }

    public function verifyOrderProductShipmentBarcode($orderId, $barcode, Request $request, BaseHttpResponse $response)
    {
        if ($barcode) {
            $product = Product::where('upc', $barcode)->first();
            if ($product) {
                $orderProduct = OrderProduct::where(['order_id' => $orderId, 'product_id' => $product->id])->first();
                if ($orderProduct) {
                    $demandQty = $orderProduct->qty;
                    //if ($product->quantity >= $demandQty) {

                    $where = ['order_id' => $orderId, 'product_id' => $product->id];
                    $data = $where;
                    if ($demandQty == 1) {
                        $data['is_verified'] = 1;
                    }
                    $data['qty'] = 1;
                    $data['created_by'] = auth()->user()->id;

                    $check = OrderProductShipmentVerify::where($where)->first();
                    if (!$check) {
                        OrderProductShipmentVerify::create($data);
                    } else {
                        if ($demandQty == $check->qty) {
                            $data['qty'] = $demandQty;
                            $data['is_verified'] = 1;
                            OrderProductShipmentVerify::where($where)->update($data);
                        } else {
                            $data['qty'] = $check->qty + 1;
                            if ($demandQty == $data['qty']) {
                                $data['is_verified'] = 1;
                            }
                            OrderProductShipmentVerify::where($where)->update($data);
                        }
                    }
                    // return redirect()->back();
                    return response()->json(['status' => 'success'], 200);
                    /*} else {
                        // return $response->setCode(406)->setError()->setMessage($product->sku . ' is not available in ordered Qty!');
                        return response()->json(['status' => 'error', 'message' => $product->sku . ' is not available in ordered Qty!'], 406);
                    }*/
                } else {
                    // return $response->setCode(406)->setError()->setMessage($product->sku . ' is not available in ordered Qty!');
                    return response()->json(['status' => 'error', 'message' => $product->sku . ' is not available in order!'], 406);
                }
            } else {
                // return $response->setCode(406)->setError()->setMessage('Product not found!');
                return response()->json(['status' => 'error', 'message' => 'Product not found!'], 406);
            }
        } else {
            // return $response->setCode(406)->setError()->setMessage('This barcode '. $barcode . ' is not available!');
            return response()->json(['status' => 'error', 'message' => 'This barcode ' . $barcode . ' is not available!'], 406);
        }
    }

    public function saveAdvanceSearch($type, Request $request)
    {
        $params = $request->all();

        $searchData = ['user_id' => auth()->user()->id, 'search_type' => $type, 'name' => $params['search_name'], 'status' => 1];
        $search = UserSearch::updateOrCreate($searchData);

        unset($params['search_name']);
        foreach ($params as $key => $value) {
            if ($value) {
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                $searchItems = ['user_search_id' => $search->id, 'key' => $key, 'value' => $value];
                UserSearchItem::updateOrCreate(['user_search_id' => $search->id, 'key' => $key], $searchItems);
            }
        }

        if ($search) {
            return response()->json(['status' => 'success', 'data' => $search], 200);
        } else {
            return response()->json(['status' => 'error', 'data' => []], 500);
        }
    }

    public function orderRevertRefundProduct($id, $prod_id, Request $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findById($id);

        $getOrderRefProd = OrderRefundProduct::where(['order_id' => $order->id, 'product_id' => $prod_id])->first();

        if ($getOrderRefProd) {
            $this->orderHistoryRepository->createOrUpdate([
                'action'      => 'order_revert_refund_product',
                'description' => 'Order Revert Refund Product by %user_name%.',
                'order_id'    => $id,
                'user_id'     => Auth::user()->getKey(),
            ], []);

            $getOrderProd = OrderProduct::where(['order_id' => $order->id, 'product_id' => $prod_id])->first();
            if ($getOrderProd) {
                $getOrderProd->qty += $getOrderRefProd->qty;
                $getOrderProd->save();
                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'order_product_qty_changed',
                    'description' => 'Order product ' . $getOrderRefProd->product_name . ' qty changed to ' . $getOrderProd->qty . ' by %user_name%.',
                    'order_id'    => $order->id,
                    'user_id'     => Auth::user()->getKey(),
                ], []);
            } else {
                $orderProd = $getOrderRefProd->replicate();
                $this->orderProductRepository->createOrUpdate($orderProd);
                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'order_product_qty_changed',
                    'description' => 'Order product ' . $getOrderRefProd->product_name . ' qty changed to ' . $getOrderRefProd->qty . ' by %user_name%.',
                    'order_id'    => $order->id,
                    'user_id'     => Auth::user()->getKey(),
                ], []);
            }

            $getOrderRefProd->delete();

            $checkOrderProdCnt = OrderProduct::where('order_id', $order->id)->sum('qty');
            if ($checkOrderProdCnt && $order->status == OrderStatusEnum::REFUND) {
                $order->status = OrderStatusEnum::NEW_ORDER;
                $order->save();
            }

            $this->updateOrderTotal($order->id);
        }

        return $response->setData($order)->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function orderRefundProduct($id, Request $request, BaseHttpResponse $response)
    {
        $params = $request->all();

        $order = $this->orderRepository->findById($id);

        /*************** Validation Start ****************/
        $checkProd = false;
        $products = $order->products->pluck('qty', 'product_id')->all();
        foreach ($products as $productId => $quantity) {
            foreach ($params['order_refund_prod_move'] as $prodId => $qty) {
                if ($productId == $prodId && (int)$qty > $quantity) {
                    return $response->setCode(406)->setError()->setMessage('Product Qty is not available!');
                }
                if ($productId == $prodId) {
                    $checkProd = true;
                }
            }
        }

        if (!$checkProd) {
            return $response->setCode(406)->setError()->setMessage('Product is not available!');
        }
        /*************** Validation End ****************/

        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'order_refund_product',
            'description' => 'Order Refund Products by %user_name%.',
            'order_id'    => $id,
            'user_id'     => Auth::user()->getKey(),
        ], []);

        $products = $order->products;
        foreach ($products as $product) {
            foreach ($params['order_refund_prod_move'] as $prodId => $qty) {
                if ($product->product_id == $prodId && (int)$qty <= $product->qty) {
                    $productData = $product->replicate();
                    $productData->qty = (int)$qty;
                    if ($productData->qty > 0) {
                        $getOrderRefProd = OrderRefundProduct::where(['order_id' => $order->id, 'product_id' => $product->product_id])->first();
                        if ($getOrderRefProd) {
                            $getOrderRefProd->qty += $productData->qty;
                            $getOrderRefProd->save();
                        } else {
                            OrderRefundProduct::create($productData->toArray());
                        }
                    }

                    $this->orderHistoryRepository->createOrUpdate([
                        'action'      => 'order_product_qty_changed',
                        'description' => 'Order product ' . $product->product_name . ' qty changed from ' . $product->qty . ' to ' . ($product->qty - (int)$qty) . ' by %user_name%.',
                        'order_id'    => $order->id,
                        'user_id'     => Auth::user()->getKey(),
                    ], []);

                    $product->qty -= (int)$qty;
                    if ($product->qty <= 0) {
                        $product->delete();
                    } else {
                        $product->save();
                    }

                }
            }
        }

        $checkOrderProdCnt = OrderProduct::where('order_id', $order->id)->sum('qty');
        if (!$checkOrderProdCnt) {
            $order->status = OrderStatusEnum::REFUND;
            $order->save();
        }

        $this->updateOrderTotal($order->id);

        return $response->setData($order)->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function splitOrder($id, Request $request, BaseHttpResponse $response)
    {
        $params = $request->all();

        $order = $this->orderRepository->findById($id);

        /*************** Validation Start ****************/
        $checkProd = false;
        $products = $order->products->pluck('qty', 'product_id')->all();
        foreach ($products as $productId => $quantity) {
            foreach ($params['order_prod_move'] as $prodId => $qty) {
                if ($productId == $prodId && (int)$qty > $quantity) {
                    return $response->setCode(406)->setError()->setMessage('Product Qty is not available!');
                }
                if ($productId == $prodId) {
                    $checkProd = true;
                }
            }
        }

        if (!$checkProd) {
            return $response->setCode(406)->setError()->setMessage('Product is not available!');
        }
        /*************** Validation End ****************/


        /*************** Replication Start ****************/
        $orderData = $order->replicate();
        $orderData->order_type = Order::PRE_ORDER;
        $orderData->status = 'new';
        $new_order = $this->orderRepository->createOrUpdate($orderData);

        $paymentData = $order->payment->replicate();
        $paymentData->order_id = $new_order->id;
        $payment = $this->paymentRepository->createOrUpdate($paymentData);

        $new_order->payment_id = $payment->id;
        $new_order->coupon_code = NULL;
        $new_order->discount_amount = 0;
        $new_order->save();

        $histories = $order->histories;
        foreach ($histories as $history) {
            $historyData = $history->replicate();
            $historyData->order_id = $new_order->id;
            $this->orderHistoryRepository->createOrUpdate($historyData);
        }

        $addressData = $order->address->replicate();
        $addressData->order_id = $new_order->id;
        $this->orderAddressRepository->createOrUpdate($addressData);

        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'split_order',
            'description' => 'Order split by %user_name%.',
            'order_id'    => $id,
            'user_id'     => Auth::user()->getKey(),
        ], []);

        $products = $order->products;
        foreach ($products as $product) {
            foreach ($params['order_prod_move'] as $prodId => $qty) {
                if ($product->product_id == $prodId && (int)$qty <= $product->qty) {
                    $productData = $product->replicate();
                    $productData->order_id = $new_order->id;
                    $productData->qty = (int)$qty;
                    if ($productData->qty > 0) {
                        $this->orderProductRepository->createOrUpdate($productData);
                    }


                    $this->orderHistoryRepository->createOrUpdate([
                        'action'      => 'order_product_qty_changed',
                        'description' => 'Order product ' . $product->product_name . ' qty changed from ' . $product->qty . ' to ' . ($product->qty - (int)$qty) . ' by %user_name%.',
                        'order_id'    => $order->id,
                        'user_id'     => Auth::user()->getKey(),
                    ], []);

                    $getParentProdId = ProductVariation::where('product_id', $product->product_id)->value('configurable_product_id');
                    $logParam = [
                        'parent_product_id' => $getParentProdId,
                        'product_id'        => $product->product_id,
                        'sku'               => $product->product->sku,
                        'quantity'          => (int)$qty,
                        'new_stock'         => $product->product->quantity + (int)$qty,
                        'old_stock'         => $product->product->quantity,
                        'order_id'          => $order->id,
                        'created_by'        => Auth::user()->id,
                        'reference'         => InventoryHistory::PROD_ORDER_QTY_ADD
                    ];
                    log_product_history($logParam);


                    $product->qty -= (int)$qty;
                    if ($product->qty <= 0) {
                        $product->delete();
                    } else {
                        $product->save();
                    }

                    if ($order->order_type != Order::PRE_ORDER) {
                        $this->productRepository
                            ->getModel()
                            ->where('id', $product->product_id)
                            ->where('with_storehouse_management', 1)
                            ->increment('quantity', (int)$qty);
                    }

                }
            }
        }

        $this->updateOrderTotal($order->id);
        $this->updateOrderTotal($new_order->id);

        /*************** Replication End ****************/

        return $response->setData($order)->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function updateOrderTotal($orderId, $checkPromotion = true)
    {
        $orderObj = Order::where('id', $orderId)->first();

        $orderTotal = 0;
        $products = $orderObj->products;
        foreach ($products as $product) {
            $orderTotal += $product->qty * $product->price;
        }

        $orderObj->sub_total = $orderTotal;

        $orderTotal += $orderObj->shipping_amount;
        $orderTotal -= $orderObj->discount_amount;

        $orderObj->amount = $orderTotal;

        Order::where('id', $orderId)->update(['sub_total' => $orderObj->sub_total, 'amount' => $orderObj->amount]);

        if ($checkPromotion) {
            $this->promotion_service->applyPromotionIfAvailable($orderObj->id);
        }
    }

    public function splitPayment($id, Request $request, BaseHttpResponse $response)
    {
        $params = $request->except('_token');

        $orderAmount = Order::where('id', $id)->value('amount');
        if (array_sum(array_values($params)) != $orderAmount) {
            return $response->setCode(406)->setError()->setMessage('Order Split Payment must be equal to the Order Total Amount!');
        }

        OrderSplitPayment::where('order_id', $id)->delete();
        foreach ($params as $key => $val) {
            if ($key != '_token') {
                $data = ['order_id' => $id, 'payment_type' => $key, 'amount' => $val];
                OrderSplitPayment::create($data);
            }
        }
        Order::where('id', $id)->update(['split_payment' => 1]);

        return $response->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function quicksearch(Request $request, BaseHttpResponse $response)
    {
        $order = $this->orderRepository->findOrFail($request->quicksearch);
        if ($order) {
            //redirect(route('order'))
        }
        return $response->setData(Helper::countries());
    }

    public function salesRepupdate($id, Request $request, BaseHttpResponse $response)
    {
        $rep['salesperson_id'] = $request->salesperson_id;
        $order = Order::where('id', $id)->update($rep);
        return $response->setData($order)->setMessage('Sales Rep Updated Sucessfully');
    }

    public function printReceipt($orders)
    {
        $list = Order::whereIn('id', json_decode($orders))->with(['payment', 'shippingAddress', 'billingAddress', 'products' => function ($query) {
            $query->with(['product']);
        }])->get();

        $orderHtml = '';
        foreach ($list as $order) {
            $orderHtml .= view('plugins/ecommerce::orders.partials.orderReceipt', ['order' => $order]);
        }

        return view('plugins/ecommerce::orders.receiptList', ['orderHtml' => $orderHtml]);

    }

    public function removeDiscount($orderId, Request $request)
    {
        $getType = $request->get('discount_type', false);
        if ($getType == 'coupon') {
            $order = Order::where('id', $orderId)->first();
            $order->discount_amount = $order->promotion_amount;
            $order->coupon_code = NULL;
            $order->discount_description = NULL;
            $order->amount = $order->sub_total - $order->discount_amount;
            $order->save();

            $this->orderHistoryRepository->createOrUpdate([
                'action'      => 'remove_discount_on_order',
                'description' => '$' . $order->discount_amount . ' discount removed from order by %user_name%.',
                'order_id'    => $order->id,
                'user_id'     => Auth::user()->getKey(),
            ], []);

        } elseif ($getType == 'promotion') {
            $this->promotion_service->removePromotionIfAvailable($orderId);
        }
        return redirect()->back();
    }


    public function createInvoice($orderId, Request $request, BaseHttpResponse $response)
    {
        if ($orderId) {
            $order = Order::where('id', $orderId)->first();
            $user = Customer::where('id', $order->user_id)->first();

            //LIVE
            $apiContext = new \PayPal\Rest\ApiContext(
                new \PayPal\Auth\OAuthTokenCredential(
                    env('PAYPAL_SANDBOX_CLIENT_ID', 'AciZq5ZxzoDCQB03f_TzwP9dC6UB2GqihP4s_gu3DJLfcVryAB8sf2UFTCWXl5rSSagaKXHYDpwL_xpP'),     // ClientID
                    env('PAYPAL_SANDBOX_CLIENT_SECRET', 'EHgN0b0AItivo9SztzVFr5ZUVshS_MdagqUcaHHjX-QLdNRbbPZGeKdVZDA2ebr9JRjyCeMDKJSrQgdQ')     // ClientSecret
                )
            );

            //SANDBOX
            /*$apiContext = new \PayPal\Rest\ApiContext(
                new \PayPal\Auth\OAuthTokenCredential(
                    'AQX1TpbTGqjgE56LJL0M880xBPBIHKSY8wMT1Ehk8Po_fkIjGAqTbrOPZVNuea1OgjuLE-qZOjb3eysv',     // ClientID
                    'EAZWdCMjo7OK1ndf-4658vQ5Xt9HII_cnwhJqRrKlDf0sfUEqrvJqIlFXJ8RtmyvUGuqeLzS4mbRyNXT'     // ClientSecret
                )
            );*/

            $apiContext->setConfig(
                array(
                    'mode'           => env('PAYPAL_MODE', 'live'),
                    'log.LogEnabled' => false,
                    'log.FileName'   => '../PayPal.log',
                    'log.LogLevel'   => 'INFO', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                )
            );

            $invoice = new Invoice();
            $invoice
                ->setMerchantInfo(new MerchantInfo())
                ->setBillingInfo(array(new BillingInfo()))
                ->setNote($request->type == 0 ? 'Shipping' : 'Order' . " Cost of order # " . $order->id)
                ->setPaymentTerm(new PaymentTerm())
                ->setShippingInfo(new ShippingInfo());

            //LIVE
            $invoice->getMerchantInfo()
                ->setemail("accountsreceivable@landbapparel.com")
                ->setbusinessName("LUCKY AND BLESSED LLC")
                ->setPhone(new Phone())
                ->setAddress(new InvoiceAddress());

            //SANDBOX
            /*$invoice->getMerchantInfo()
                ->setemail("sb-h2vny4007350@business.example.com")
                ->setbusinessName("LUCKY AND BLESSED LLC")
                ->setPhone(new Phone())
                ->setAddress(new InvoiceAddress());*/

            $invoice->getMerchantInfo()->getPhone()
                ->setCountryCode("001")
                ->setNationalNumber("9724086273");

            $invoice->getMerchantInfo()->getAddress()
                ->setLine1("12801 N Stemmon.")
                ->setCity("Farmers Branch")
                ->setState("TX")
                ->setPostalCode("75234")
                ->setCountryCode("US");

            $billing = $invoice->getBillingInfo();
            $billing[0]->setEmail(($order->payments) ? $order->payments[0]->paypal_email : $user->email);


            $items = array();
            if ($request->type == 0) {
                if ($order->shipping_amount > 0) {
                    // After Step 1
                    $items[0] = new InvoiceItem();
                    $items[0]
                        ->setName("Shipping Cost of Order ID:" . $order->id)
                        ->setQuantity(1)
                        ->setUnitPrice(new Currency());
                    $items[0]->getUnitPrice()
                        ->setCurrency("USD")
                        ->setValue($order->shipping_amount);
                } else {
                    return $response->setCode(406)->setError()->setMessage('Please add the shipping cost first');
                }
            } else {
                $i = 0;
                foreach ($order->products as $ord) {
                    $items[$i] = new InvoiceItem();
                    $items[$i]
                        ->setName("Product Code:" . $ord->product->sku)
                        ->setQuantity($ord->qty)
                        ->setUnitPrice(new Currency());
                    $items[$i]->getUnitPrice()
                        ->setCurrency("USD")
                        ->setValue($ord->price);
                    $i++;
                }
            }
            $invoice->setItems($items);

            $invoice->getPaymentTerm()->setTermType("NET_45");

            $invoice->getShippingInfo()
                ->setFirstName($user->first_name)
                ->setLastName($user->last_name)
                ->setBusinessName(isset($user->detail->company) ? $user->detail->company : 'No Company')
                ->setPhone(new Phone())
                ->setAddress(new InvoiceAddress());

            $invoice->getShippingInfo()->getPhone()
                ->setCountryCode("001")
                ->setNationalNumber(isset($user->phone) ? $user->phone : '0000000000');

            $invoice->getShippingInfo()->getAddress()
                ->setLine1(isset($user->shippingAddress->address) ? $user->shippingAddress->address : '')
                ->setCity(isset($user->shippingAddress->city) ? $user->shippingAddress->city : '')
                ->setState(isset($user->shippingAddress->state) ? $user->shippingAddress->state : '')
                ->setPostalCode(isset($user->shippingAddress->zip_code) ? $user->shippingAddress->zip_code : '')
                ->setCountryCode(isset($user->shippingAddress->country) ? $user->shippingAddress->country : 'US');

            $invoice->setLogoUrl('https://landbapparel.com/images/logos/31/landb-final---logo.png');


            try {
                $paypal = Payment::where(['order_id' => $order->id, 'payment_channel' => 'paypal', 'type' => $request->type])->whereNotNull('paypal_invoice_id')->orderBy('id', 'DESC')->first();
                if ($paypal != null && $paypal->paypal_invoice_id) {
                    if ($this->checkInvoice($paypal->paypal_invoice_id, $request->type)) {
                        return redirect()->back();
                    }
                    $notify = new Notification();
                    $notify->setSubject("Invoice Reminder")->setNote("Please pay soon")->setSendToMerchant(true);
                    $invoice = Invoice::get($paypal->paypal_invoice_id, $apiContext);
                    $invoice->remind($notify, $apiContext);
                    return $response->setMessage('Reminder Sent');
                } else {
                    $invoice = $invoice->create($apiContext);
                    $invoice->send($apiContext);
                    $invoice = Invoice::get($invoice->id, $apiContext);

                    Payment::updateOrCreate(['order_id' => $order->id, 'payment_channel' => 'paypal', 'type' => $request->type])->update(['paypal_invoice_id' => $invoice->id]);
                    return $response->setMessage('Invoice created successfully');
                }

            } catch (\PayPal\Exception\PayPalConnectionException $ex) {
                echo $ex->getCode(); // Prints the Error Code
                echo $ex->getData();
                die($ex);
            }
        }

        return $response->setCode(406)->setError()->setMessage('Data is incorrect');
    }

    public function checkInvoice($paypal_invoice_id, $type)
    {
        //LIVE
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                env('PAYPAL_SANDBOX_CLIENT_ID', 'AciZq5ZxzoDCQB03f_TzwP9dC6UB2GqihP4s_gu3DJLfcVryAB8sf2UFTCWXl5rSSagaKXHYDpwL_xpP'),     // ClientID
                env('PAYPAL_SANDBOX_CLIENT_SECRET', 'EHgN0b0AItivo9SztzVFr5ZUVshS_MdagqUcaHHjX-QLdNRbbPZGeKdVZDA2ebr9JRjyCeMDKJSrQgdQ')     // ClientSecret
            )
        );

        //SANDBOX
        /*$apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'AQX1TpbTGqjgE56LJL0M880xBPBIHKSY8wMT1Ehk8Po_fkIjGAqTbrOPZVNuea1OgjuLE-qZOjb3eysv',     // ClientID
                'EAZWdCMjo7OK1ndf-4658vQ5Xt9HII_cnwhJqRrKlDf0sfUEqrvJqIlFXJ8RtmyvUGuqeLzS4mbRyNXT'     // ClientSecret
            )
        );*/

        $apiContext->setConfig(
            array(
                'mode'           => env('PAYPAL_MODE', 'live'),
                'log.LogEnabled' => true,
                'log.FileName'   => '../PayPal.log',
                'log.LogLevel'   => 'INFO', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
            )
        );

        try {
            $paypal = Payment::where('paypal_invoice_id', $paypal_invoice_id)->where('status', 'pending')->where('payment_channel', 'paypal')->where('type', $type)->orderBy('id', 'DESC')->first();
            if ($paypal && $paypal->paypal_invoice_id) {
                $invoice = Invoice::get($paypal->paypal_invoice_id, $apiContext);
                if ($invoice->status == 'PAID') {
                    Payment::where('id', $paypal->id)->update(['status' => 'completed']);
                    return true;
                }
            }
            return false;
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode(); // Prints the Error Code
            echo $ex->getData();
            die($ex);
        }
    }


    public function pushOrderToOldSystem($id, BaseHttpResponse $response)
    {
        $newSystemOrder = Order::where('id', $id)->first();
        $bAddress = OrderAddress::where(['order_id' => $id, 'type' => 'billing'])->first();

        $sAddress = OrderAddress::where(['order_id' => $id, 'type' => 'shipping'])->first();

        $status = newToOldStatus($newSystemOrder->status);
    dd($newSystemOrder->order_completion_date,$newSystemOrder->created_at );
        $orderData = [
            'user_id'                 => $newSystemOrder->user_id,
            'status'                  => ($newSystemOrder->order_type == 'normal') ? $status : 'B',
            'location'                => 'new portal id ' . $id,
            'total'                   => $newSystemOrder->amount,
            'shipping_cost'           => $newSystemOrder->shipping_amount,
            'notes'                   => $newSystemOrder->description,
            'subtotal_discount'       => $newSystemOrder->discount_amount,
            'subtotal'                => $newSystemOrder->sub_total,
            'issuer_id'               => $newSystemOrder->salesperson_id,
            'po_number'               => $newSystemOrder->po_number,
            'complete_date'           => $newSystemOrder->order_completion_date->getTimestamp(),
//            'timestamp'               => date('Y-m-d H:i:s', strtotime($newSystemOrder->created_at)),
            'timestamp'               => $newSystemOrder->created_at->getTimestamp(),
            'last_status_change_date' => $newSystemOrder->updated_at->getTimestamp(),

            'email'      => $newSystemOrder->user->email,
            'company'    => '',
            'company_id' => 1
            ,

            'b_firstname' => @$bAddress->name,
            'b_lastname'  => @$bAddress->name,
            'b_phone'     => @$bAddress->phone,
            'b_country'   => @$bAddress->country,
            'b_state'     => @$bAddress->state,
            'b_city'      => @$bAddress->city,
            'b_address_2' => @$bAddress->address,
            'b_zipcode'   => @$bAddress->zip_code,

            's_firstname' => @$sAddress->name,
            's_lastname'  => @$sAddress->name,
            's_phone'     => @$sAddress->phone,
            's_country'   => @$sAddress->country,
            's_state'     => @$sAddress->state,
            's_city'      => @$sAddress->city,
            's_address_2' => @$sAddress->address,
            's_zipcode'   => @$sAddress->zip_code,
        ];

        $checkOldSystemOrder = DB::connection('mysql2')->table('hw_orders')->where('order_id', $newSystemOrder->old_system_order_id)->first();
        if ($checkOldSystemOrder) {
            $oldSystemOrderId = $newSystemOrder->old_system_order_id;
            DB::connection('mysql2')->table('hw_order_details')->where('order_id', $oldSystemOrderId)->delete();
            DB::connection('mysql2')->table('hw_orders')->where('hw_orders.order_id', $oldSystemOrderId)->update($orderData);
        } else {
            $oldSystemOrderId = DB::connection('mysql2')->table('hw_orders')->insertGetId($orderData);

            $newSystemOrder->old_system_order_id = $oldSystemOrderId;
            $newSystemOrder->save();
        }


        foreach ($newSystemOrder->products as $key => $orderProduct) {
            $productObj = Product::join('ec_product_variations', 'ec_product_variations.product_id', 'ec_products.id')
                ->where('ec_product_variations.product_id', $orderProduct->product_id)
                ->where('ec_product_variations.is_default', 1)
                ->first();

            $item_id = rand(000000000,000000000)+ $key;

            $orderProductData = [
                'item_id'      => $item_id,
                'order_id'     => $oldSystemOrderId,
                'amount'       => $productObj->prod_pieces ? ($productObj->prod_pieces * $orderProduct->qty) : $orderProduct->qty,
                'price'        => $productObj->prod_pieces ? round($orderProduct->price / $productObj->prod_pieces, 2) : $orderProduct->price,
                'product_id'   => $productObj->configurable_product_id,
                'product_code' => $productObj->sku
            ];

            DB::connection('mysql2')->table('hw_order_details')->insert($orderProductData);
        }

        $userOmniId = CustomerCard::where('customer_id', $newSystemOrder->user_id)->value('customer_omni_id');
        $data = ['omni_customer_id' => $userOmniId];
        DB::connection('mysql2')->table('hw_users')->where('user_id', $newSystemOrder->user_id)->update($data);

        return $response
            ->setData($newSystemOrder)
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function fetchOldSystemOrder($id, BaseHttpResponse $response)
    {
        $order = DB::connection('mysql2')
            ->table('hw_orders')
            ->orderBy('hw_orders.order_id', 'ASC')
            ->where('hw_orders.order_id', $id)
            ->first();

        $checkOrder = Order::where('id', $id)->value('id');

        if ($checkOrder) {
            $pre = 0;

            if ($order->status == 'C') {
                $order->status = 'shipping complete';
            } else if ($order->status == 'O') {
                $order->status = 'processing';
            } else if ($order->status == 'F') {
                $order->status = 'in store complete';
            } else if ($order->status == 'D') {
                $order->status = 'declined';
            } else if ($order->status == 'I') {
                $order->status = 'cancelled';
            } else if ($order->status == 'Y') {
                $order->status = 'pick up';
            } else if ($order->status == 'A') {
                $order->status = 'tradeshow';
            } else if ($order->status == 'E') {
                $order->status = 'exchange';
            } else if ($order->status == 'G') {
                $order->status = 'awaiting payment';
            } else if ($order->status == 'H') {
                $order->status = 'net accounts';
            } else if ($order->status == 'J') {
                $order->status = 'fashiongo complete';
            } else if ($order->status == 'K') {
                $order->status = 'lashowroom complete';
            } else if ($order->status == 'L') {
                $order->status = 'online pre-order';
            } else if ($order->status == 'Q') {
                $order->status = 'paid in full';
            } else if ($order->status == 'R') {
                $order->status = 'orangeshine complete';
            } else if ($order->status == 'S') {
                $order->status = 'atlanta pre-order';
            } else if ($order->status == 'U') {
                $order->status = 'dallas pre-order';
            } else if ($order->status == 'V') {
                $order->status = 'pita va';
            } else if ($order->status == 'W') {
                $order->status = 'pita personal';
            } else if ($order->status == 'X') {
                $order->status = 'store credit';
            } else if ($order->status == 'T') {
                $order->status = 'send to model';
            } else if ($order->status == 'AA') {
                $order->status = 'las vegas pre-order';
            } else if ($order->status == 'AB') {
                $order->status = 'on hold';
            } else if ($order->status == 'AC') {
                $order->status = 'model purchase';
            } else if ($order->status == 'AE') {
                $order->status = 'picking complete';
            } else if ($order->status == 'AF') {
                $order->status = 'dallas kids pre-order';
            } else if ($order->status == 'AG') {
                $order->status = 'showroom';
            } else if ($order->status == 'AH') {
                $order->status = 'refund';
            } else if ($order->status == 'AD') {
                $order->status = 'need tax ID';
            } else if ($order->status == 'AI') {
                $order->status = 'factory';
            } else if ($order->status == 'AJ') {
                $order->status = 'ready 2 ship';
            } else if ($order->status == 'AM') {
                $order->status = 'lashowroom processing';
            } else if ($order->status == 'AN') {
                $order->status = 'orangeshine processing';
            } else if ($order->status == 'AO') {
                $order->status = 'pending shipment';
            } else if ($order->status == 'AP') {
                $order->status = 'Western MKT Pre Order';
            } else if ($order->status == 'M') {
                $order->status = 'Orangeshine Pre-Order';
            } else if ($order->status == 'AQ') {
                $order->status = 'LA Showroom Pre-Order';
            } else if ($order->status == 'AR') {
                $order->status = 'FashionGO Pre-Order';
            } else if ($order->status == 'AS') {
                $order->status = 'Sample Sent';
            } else if ($order->status == 'AT') {
                $order->status = 'Pickup & Process';
            } else if ($order->status == 'AU') {
                $order->status = 'Donation / Gift';
            } else if ($order->status == 'AV') {
                $order->status = 'Delinquent';
            } else if ($order->status == 'AW') {
                $order->status = 'Ready to Charge';
            } else if ($order->status == 'AY') {
                $order->status = 'R2SO';
            } else if ($order->status == 'B') {
                $pre = 1;
            } else {
                $order->status = 'Failed Preauth';
            }

            $orderData = [
                'user_id'               => $order->user_id,
                'status'                => $order->status,
                'order_type'            => ($pre) ? Order::PRE_ORDER : Order::NORMAL,
                'platform'              => $order->location ? $order->location : ($order->online_order ? 'online' : 'backorder'),
                'amount'                => $order->total,
                'shipping_amount'       => $order->shipping_cost,
                'description'           => $order->notes,
                'discount_amount'       => $order->subtotal_discount,
                'sub_total'             => $order->subtotal,
                'salesperson_id'        => $order->issuer_id,
                'po_number'             => $order->po_number,
                'order_completion_date' => $order->complete_date,
                'created_at'            => date('Y-m-d H:i:s', $order->timestamp),
                'updated_at'            => date('Y-m-d H:i:s', $order->last_status_change_date),
            ];
            DB::table('ec_orders')->where('id', $id)->update($orderData);

            $neworder = Order::where('id', $id)->first();

            $orderProducts = DB::connection('mysql2')->table('hw_order_details')->where('order_id', $order->order_id)->get();

            OrderProduct::where('order_id', $id)->delete();

            foreach ($orderProducts as $orderProduct) {

                $productObj = Product::join('ec_product_variations', 'ec_product_variations.product_id', 'ec_products.id')
                    ->where('ec_product_variations.configurable_product_id', $orderProduct->product_id)
                    ->where('ec_product_variations.is_default', 1)
                    ->first();

                $qty = $orderProduct->amount;//8
                if ($productObj && $productObj->prod_pieces) {//3
                    $isPack = 1;
                    $packQty = floor($qty / $productObj->prod_pieces);//2.6
                    $qty = $packQty;//2

                    $looseQty = $packQty * $productObj->prod_pieces;//6
                    $diff = $orderProduct->amount - $looseQty;//2

                    if ($qty > 0) {
                        $orderProductData = [
                            'order_id'     => $orderProduct->order_id,
                            'qty'          => $qty,
                            'is_pack'      => $isPack,
                            'price'        => round($orderProduct->price * $productObj->prod_pieces, 2),
                            'product_id'   => $productObj->product_id,
                            'product_name' => $productObj->name
                        ];
                        OrderProduct::create($orderProductData);


                        $meta_condition = ['order_id' => $order->order_id];
                        $paymentData = [
                            'amount'       => round($orderProduct->price * $productObj->prod_pieces, 2),
                            'currency'     => get_application_currency()->title,
                            'paypal_email' => '',
                            'status'       => PaymentStatusEnum::PENDING,
                            'payment_type' => 'confirm',
                            'order_id'     => $order->order_id,
                            'charge_id'    => Str::upper(Str::random(10))
                        ];

                        if ($order->payment_id == 41) {
                            $paymentData['payment_channel'] = 'omni-payment';
                        } elseif ($order->payment_id == 20) {
                            $paymentData['payment_channel'] = 'paypal';
                            $paymentData['paypal_email'] = $neworder->user->email;
                        } elseif ($order->payment_id == 36) {
                            $paymentData['payment_channel'] = 'showroom';
                        } elseif ($order->payment_id == 37) {
                            $paymentData['payment_channel'] = 'Split';
                        } elseif ($order->payment_id == 29) {
                            $paymentData['payment_channel'] = 'Store Credit';
                        } elseif ($order->payment_id == 17) {
                            $paymentData['payment_channel'] = 'COD';
                        } elseif ($order->payment_id == 6) {
                            $paymentData['payment_channel'] = 'Cash';
                        } elseif ($order->payment_id == 9) {
                            $paymentData['payment_channel'] = 'Business Check';
                        } elseif ($order->payment_id == 35) {
                            $paymentData['payment_channel'] = 'Gift Cert';
                        }

                        $payment = $this->paymentRepository->createOrUpdate($paymentData, $meta_condition);
                        Order::where('id', $order->order_id)->update(['payment_id' => $payment->id]);


                    } elseif ($diff > 0) {
                        $productObjS = Product::join('ec_product_variations', 'ec_product_variations.product_id', 'ec_products.id')
                            ->where('ec_product_variations.configurable_product_id', $orderProduct->product_id)
                            ->where('ec_product_variations.is_default', 0)
                            ->first();

                        $isPack = 0;
                        $orderProductData = [
                            'order_id'     => $orderProduct->order_id,
                            'qty'          => $diff,
                            'is_pack'      => $isPack,
                            'price'        => $orderProduct->price,
                            'product_id'   => $productObjS ? $productObjS->product_id : $productObj->product_id,
                            'product_name' => $productObjS ? $productObjS->name : $productObj->name
                        ];
                        OrderProduct::create($orderProductData);

                        $meta_condition = ['order_id' => $order->order_id];
                        $paymentData = [
                            'amount'       => round($orderProduct->price * $productObj->prod_pieces, 2),
                            'currency'     => get_application_currency()->title,
                            'paypal_email' => '',
                            'status'       => PaymentStatusEnum::PENDING,
                            'payment_type' => 'confirm',
                            'order_id'     => $order->order_id,
                            'charge_id'    => Str::upper(Str::random(10))
                        ];

                        if ($order->payment_id == 41) {
                            $paymentData['payment_channel'] = 'omni-payment';
                        } elseif ($order->payment_id == 20) {
                            $paymentData['payment_channel'] = 'paypal';
                            $paymentData['paypal_email'] = $neworder->user->email;
                        } elseif ($order->payment_id == 36) {
                            $paymentData['payment_channel'] = 'showroom';
                        } elseif ($order->payment_id == 37) {
                            $paymentData['payment_channel'] = 'Split';
                        } elseif ($order->payment_id == 29) {
                            $paymentData['payment_channel'] = 'Store Credit';
                        } elseif ($order->payment_id == 17) {
                            $paymentData['payment_channel'] = 'COD';
                        } elseif ($order->payment_id == 6) {
                            $paymentData['payment_channel'] = 'Cash';
                        } elseif ($order->payment_id == 9) {
                            $paymentData['payment_channel'] = 'Business Check';
                        } elseif ($order->payment_id == 35) {
                            $paymentData['payment_channel'] = 'Gift Cert';
                        }

                        $payment = $this->paymentRepository->createOrUpdate($paymentData, $meta_condition);
                        Order::where('id', $order->order_id)->update(['payment_id' => $payment->id]);

                    }

                }

            }

            $custAddB = [
                'name'        => $order->b_firstname . ' ' . $order->b_lastname,
                'email'       => $order->email,
                'phone'       => $order->b_phone,
                'country'     => $order->b_country,
                'state'       => $order->b_state,
                'city'        => $order->b_city,
                'address'     => $order->b_address_2,
                'customer_id' => $order->user_id,
                'zip_code'    => $order->b_zipcode,
                'first_name'  => $order->b_firstname,
                'last_name'   => $order->b_lastname,
                'company'     => $order->company,
                'type'        => 'billing',
            ];
            $custBA = Address::create($custAddB);

            $billingAddress = [
                'name'                => $order->b_firstname . ' ' . $order->b_lastname,
                'phone'               => $order->b_phone,
                'email'               => $order->email,
                'country'             => $order->b_country,
                'state'               => $order->b_state,
                'city'                => $order->b_city,
                'address'             => $order->b_address_2,
                'order_id'            => $order->order_id,
                'zip_code'            => $order->b_zipcode,
                'type'                => 'billing',
                'customer_address_id' => $custBA->id,
            ];
            OrderAddress::create($billingAddress);


            $custAddS = [
                'name'        => $order->s_firstname . ' ' . $order->s_lastname,
                'email'       => $order->email,
                'phone'       => $order->s_phone,
                'country'     => $order->s_country,
                'state'       => $order->s_state,
                'city'        => $order->s_city,
                'address'     => $order->s_address_2,
                'customer_id' => $order->user_id,
                'zip_code'    => $order->s_zipcode,
                'first_name'  => $order->s_firstname,
                'last_name'   => $order->s_lastname,
                'company'     => $order->company,
                'type'        => 'shipping',
            ];
            $custSA = Address::create($custAddS);

            $shippingAddress = [
                'name'                => $order->s_firstname . ' ' . $order->s_lastname,
                'phone'               => $order->s_phone,
                'email'               => $order->email,
                'country'             => $order->s_country,
                'state'               => $order->s_state,
                'city'                => $order->s_city,
                'address'             => $order->s_address_2,
                'order_id'            => $order->order_id,
                'zip_code'            => $order->s_zipcode,
                'type'                => 'shipping',
                'customer_address_id' => $custSA->id,
            ];
            OrderAddress::create($shippingAddress);


            DB::connection('mysql2')->table('hw_orders')->where('order_id', $order->order_id)->update(['hw_orders.fetch_status' => 1]);


            $userOmniId = DB::connection('mysql2')->table('hw_users')->where('user_id', $order->user_id)->value('omni_customer_id');
            if ($userOmniId) {
                $data = ['customer_id' => $order->user_id, 'customer_omni_id' => $userOmniId];
                CustomerCard::updateOrCreate($data, $data);
            }
        }

        return $response
            ->setData($order)
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function fetchOldSystemCard($id, BaseHttpResponse $response)
    {
        $order = Order::where('id', $id)->first();
        $userOmniId = DB::connection('mysql2')->table('hw_users')->where('user_id', $order->user_id)->value('omni_customer_id');
        if ($userOmniId) {
            $data = ['customer_id' => $order->user_id, 'customer_omni_id' => $userOmniId];
            CustomerCard::updateOrCreate($data, $data);
        }
        return $response
            ->setData($order)
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function sendInvoice($id, BaseHttpResponse $response)
    {
        $order = Order::where('id', $id)->first();
        Mail::to($order->user->email)->send(new OrderCreate($order));
        return $response
            ->setData($order)
            ->setMessage('Invoice Sent');
    }

}
