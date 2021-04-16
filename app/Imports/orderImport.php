<?php

namespace App\Imports;

use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\CustomerDetail;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Eloquent\CustomerRepository;
use Laravel\Passport\Bridge\UserRepository;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class orderImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected $customerRepository;

    public function __construct()
    {
        // $this->customerRepository = $customerRepo;
    }

    public function model(array $row)
    {
//checking customer
        dd($row);
        $customer = Customer::where(['phone' => $row['phone_number']])->first();
        if ($customer == null) {
            //creating Customer
            $data = [];
            $data['name'] = $row['business_contact_name'];
            $data['email'] = $row['phone_number'];
            $data['password'] = bcrypt(rand(0, 15));
            $customer = Customer::create($data);
            $detail['customer_id'] = $customer->id;
            $detail['company'] = $row['business_company_name'];
            $detail['type'] = Order::LASHOWROOM;
            CustomerDetail::create($detail);
            //creating address
            $baddress['billing_address'] = $row['address'];
            $baddress['billing_city'] = $row['address'];
            $baddress['billing_state'] = $row['address'];
            $baddress['billing_zip_code'] = $row['address'];
            $baddress['billing_county'] = $row['address'];

            $billing = Address::create($baddress);

            $saddress['shipping_address'] = $row['address'];
            $saddress['shipping_city'] = $row['address'];
            $saddress['shipping_state'] = $row['address'];
            $saddress['shipping_zip_code'] = $row['address'];
            $saddress['shipping_country'] = $row['address'];

            $shipping = Address::create($saddress);

        }
        //Finding Product For Order

        $product = Product::where('sku', $row['style_no'])->first();
        //count pack quantity for product
        $pack = quantityCalculate($product->category_id);
        $orderQuantity = $row['original_qty'] / $pack;

        //creating Order
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


        dd($pack);


    }
}
