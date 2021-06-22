<?php

namespace App\Imports;

use App\Models\CustomerAddress;
use App\Models\CustomerCard;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\CustomerDetail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use SlugHelper;


class ImportCustomers implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if ($row['user_id']) {
            $check = Customer::where('id', $row['user_id'])->first();
            if (!$check) {
                $customerData = [
                    'id' => $row['user_id'],
                    'email' => $row['email'],
                    'name' => $row['firstname'].' '.$row['lastname'],
                    'password' => $row['password'],
                    'avatar' => $row['profile_image'],
                    'phone' => $row['phone'],
                    'first_name' => $row['firstname'],
                    'last_name' => $row['lastname'],
                    'salesperson_id' => $row['srep_id'],
                    'login_status' => $row['status'],
                ];
                Customer::create($customerData);

                CustomerCard::create(['customer_id' => $row['user_id'], 'customer_omni_id' => $row['omni_customer_id']]);

                $customerDetailData = [
                    'customer_id' => $row['user_id'],
                    'sales_tax_id' => $row['sales_tax_id'],
                    'first_name' => $row['firstname'],
                    'last_name' => $row['lastname'],
                    'business_phone' => $row['phone'],
                    'company' => $row['company'],
                    'phone' => $row['phone'],
                ];
                CustomerDetail::create($customerDetailData);

                $customerAddress = [
                    'name' => $row['b_firstname'].' '.$row['b_lastname'],
                    'email' => $row['email'],
                    'phone' => $row['b_phone'],
                    'country' => $row['b_country'],
                    'city' => $row['b_city'],
                    'address' => $row['b_address'],
                    'customer_id' => $row['user_id'],
                    'first_name' => $row['b_firstname'],
                    'last_name' => $row['b_lastname'],
                    'company' => $row['company'],
                    'type' => 'billing',
                ];
                CustomerAddress::create($customerAddress);

                $customerAddressS = [
                    'name' => $row['s_firstname'].' '.$row['s_lastname'],
                    'email' => $row['email'],
                    'phone' => $row['s_phone'],
                    'country' => $row['s_country'],
                    'city' => $row['s_city'],
                    'address' => $row['s_address'],
                    'customer_id' => $row['user_id'],
                    'first_name' => $row['s_firstname'],
                    'last_name' => $row['s_lastname'],
                    'company' => $row['company'],
                    'type' => 'shipping',
                ];
                CustomerAddress::create($customerAddressS);
            }
        }
    }
}
