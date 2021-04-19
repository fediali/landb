<?php

namespace App\Imports;

use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\CustomerDetail;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Eloquent\CustomerRepository;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Laravel\Passport\Bridge\UserRepository;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrderImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */


    public function __construct()
    {

    }

    public function model(array $row)
    {
//checking customer

        $customer = Customer::where(['phone' => $row['phone_number']])->first();
        if ($customer == null) {
            //creating Customer
            $data['name'] = $row['business_contact_name'];
            $data['email'] = rand(1, 5) . '@lashowroomcustomer.com';
            $data['phone'] = '+56806593174691';
            $data['password'] = bcrypt(rand(0, 15));

            create_customer($data);
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
            $baddress['name'] = $row['shipping_contact_name'];

            $shipping = Address::create($saddress);

        }

        //Finding Product For Order

        $product = Product::where('sku', $row['style_no'])->first();

        //count pack quantity for product
        $pack = quantityCalculate($product['category_id']);

        $orderQuantity = $row['original_qty'] / $pack;
        $orderPo = DB::table('ec_order_import')->where('po_number', $row['po'])->get();

        if ($orderPo->count() > 0) {
            $detail['order_id'] = $orderPo->order_id;
            $detail['qty'] = $pack;
            $detail['price'] = $row['sub_total'];
            $detail['product_id'] = $product->id;
            $detail['product_name'] = $product->name;
            OrderProduct::create($detail);
            //import record
        } else {

            $order['customer_id'] = $customer->id;
            $order['amount'] = $row['original_amount'];
            $order['currency_id'] = $customer->id;
            $order['is_confirmed'] = 1;
            $order['is_finished'] = 1;

            $importOrder = Order::create($order);
            $detail['order_id'] = $importOrder->id;
            $detail['qty'] = $pack;
            $detail['price'] = $row['sub_total'];
            $detail['product_id'] = $product->id;
            $detail['product_name'] = $product->name;
            OrderProduct::create($detail);
            //import record
            $orderInfo['order_id'] = $importOrder->id;
            $orderInfo['po_number'] = $row['po'];
            $orderInfo['order_date'] = $row['order_date'];

        }
        //creating Order

        dd('ss');
    }
}
