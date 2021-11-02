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
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class fetchCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Customers';


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
        DB::connection('mysql2')
            ->table('hw_users')
            ->join('hw_user_profiles', 'hw_user_profiles.user_id', 'hw_users.user_id')
            ->leftJoin('hw_hw_resale_certificate', 'hw_hw_resale_certificate.user_id', 'hw_users.user_id')
            ->leftJoin('hw_hw_user_extra_fields', 'hw_hw_user_extra_fields.user_id', 'hw_users.user_id')
            ->where('hw_users.user_type', 'C')
            ->where('hw_users.fetch_status', 0)
            ->groupBy('hw_users.user_id')
            ->orderBy('hw_users.user_id', 'ASC')
            ->selectRaw('hw_users.user_id, hw_users.email, hw_users.password, hw_users.firstname, hw_users.lastname, hw_users.company, hw_users.phone AS user_phone, 
                        hw_users.srep_id, hw_users.srep_date,hw_users.profile_image, hw_users.omni_customer_id, hw_users.`status` AS user_status, 
                        hw_user_profiles.b_firstname, hw_user_profiles.b_lastname, hw_user_profiles.b_address, hw_user_profiles.b_address_2, hw_user_profiles.b_city, 
                        hw_user_profiles.b_country, hw_user_profiles.b_phone, hw_user_profiles.b_state, hw_user_profiles.b_zipcode,
                        hw_user_profiles.s_firstname, hw_user_profiles.s_lastname, hw_user_profiles.s_address, hw_user_profiles.s_address_2, hw_user_profiles.s_city, 
                        hw_user_profiles.s_country, hw_user_profiles.s_phone, hw_user_profiles.s_state, hw_user_profiles.s_zipcode,
                        hw_hw_resale_certificate.name AS cert_name, 
                        hw_hw_resale_certificate.phone AS cert_phone, 
                        hw_hw_resale_certificate.address AS cert_address, 
                        hw_hw_resale_certificate.address2 AS cert_address2, 
                        hw_hw_resale_certificate.tax_number AS cert_tax_number, 
                        hw_hw_resale_certificate.registration_number AS cert_registration_number, 
                        hw_hw_resale_certificate.business_description AS cert_business_description, 
                        hw_hw_resale_certificate.products_description AS cert_products_description, 
                        hw_hw_resale_certificate.title AS cert_title, 
                        hw_hw_resale_certificate.date AS cert_date, 
                        hw_hw_resale_certificate.signature AS cert_signature,
                        hw_hw_user_extra_fields.type_western,hw_hw_user_extra_fields.type_boho,hw_hw_user_extra_fields.type_contemporary,
                        hw_hw_user_extra_fields.type_conservative,hw_hw_user_extra_fields.type_other')
            ->selectRaw("(SELECT `value` FROM hw_profile_fields_data WHERE hw_profile_fields_data.object_id = hw_users.user_id AND hw_profile_fields_data.field_id = 40 AND hw_profile_fields_data.object_type = 'U') AS fb")
            ->selectRaw("(SELECT `value` FROM hw_profile_fields_data WHERE hw_profile_fields_data.object_id = hw_users.user_id AND hw_profile_fields_data.field_id = 41 AND hw_profile_fields_data.object_type = 'U') AS insta")
            ->selectRaw("(SELECT `value` FROM hw_profile_fields_data WHERE hw_profile_fields_data.object_id = hw_users.user_id AND hw_profile_fields_data.field_id = 43 AND hw_profile_fields_data.object_type = 'U') AS mortar")
            ->selectRaw("(SELECT `value` FROM hw_profile_fields_data WHERE hw_profile_fields_data.object_id = hw_users.user_id AND hw_profile_fields_data.field_id = 44 AND hw_profile_fields_data.object_type = 'U') AS sales_tax_id")
            ->selectRaw("(SELECT `value` FROM hw_profile_fields_data WHERE hw_profile_fields_data.object_id = hw_users.user_id AND hw_profile_fields_data.field_id = 46 AND hw_profile_fields_data.object_type = 'U') AS hear_us")
            ->selectRaw("(SELECT `value` FROM hw_profile_fields_data WHERE hw_profile_fields_data.object_id = hw_users.user_id AND hw_profile_fields_data.field_id = 48 AND hw_profile_fields_data.object_type = 'U') AS way")
            ->selectRaw("(SELECT `value` FROM hw_profile_fields_data WHERE hw_profile_fields_data.object_id = hw_users.user_id AND hw_profile_fields_data.field_id = 52 AND hw_profile_fields_data.object_type = 'U') AS mob")
            ->chunk(500, function ($users) {
                foreach ($users as $user) {
                    $row = (array) $user;
                    if ($row['user_id']) {
                        $check = Customer::where('id', $row['user_id'])->first();
                        if (!$check) {

                            if ($row['user_status'] == 'A') {
                                $row['user_status'] = BaseStatusEnum::ACTIVE;
                            } elseif ($row['user_status'] == 'X') {
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


                            $customer_type = [];
                            if ($row['type_western']) {
                                $customer_type[] = 'Western';
                            }
                            if ($row['type_boho']) {
                                $customer_type[] = 'Boho';
                            }
                            if ($row['type_contemporary']) {
                                $customer_type[] = 'Contemporary';
                            }
                            if ($row['type_conservative']) {
                                $customer_type[] = 'Conservative';
                            }
                            if ($row['type_other']) {
                                $customer_type[] = 'Other';
                            }


                            $customerDetailData = [
                                'customer_id'             => $row['user_id'],
                                'sales_tax_id'            => $row['sales_tax_id'],
                                'first_name'              => $row['firstname'],
                                'last_name'               => $row['lastname'],
                                'business_phone'          => $row['user_phone'],
                                'company'                 => $row['company'],
                                'phone'                   => $row['mob'],
                                'store_facebook'          => $row['fb'],
                                'store_instagram'         => $row['insta'],
                                'mortar_address'          => $row['mortar'],
                                'hear_us'                 => $row['hear_us'],
                                'preferred_communication' => $row['way'],
                                'customer_type'           => json_encode($customer_type),
                            ];
                            CustomerDetail::create($customerDetailData);

                            $customerAddress = [
                                'name'        => $row['b_firstname'] . ' ' . $row['b_lastname'],
                                'email'       => $row['email'],
                                'phone'       => $row['b_phone'],
                                'country'     => $row['b_country'],
                                'city'        => $row['b_city'],
                                'zip_code'    => $row['b_zipcode'],
                                'state'       => $row['b_state'],
                                'address'     => $row['b_address_2'],
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
                                'address'     => $row['s_address_2'],
                                'state'       => $row['s_state'],
                                'customer_id' => $row['user_id'],
                                'first_name'  => $row['s_firstname'],
                                'last_name'   => $row['s_lastname'],
                                'company'     => $row['company'],
                                'zip_code'    => $row['s_zipcode'],
                                'type'        => 'shipping',
                            ];
                            CustomerAddress::create($customerAddressS);

                            $taxData = [
                                'customer_id'          => $row['user_id'],
                                'purchaser_name'       => $row['cert_name'] ? $row['cert_name'] : 'N/A',
                                'purchaser_phone'      => $row['cert_phone'] ? $row['cert_phone'] : 'N/A',
                                'purchaser_address'    => $row['cert_address'] . ' ' . $row['cert_address2'],
                                'purchaser_city'       => $row['cert_address2'],
                                'permit_no'            => $row['cert_tax_number'],
                                'registration_no'      => $row['cert_registration_number'],
                                'business_description' => $row['cert_business_description'],
                                'items_description'    => $row['cert_products_description'],
                                'title'                => $row['cert_title'],
                                'date'                 => date('Y-m-d', strtotime($row['cert_date'])),
                                'purchaser_sign'       => $row['cert_signature'],
                                'status'               => 1,
                            ];
                            CustomerTaxCertificate::create($taxData);

                            DB::connection('mysql2')->table('hw_users')->where('hw_users.fetch_status', 0)->where('user_id', $row['user_id'])->update(['hw_users.fetch_status' => 1]);

                        } else {
                            echo $row['user_id'].'----------';
                        }
                    } else {
                        echo $row['user_id'].'==========';
                    }
                }
            });

        echo 'success';
    }
}
