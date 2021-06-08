<?php

namespace Botble\Ecommerce\Http\Controllers;

use App\Events\OrderEdit;
use App\Imports\OrderImportFile;
use App\Models\CardPreAuth;
use App\Models\OrderImport;
use App\Models\OrderImportUpload;
use Assets;
use Botble\ACL\Models\Role;
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
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\OrderProductShipmentVerify;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderAddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Botble\Ecommerce\Repositories\Interfaces\StoreLocatorInterface;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleShippingFeeService;
use Botble\Ecommerce\Tables\OrderIncompleteTable;
use Botble\Ecommerce\Tables\OrderTable;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
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
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use OrderHelper;
use RvMedia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;



use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;
use Twilio\Rest\Client;


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
        AddressInterface $addressRepository
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
        if ($request->input('order_id') && $request->input('order_id') > 0) {
            $condition = ['id' => $request->input('order_id')];
            $meta_condition = ['order_id' => $request->input('order_id')];

            $order_products = OrderProduct::where('order_id', $request->input('order_id'))->get();
            foreach ($order_products as $order_product) {
                $this->productRepository
                    ->getModel()
                    ->where('id', $order_product->product_id)
                    ->where('with_storehouse_management', 1)
                    ->increment('quantity', $order_product->qty);
            }
            OrderProduct::where('order_id', $request->input('order_id'))->delete();
        }

        foreach ($request->input('products', []) as $productItem) {
            $product = $this->productRepository->findById(Arr::get($productItem, 'id'));
            if (!$product) {
                continue;
            }
            $demandQty = Arr::get($productItem, 'quantity', 1);

            if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES) {
                $stockQty = $product->online_sales_qty;
            } elseif (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES) {
                $stockQty = $product->in_person_sales_qty;
            } else {
                $stockQty = $product->quantity;
            }

            if ($request->input('order_type') != Order::PRE_ORDER) {
                if ($stockQty < $demandQty) {
                    return $response->setCode(406)->setError()->setMessage($product->sku . ' is not available in this Qty!');
                }
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
            'status'               => OrderStatusEnum::PROCESSING,
            'order_type'           => $request->input('order_type'),
        ]);

        $order = $this->orderRepository->createOrUpdate($request->input(), $condition);

        if ($order) {

            $this->orderHistoryRepository->createOrUpdate([
                'action'      => 'create_order_from_payment_page',
                'description' => trans('plugins/ecommerce::order.create_order_from_payment_page'),
                'order_id'    => $order->id,
            ], $meta_condition);

            $this->orderHistoryRepository->createOrUpdate([
                'action'      => 'create_order',
                'description' => trans('plugins/ecommerce::order.new_order',
                    ['order_id' => get_order_code($order->id)]),
                'order_id'    => $order->id,
            ], $meta_condition);

            $this->orderHistoryRepository->createOrUpdate([
                'action'      => 'confirm_order',
                'description' => trans('plugins/ecommerce::order.order_was_verified_by'),
                'order_id'    => $order->id,
                'user_id'     => Auth::user()->getKey(),
            ], $meta_condition);

            $payment = $this->paymentRepository->createOrUpdate([
                'amount'          => $order->amount,
                'currency'        => get_application_currency()->title,
                'payment_channel' => $order->payment->payment_channel,
                'status'          => $request->input('payment_status', PaymentStatusEnum::PENDING),
                'payment_type'    => 'confirm',
                'order_id'        => $order->id,
                'charge_id'       => Str::upper(Str::random(10)),
            ], $meta_condition);

            $order->payment_id = $payment->id;

            $order->editing_by = NULL;
            $order->editing_started_at = NULL;

            $order->is_finished = 1;
            $order->salesperson_id = auth()->user()->id;

            $order->save();

            if ($request->input('payment_status') === PaymentStatusEnum::COMPLETED) {
                $this->orderHistoryRepository->createOrUpdate([
                    'action'      => 'confirm_payment',
                    'description' => trans('plugins/ecommerce::order.payment_was_confirmed_by', [
                        'money' => format_price($order->amount, $order->currency_id),
                    ]),
                    'order_id'    => $order->id,
                    'user_id'     => Auth::user()->getKey(),
                ], $meta_condition);
            }

            if ($request->input('customer_address.name')) {
                $this->orderAddressRepository->createOrUpdate([
                    'name'     => $request->input('customer_address.name'),
                    'phone'    => $request->input('customer_address.phone'),
                    'email'    => $request->input('customer_address.email'),
                    'state'    => $request->input('customer_address.state'),
                    'city'     => $request->input('customer_address.city'),
                    'zip_code' => $request->input('customer_address.zip_code'),
                    'country'  => $request->input('customer_address.country'),
                    'address'  => $request->input('customer_address.address'),
                    'order_id' => $order->id,
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

            foreach ($request->input('products', []) as $productItem) {
                $product = $this->productRepository->findById(Arr::get($productItem, 'id'));
                if (!$product) {
                    continue;
                }

                $data = [
                    'order_id'     => $order->id,
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'qty'          => Arr::get($productItem, 'quantity', 1),
                    'weight'       => $product->weight,
                    'price'        => $product->front_sale_price,
                    'tax_amount'   => 0,
                    'options'      => [],
                ];

                $this->orderProductRepository->create($data);

                $preQty = $product->quantity;

                if ($order->order_type == Order::NORMAL) {
                    $this->productRepository
                        ->getModel()
                        ->where('id', $product->id)
                        ->where('with_storehouse_management', 1)
                        ->where('quantity', '>', 0)
                        ->decrement('quantity', Arr::get($productItem, 'quantity', 1));

                    if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES || @auth()->user()->roles[0]->slug == Role::ADMIN) {
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
                    }

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
        if (!$order->user->card->isEmpty()) {
            $url = (env("OMNI_URL") . "customer/" . $order->user->card[0]->customer_omni_id . "/payment-method");
            list($card, $info) = omni_api($url);
            $cards = collect(json_decode($card))->pluck('nickname', 'id')->push('Add New Card');
        }

        return view('plugins/ecommerce::orders.edit', compact('order', 'weight', 'defaultStore', 'cards'));
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

        $this->orderRepository->createOrUpdate(['status' => OrderStatusEnum::CANCELED, 'is_confirmed' => true],
            compact('id'));

        $this->orderHistoryRepository->createOrUpdate([
            'action'      => 'cancel_order',
            'description' => trans('plugins/ecommerce::order.order_was_canceled_by'),
            'order_id'    => $order->id,
            'user_id'     => Auth::user()->getKey(),
        ]);

        $mailer = EmailHandler::setModule(ECOMMERCE_MODULE_SCREEN_NAME);
        if ($mailer->templateEnabled('customer_cancel_order')) {
            OrderHelper::setEmailVariables($order);
            $mailer->sendUsingTemplate(
                'customer_cancel_order',
                $order->user->email ? $order->user->email : $order->address->email
            );
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

        page_title()->setTitle(trans('plugins/ecommerce::order.reorder'));

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
            $availableProduct->image_url = RvMedia::getImageUrl(Arr::first($availableProduct->images) ?? null, 'thumb',
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

        $order->editing_by = auth()->user()->id;
        $order->editing_started_at = Carbon::now();
        $order->save();

        return view('plugins/ecommerce::orders.reorder', compact(
            'order',
            'products',
            'productIds',
            'customer',
            'customerAddresses',
            'customerAddress',
            'customerOrderNumbers'
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
            $availableProduct->image_url = RvMedia::getImageUrl(Arr::first($availableProduct->images) ?? null, 'thumb',
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
            $type = strtolower($request['file']->getClientOriginalExtension());
            $image = str_replace(' ', '_', rand(1, 100) . '_' . substr(microtime(), 2, 7)) . '.' . $type;
            $spec_file_name = time() . rand(1, 100) . '.' . $type;
            $move = $request->file('file')->move(public_path('storage/importorders'), $spec_file_name);
            $order = Excel::toCollection(new OrderImportFile(), $move);

            $upload = OrderImportUpload::create(['file' => $move]);

            $errors = [];

            if ($request->market_place == Order::LASHOWROOM) {
                foreach ($order as $od) {
                    foreach ($od as $row) {
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
                        $product = Product::where('sku', $row['style_no'])->first();

                        if ($product) {
                            //count pack quantity for product
                            $pack = quantityCalculate($product['category_id']);
                            $orderQuantity = $row['original_qty'] / $pack;

                            if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES) {
                                if ($orderQuantity <= $product->online_sales_qty) {
                                    $checkProdQty = true;
                                } elseif ($product->online_sales_qty > 0) {
                                    $remQty = $orderQuantity - $product->online_sales_qty;
                                    $orderQuantity = $product->online_sales_qty;
                                    $errors[] = $row['style_no'] . ' product is short in ' . $remQty . ' quantity.';
                                }
                            } elseif (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES) {
                                if ($orderQuantity <= $product->in_person_sales_qty) {
                                    $checkProdQty = true;
                                } elseif ($product->in_person_sales_qty > 0) {
                                    $remQty = $orderQuantity - $product->in_person_sales_qty;
                                    $orderQuantity = $product->in_person_sales_qty;
                                    $errors[] = $row['style_no'] . ' product is short in ' . $remQty . ' quantity.';
                                }
                            } else {
                                if ($orderQuantity <= $product->quantity) {
                                    $checkProdQty = true;
                                } elseif ($product->quantity > 0) {
                                    $remQty = $orderQuantity - $product->quantity;
                                    $orderQuantity = $product->quantity;
                                    $errors[] = $row['style_no'] . ' product is short in ' . $remQty . ' quantity.';
                                }
                            }

                        } else {
                            $errors[] = $row['style_no'] . ' product is not found.';
                        }

                        if (!$checkProdQty && $product) {
                            $errors[] = $row['style_no'] . ' product is out of stock.';
                        }

                        $orderPo = DB::table('ec_order_import')->where('po_number', $row['po'])->first();

                        if ($orderPo != null && $product && $checkProdQty) {
                            $detail['order_id'] = $orderPo->order_id;
                            $detail['qty'] = $orderQuantity;
                            $detail['price'] = intval(str_replace('$', '', $row['sub_total'])) / $orderQuantity;
                            $detail['product_id'] = $product->id;
                            $detail['product_name'] = $product->name;
                            $importOrder = OrderProduct::create($detail);
                            //import record
                        } else if ($product) {
                            $iorder['user_id'] = $customer->id;
                            $iorder['amount'] = str_replace('$', '', $row['original_amount']);;
                            $iorder['currency_id'] = 1;
                            $iorder['is_confirmed'] = 1;
                            $iorder['is_finished'] = 1;
                            $iorder['status'] = OrderStatusEnum::PROCESSING;
                            $importOrder = Order::create($iorder);
                            if ($importOrder && $product && $checkProdQty) {
                                $detail['order_id'] = $importOrder->id;
                                $detail['qty'] = $orderQuantity;
                                $detail['price'] = intval(str_replace('$', '', $row['sub_total'])) / $orderQuantity;
                                $detail['product_id'] = $product->id;
                                $detail['product_name'] = $product->name;
                                $orderProduct = OrderProduct::create($detail);
                                if ($orderProduct) {
                                    $orderInfo['order_id'] = $importOrder->id;
                                    $orderInfo['po_number'] = $row['po'];
                                    $orderInfo['order_date'] = $row['order_date'];
                                    $orderInfo['type'] = Order::LASHOWROOM;
                                    $orderInfo['order_import_upload_id'] = $upload->id;

                                    $upload_id = OrderImport::create($orderInfo);
                                }
                            }

                        }
                    }
                }
            } elseif ($request->market_place == Order::ORANGESHINE) {
                foreach ($order as $od) {
                    foreach ($od as $row) {
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
                        $product = Product::where('sku', $row['style'])->first();
                        if ($product) {
                            //count pack quantity for product
                            $pack = quantityCalculate($product['category_id']);
                            $orderQuantity = $row['total_qty'] / $pack;

                            if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES) {
                                if ($orderQuantity <= $product->online_sales_qty) {
                                    $checkProdQty = true;
                                } elseif ($product->online_sales_qty > 0) {
                                    $remQty = $orderQuantity - $product->online_sales_qty;
                                    $orderQuantity = $product->online_sales_qty;
                                    $errors[] = $row['style'] . ' product is short in ' . $remQty . ' quantity.';
                                }
                            } elseif (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES) {
                                if ($orderQuantity <= $product->in_person_sales_qty) {
                                    $checkProdQty = true;
                                } elseif ($product->in_person_sales_qty > 0) {
                                    $remQty = $orderQuantity - $product->in_person_sales_qty;
                                    $orderQuantity = $product->in_person_sales_qty;
                                    $errors[] = $row['style'] . ' product is short in ' . $remQty . ' quantity.';
                                }
                            } else {
                                if ($orderQuantity <= $product->quantity) {
                                    $checkProdQty = true;
                                } elseif ($product->quantity > 0) {
                                    $remQty = $orderQuantity - $product->quantity;
                                    $orderQuantity = $product->quantity;
                                    $errors[] = $row['style'] . ' product is short in ' . $remQty . ' quantity.';
                                }
                            }

                        } else {
                            $errors[] = $row['style'] . ' product is not found.';
                        }

                        if (!$checkProdQty && $product) {
                            $errors[] = $row['style'] . ' product is out of stock.';
                        }

                        $orderPo = DB::table('ec_order_import')->where('po_number', $row['invoice'])->first();
                        if ($orderPo != null && $product && $checkProdQty) {
                            $detail['order_id'] = $orderPo->order_id;
                            $detail['qty'] = $orderQuantity;
                            $detail['price'] = str_replace('$', '', $row['sub_total']) / $orderQuantity;
                            $detail['product_id'] = $product->id;
                            $detail['product_name'] = $product->name;
                            $importOrder = OrderProduct::create($detail);
                            //import record
                        } else if ($product) {
                            $iorder['user_id'] = $customer->id;
                            $iorder['amount'] = str_replace('$', '', $row['order_amt']);;
                            $iorder['currency_id'] = 1;
                            $iorder['is_confirmed'] = 1;
                            $iorder['is_finished'] = 1;
                            $iorder['status'] = OrderStatusEnum::PROCESSING;
                            $importOrder = Order::create($iorder);
                            if ($importOrder && $product && $checkProdQty) {
                                $detail['order_id'] = $importOrder->id;
                                $detail['qty'] = $orderQuantity;
                                $detail['price'] = str_replace('$', '', $row['sub_total']) / $orderQuantity;
                                $detail['product_id'] = $product->id;
                                $detail['product_name'] = $product->name;
                                $orderProduct = OrderProduct::create($detail);
                                if ($orderProduct) {
                                    $orderInfo['order_id'] = $importOrder->id;
                                    $orderInfo['po_number'] = $row['invoice'];
                                    $orderInfo['order_date'] = $row['order_date'];
                                    $orderInfo['type'] = Order::ORANGESHINE;
                                    $orderInfo['order_import_upload_id'] = $upload->id;

                                    $upload_id = OrderImport::create($orderInfo);
                                }
                            }

                        }
                    }
                }
            } else {
                foreach ($order as $od) {

                    foreach ($od as $row) {
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
                        $product = Product::where('sku', $row['styleno'])->first();
                        if ($product) {
                            //count pack quantity for product
                            $pack = quantityCalculate($product['category_id']);
                            $orderQuantity = $row['totalqty'] / $pack;

                            if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES) {
                                if ($orderQuantity <= $product->online_sales_qty) {
                                    $checkProdQty = true;
                                } elseif ($product->online_sales_qty > 0) {
                                    $remQty = $orderQuantity - $product->online_sales_qty;
                                    $orderQuantity = $product->online_sales_qty;
                                    $errors[] = $row['styleno'] . ' product is short in ' . $remQty . ' quantity.';
                                }
                            } elseif (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES) {
                                if ($orderQuantity <= $product->in_person_sales_qty) {
                                    $checkProdQty = true;
                                } elseif ($product->in_person_sales_qty > 0) {
                                    $remQty = $orderQuantity - $product->in_person_sales_qty;
                                    $orderQuantity = $product->in_person_sales_qty;
                                    $errors[] = $row['styleno'] . ' product is short in ' . $remQty . ' quantity.';
                                }
                            } else {
                                if ($orderQuantity <= $product->quantity) {
                                    $checkProdQty = true;
                                } elseif ($product->quantity > 0) {
                                    $remQty = $orderQuantity - $product->quantity;
                                    $orderQuantity = $product->quantity;
                                    $errors[] = $row['styleno'] . ' product is short in ' . $remQty . ' quantity.';
                                }
                            }

                        } else {
                            $errors[] = $row['styleno'] . ' product is not found.';
                        }

                        if (!$checkProdQty && $product) {
                            $errors[] = $row['styleno'] . ' product is out of stock.';
                        }

                        $orderPo = DB::table('ec_order_import')->where('po_number', $row['ponumber'])->first();
                        if ($orderPo != null && $product && $checkProdQty) {
                            $detail['order_id'] = $orderPo->order_id;
                            $detail['qty'] = $orderQuantity;
                            $detail['price'] = str_replace('$', '', $row['subtotal']) / $orderQuantity;
                            $detail['product_id'] = $product->id;
                            $detail['product_name'] = $product->name;
                            $importOrder = OrderProduct::create($detail);
                            //import record
                        } else if ($product) {
                            $iorder['user_id'] = $customer->id;
                            $iorder['amount'] = str_replace('$', '', $row['totalamount']);;
                            $iorder['currency_id'] = 1;
                            $iorder['is_confirmed'] = 1;
                            $iorder['is_finished'] = 1;
                            $iorder['status'] = OrderStatusEnum::PROCESSING;
                            $importOrder = Order::create($iorder);
                            if ($importOrder && $product && $checkProdQty) {
                                $detail['order_id'] = $importOrder->id;
                                $detail['qty'] = $orderQuantity;
                                $detail['price'] = str_replace('$', '', $row['subtotal']) / $orderQuantity;
                                $detail['product_id'] = $product->id;
                                $detail['product_name'] = $product->name;
                                $orderProduct = OrderProduct::create($detail);
                                if ($orderProduct) {
                                    $orderInfo['order_id'] = $importOrder->id;
                                    $orderInfo['po_number'] = $row['ponumber'];
                                    $orderInfo['order_date'] = $row['orderdate'];
                                    $orderInfo['type'] = Order::FASHIONGO;
                                    $orderInfo['order_import_upload_id'] = $upload->id;
                                    $upload_id = OrderImport::create($orderInfo);
                                }
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
            CardPreAuth::where('transaction_id', $request->transaction_id)->update($order);
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

        $order->fill($requestData);

        event(new UpdatedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $order));
        $this->orderRepository->createOrUpdate($order);
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



    public function chatRoom(Request $request)
    {
        page_title()->setTitle('Chat Room');
        $customers = get_customers();
        return view('plugins/ecommerce::orders.chatRoom', compact('customers'));
    }

    public function chatMessage(Request $request, $ids)
    {
        $authUser = $request->user();
        $otherUser = Customer::find(explode('-', $ids)[1]);
        $customers = Customer::where('id', '<>', $authUser->id)->get();

        $twilio = new Client(env('TWILIO_AUTH_SID'), env('TWILIO_AUTH_TOKEN'));

        // Fetch channel or create a new one if it doesn't exist
        try {
            $channel = $twilio->chat->v2->services(env('TWILIO_SERVICE_SID'))->channels($ids)->fetch();
        } catch (\Twilio\Exceptions\RestException $e) {
            $channel = $twilio->chat->v2->services(env('TWILIO_SERVICE_SID'))->channels->create(['uniqueName' => $ids, 'type' => 'private']);
        }

        // Add first user to the channel
        try {
            $twilio->chat->v2->services(env('TWILIO_SERVICE_SID'))->channels($ids)->members($authUser->email)->fetch();
        } catch (\Twilio\Exceptions\RestException $e) {
            $twilio->chat->v2->services(env('TWILIO_SERVICE_SID'))->channels($ids)->members->create($authUser->email);
        }

        // Add second user to the channel
        try {
            $twilio->chat->v2->services(env('TWILIO_SERVICE_SID'))->channels($ids)->members($otherUser->email)->fetch();
        } catch (\Twilio\Exceptions\RestException $e) {
            $twilio->chat->v2->services(env('TWILIO_SERVICE_SID'))->channels($ids)->members->create($otherUser->email);
        }

        return view('plugins/ecommerce::orders.chatMessage', compact('customers', 'otherUser'));
    }

    public function generate(Request $request)
    {
        $token = new AccessToken( env('TWILIO_AUTH_SID'), env('TWILIO_API_SID'), env('TWILIO_API_SECRET'), 3600, $request->email);

        $chatGrant = new ChatGrant();
        $chatGrant->setServiceSid(env('TWILIO_SERVICE_SID'));
        $token->addGrant($chatGrant);

        return response()->json([ 'token' => $token->toJWT() ]);
    }
}
