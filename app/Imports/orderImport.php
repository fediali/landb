<?php

namespace App\Imports;

use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\CustomerDetail;
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
        $user = Customer::where(['phone' => $row['phone_number']])->first();
        if ($user == null) {
            //creating Customer
            $data = [];
            $data['name'] = $row['business_contact_name'];
            $data['email'] = $row['phone_number'];
            $data['password'] = bcrypt(rand(0, 15));
            $customer = Customer::create($data);
            $detail['customer_id'] = $customer->id;
            $detail['company'] = $row['business_company_name'];
            CustomerDetail::create($detail);
        }
        //Finding Product For Order

        $product = Product::where('sku', $row['style_no'])->first();
        //count pack quantity for product
        $pack = quantityCalculate($product->category_id);


        //creating Order

        dd($pack);


    }
}
