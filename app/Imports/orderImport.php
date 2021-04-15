<?php

namespace App\Imports;

use Botble\Ecommerce\Models\Customer;
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

        $user = Customer::where(['phone' => $row['phone_number']])->first();
        if ($user == null) {
            $data = [];
            $data['name'] = $row['business_contact_name'];
            $data['email'] = $row['phone_number'];
            $data['password'] = $row['business_contact_name'];
            $customer = Customer::create($data);


            $data[''] = $row['business_contact_name'];
            $data[''] = $row['business_contact_name'];
        }
        dd($user);
    }
}
