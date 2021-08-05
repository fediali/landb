<?php

namespace App\Console\Commands;


use App\Models\CustomerAddress;
use App\Models\CustomerCard;
use App\Models\CustomerTaxCertificate;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\CustomerDetail;
use File;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class importCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Customers';


    protected $response;
    protected $productVariation;
    protected $productCategoryRepository;

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
        /*$file = public_path('lnb-customers-3000.xlsx');
        Excel::import(new \App\Imports\ImportCustomers(), $file);*/

        $file = File::get(public_path('lnb-customers-8000.json'));
        $data = json_decode(utf8_encode($file), true);

        foreach ($data['rows'] as $row) {
            if ($row['user_id']) {
                $check = Customer::where('id', $row['user_id'])->first();
                if (!$check) {
                    if ($row['user_status'] == 'A') {
                        $row['user_status'] = BaseStatusEnum::ACTIVE;
                    } elseif ($row['user_status'] = 'X') {
                        $row['user_status'] = BaseStatusEnum::DECLINED;
                    } else {
                        $row['user_status'] = BaseStatusEnum::DISABLED;
                    }
                    $customerData = [
                        'id'             => $row['user_id'],
                        'email'          => $row['email'],
                        'name'           => $row['firstname'] . ' ' . $row['lastname'],
                        'password'       => $row['password'],
                        'avatar'         => $row['profile_image'],
                        'phone'          => $row['user_phone'],
                        'first_name'     => $row['firstname'],
                        'last_name'      => $row['lastname'],
                        'salesperson_id' => $row['srep_id'],
                        'status'         => $row['user_status'],
                    ];
                    Customer::create($customerData);

                    CustomerCard::create(['customer_id' => $row['user_id'], 'customer_omni_id' => $row['omni_customer_id']]);

                    $customerDetailData = [
                        'customer_id'    => $row['user_id'],
                        'sales_tax_id'   => $row['sales_tax_id'],
                        'first_name'     => $row['firstname'],
                        'last_name'      => $row['lastname'],
                        'business_phone' => $row['phone'],
                        'company'        => $row['company'],
                        'phone'          => $row['phone'],
                    ];
                    CustomerDetail::create($customerDetailData);

                    $customerAddress = [
                        'name'        => $row['b_firstname'] . ' ' . $row['b_lastname'],
                        'email'       => $row['email'],
                        'phone'       => $row['b_phone'],
                        'country'     => $row['b_country'],
                        'city'        => $row['b_city'],
                        'address'     => $row['b_address'],
                        'customer_id' => $row['user_id'],
                        'first_name'  => $row['b_firstname'],
                        'last_name'   => $row['b_lastname'],
                        'company'     => $row['company'],
                        'type'        => 'billing',
                    ];
                    CustomerAddress::create($customerAddress);

                    $customerAddressS = [
                        'name'        => $row['s_firstname'] . ' ' . $row['s_lastname'],
                        'email'       => $row['email'],
                        'phone'       => $row['s_phone'],
                        'country'     => $row['s_country'],
                        'city'        => $row['s_city'],
                        'address'     => $row['s_address'],
                        'customer_id' => $row['user_id'],
                        'first_name'  => $row['s_firstname'],
                        'last_name'   => $row['s_lastname'],
                        'company'     => $row['company'],
                        'type'        => 'shipping',
                    ];
                    CustomerAddress::create($customerAddressS);

                    $taxData = [
                        'customer_id'          => $row['user_id'],
                        'purchaser_name'       => $row['name'],
                        'purchaser_phone'      => $row['phone'],
                        'purchaser_address'    => $row['address'] . ' ' . $row['address2'],
                        'purchaser_city'       => 'N/A',
                        'permit_no'            => $row['tax_number'],
                        'registration_no'      => $row['registration_number'],
                        'business_description' => $row['business_description'],
                        'items_description'    => $row['products_description'],
                        'title'                => $row['title'],
                        'date'                 => date('Y-m-d', strtotime($row['date'])),
                        'purchaser_sign'       => $row['signature'],
                        'status'               => 1,
                    ];
                    CustomerTaxCertificate::create($taxData);
                }
            }
        }

        echo 'success';
    }
}
