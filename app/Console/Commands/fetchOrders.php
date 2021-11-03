<?php

namespace App\Console\Commands;

use App\Models\CustomerCard;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class fetchOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $paymentRepository;

    public function __construct(PaymentInterface $paymentRepository)
    {
        parent::__construct();
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//
//        DB::table('ec_order_addresses')->truncate();
//        DB::table('ec_order_histories')->truncate();
//        DB::table('ec_order_product')->truncate();
//        DB::table('ec_orders')->truncate();
        $meta_condition = [];
        DB::connection('mysql2')->table('hw_orders')->where([/*'hw_orders.fetch_status' => 0, 'status' => 'AJ'*/'hw_orders.order_id' => 120592])->orderBy('hw_orders.order_id', 'ASC')->chunk(500,
            function ($orders) {
                foreach ($orders as $order) {
                    echo $order->order_id;
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
                    } else if ($order->status == 'B') {
                        $pre = 1;
                    } else {
                        $order->status = 'Failed Preauth';
                    }

                    $orderData = [
                        'id'                    => $order->order_id,
                        'user_id'               => $order->user_id,
                        'status'                => $order->status,
                        'order_type'            => ($pre) ? Order::PRE_ORDER : Order::NORMAL,
                        'platform'              => $order->location ? $order->location : ($order->online_order ? 'online' : 'backorder'),
                        'amount'                => $order->total,
                        'currency_id'           => 1,
                        'tax_amount'            => 0,
                        'shipping_amount'       => $order->shipping_cost,
                        'description'           => $order->notes,
                        'discount_amount'       => $order->subtotal_discount,
                        'sub_total'             => $order->subtotal,
                        'is_confirmed'          => 1,
                        'is_finished'           => 1,
                        'salesperson_id'        => $order->issuer_id,
                        'po_number'             => $order->po_number,
                        'order_completion_date' => $order->complete_date,
                        'created_at'            => date('Y-m-d H:i:s', $order->timestamp),
                        'updated_at'            => date('Y-m-d H:i:s', $order->last_status_change_date),
                    ];
                    DB::table('ec_orders')->insert($orderData);

                    $orderProducts = DB::connection('mysql2')->table('hw_order_details')->where('order_id', $order->order_id)->get();
                    foreach ($orderProducts as $orderProduct) {
                        //$productName = Product::where('id', $orderProduct->product_id)->value('name');

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

                                if ($order->payment_id == 41) {
                                    $meta_condition = ['order_id' => $order->order_id];
                                    $payment = $this->paymentRepository->createOrUpdate([
                                        'amount'          => round($orderProduct->price * $productObj->prod_pieces, 2),
                                        'currency'        => get_application_currency()->title,
                                        'payment_channel' => 'omni-payment', //$order->payment->payment_channel,
                                        'paypal_email'    => '',
                                        'status'          => PaymentStatusEnum::PENDING,
                                        'payment_type'    => 'confirm',
                                        'order_id'        => $order->order_id,
                                        'charge_id'       => Str::upper(Str::random(10)),
                                    ], $meta_condition);
                                    Order::where('id', $order->order_id)->update(['payment_id', $payment->id]);
                                }



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
                                if ($order->payment_id == 41) {
                                    $meta_condition = ['order_id' => $order->order_id];
                                    $payment = $this->paymentRepository->createOrUpdate([
                                        'amount'          => $orderProduct->price,
                                        'currency'        => get_application_currency()->title,
                                        'payment_channel' => 'omni-payment', //$order->payment->payment_channel,
                                        'paypal_email'    => '',
                                        'status'          => PaymentStatusEnum::PENDING,
                                        'payment_type'    => 'confirm',
                                        'order_id'        => $order->order_id,
                                        'charge_id'       => Str::upper(Str::random(10)),
                                    ], $meta_condition);
                                    Order::where('id', $order->order_id)->update(['payment_id', $payment->id]);
                                }


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


                    DB::connection('mysql2')->table('hw_orders')->where('hw_orders.fetch_status', 0)->where('order_id', $order->order_id)->update(['hw_orders.fetch_status' => 1]);


                    $userOmniId = DB::connection('mysql2')->table('hw_users')->where('user_id', $order->user_id)->value('omni_customer_id');
                    if ($userOmniId) {
                        $data = ['customer_id' => $order->user_id, 'customer_omni_id' => $userOmniId];
                        CustomerCard::updateOrCreate($data, $data);
                    }

                }
            });

        echo 'success';
    }
}
