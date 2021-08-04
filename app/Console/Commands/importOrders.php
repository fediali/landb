<?php

namespace App\Console\Commands;

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
        DB::table('hw_orders')->orderBy('hw_orders.order_id','ASC')->chunk(100, function ($orders) {
            foreach ($orders as $order) {
                $orderData = [
                    'id' => $order->order_id,
                    'user_id' => $order->user_id,
                    'status' => $order->status,
                    'order_type' => Order::NORMAL,
                    'platform' => $order->location ? $order->location : ($order->online_order ? 'online' : 'backorder'),
                    'amount' => $order->total,
                    'currency_id' => 1,
                    'tax_amount' => 0,
                    'shipping_amount' => $order->shipping_cost,
                    'description' => $order->notes,
                    'discount_amount' => $order->subtotal_discount,
                    'sub_total' => $order->subtotal,
                    'is_confirmed' => 1,
                    'is_finished' => 1,
                    'salesperson_id' => $order->issuer_id,
                    'po_number' => $order->po_number,
                ];
                Order::create($orderData);

                $orderProducts = DB::table('hw_order_details')->where('order_id', $order->order_id)->get();
                foreach ($orderProducts as $orderProduct) {
                    $productName = Product::where('id', $orderProduct->product_id)->value('name');
                    $orderProductData = [
                        'order_id' => $orderProduct->order_id,
                        'qty' => $orderProduct->amount,
                        'price' => $orderProduct->price,
                        'product_id' => $orderProduct->product_id,
                        'product_name' => $productName ? $productName : $orderProduct->product_code
                    ];
                    OrderProduct::create($orderProductData);
                }

                $custAddB = [
                    'name' => $order->b_firstname.' '.$order->b_lastname,
                    'email' => $order->email,
                    'phone' => $order->b_phone,
                    'country' => $order->b_country,
                    'state' => $order->b_state,
                    'city' => $order->b_city,
                    'address' => $order->b_address_2,
                    'customer_id' => $order->user_id,
                    'zip_code' => $order->b_zipcode,
                    'first_name' => $order->b_firstname,
                    'last_name' => $order->b_lastname,
                    'company' => $order->company,
                    'type' => 'billing',
                ];
                $custBA = Address::create($custAddB);

                $billingAddress = [
                    'name' => $order->b_firstname.' '.$order->b_lastname,
                    'phone' => $order->b_phone,
                    'email' => $order->email,
                    'country' => $order->b_country,
                    'state' => $order->b_state,
                    'city' => $order->b_city,
                    'address' => $order->b_address_2,
                    'order_id' => $order->order_id,
                    'zip_code' => $order->b_zipcode,
                    'type' => 'billing',
                    'customer_address_id' => $custBA->id,
                ];
                OrderAddress::create($billingAddress);


                $custAddS = [
                    'name' => $order->s_firstname.' '.$order->s_lastname,
                    'email' => $order->email,
                    'phone' => $order->s_phone,
                    'country' => $order->s_country,
                    'state' => $order->s_state,
                    'city' => $order->s_city,
                    'address' => $order->s_address_2,
                    'customer_id' => $order->user_id,
                    'zip_code' => $order->s_zipcode,
                    'first_name' => $order->s_firstname,
                    'last_name' => $order->s_lastname,
                    'company' => $order->company,
                    'type' => 'shipping',
                ];
                $custSA = Address::create($custAddS);

                $shippingAddress = [
                    'name' => $order->s_firstname.' '.$order->s_lastname,
                    'phone' => $order->s_phone,
                    'email' => $order->email,
                    'country' => $order->s_country,
                    'state' => $order->s_state,
                    'city' => $order->s_city,
                    'address' => $order->s_address_2,
                    'order_id' => $order->order_id,
                    'zip_code' => $order->s_zipcode,
                    'type' => 'shipping',
                    'customer_address_id' => $custSA->id,
                ];
                OrderAddress::create($shippingAddress);

            }
        });

        echo 'success';
    }
}
