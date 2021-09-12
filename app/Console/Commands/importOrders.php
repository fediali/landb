<?php

namespace App\Console\Commands;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class importOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('ec_order_addresses')->truncate();
        DB::table('ec_order_histories')->truncate();
        DB::table('ec_order_product')->truncate();
        DB::table('ec_orders')->truncate();
        $orders = DB::table('hw_orders')->orderBy('hw_orders.order_id', 'ASC')->chunk(100,
 function ($orders) {
        foreach ($orders as $order) {
            echo $order->order_id;
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
            } else {
                $order->status = 'Failed Preauth';
            }
            $orderData = [
                'id'              => $order->order_id,
                'user_id'         => $order->user_id,
                'status'          => $order->status,
                'order_type'      => ($order->status == 'B') ? Order::PRE_ORDER: Order::NORMAL,
                'platform'        => $order->location ? $order->location : ($order->online_order ? 'online' : 'backorder'),
                'amount'          => $order->total,
                'currency_id'     => 1,
                'tax_amount'      => 0,
                'shipping_amount' => $order->shipping_cost,
                'description'     => $order->notes,
                'discount_amount' => $order->subtotal_discount,
                'sub_total'       => $order->subtotal,
                'is_confirmed'    => 1,
                'is_finished'     => 1,
                'salesperson_id'  => $order->issuer_id,
                'po_number'       => $order->po_number,
            ];
            Order::create($orderData);

            $orderProducts = DB::table('hw_order_details')->where('order_id', $order->order_id)->get();
            foreach ($orderProducts as $orderProduct) {
                //$productName = Product::where('id', $orderProduct->product_id)->value('name');
                $productObj = Product::where('id', $orderProduct->product_id)->first();
                $diff = 0;
                $isPack = 0;
                $qty = $orderProduct->amount;
                if ($productObj && $productObj->prod_pieces) {
                    $isPack = 1;
                    $packQty = floor($qty / $productObj->prod_pieces);
                    $qty = $packQty;

                    $looseQty = $packQty * $productObj->prod_pieces;
                    $diff = $qty - $looseQty;
                }
                $orderProductData = [
                    'order_id'     => $orderProduct->order_id,
                    'qty'          => $qty,
                    'is_pack'      => $isPack,
                    'price'        => $orderProduct->price,
                    'product_id'   => $orderProduct->product_id,
                    'product_name' => $productObj ? $productObj->name : $orderProduct->product_code
                ];
                OrderProduct::create($orderProductData);

                if ($diff) {
                    $isPack = 0;
                    $orderProductData = [
                        'order_id'     => $orderProduct->order_id,
                        'qty'          => $diff,
                        'is_pack'      => $isPack,
                        'price'        => $orderProduct->price,
                        'product_id'   => $orderProduct->product_id,
                        'product_name' => $productObj ? $productObj->name : $orderProduct->product_code
                    ];
                    OrderProduct::create($orderProductData);
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

        }
        });

        echo 'success';
    }
}
