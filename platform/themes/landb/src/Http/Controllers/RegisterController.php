<?php

namespace Theme\Landb\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use Botble\ACL\Traits\RegistersUsers;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\CustomerDetail;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Validator;
use Response;
use SeoHelper;
use Theme;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * @var CustomerInterface
     */
    protected $customerRepository;

    /**
     * Create a new controller instance.
     *
     * @param CustomerInterface $customerRepository
     */
    public function __construct(CustomerInterface $customerRepository)
    {
        $this->middleware('customer.guest');
        $this->customerRepository = $customerRepository;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
     // dd($data);
        $rules = [
            'email'    => 'required|email|max:255|unique:ec_customers',
            'password' => 'required|min:6|confirmed',
            'first_name'     => 'required|max:255',
            'last_name'     => 'required|max:255',
            'business_phone'     => 'required|max:12',
            'mobile'     => 'required|max:12',
            'company'     => 'required|max:255',
            "customer_type"    => "required|array|min:1",
            "sales_tax_id"    => "required|max:15",
            "shipping_first_name"    => "required|max:255",
            "shipping_last_name"    => "required|max:255",
            "shipping_company"    => "required|max:255",
            "shipping_mobile"    => "required|max:255",
            "shipping_address"    => "required|max:255",
            "shipping_city"    => "required|max:255",
            "shipping_country"    => "required|max:255",
            "shipping_state"    => "required|max:255",
            "shipping_postal_code"    => "required|max:255",
        ];

        if (setting('enable_captcha') && is_plugin_active('captcha')) {
            $rules += ['g-recaptcha-response' => 'required|captcha'];
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return Customer
     */
    protected function create(array $data)
    {
        $customer = $this->customerRepository->create([
            'name'        => $data['first_name'].' '.$data['last_name'],
            'email'       => $data['email'],
            'password'    => bcrypt($data['password']),
            'first_name'  => $data['first_name'],
            'last_name'   => $data['last_name'],
        ]);
        if($customer){
          $cutomer_details = CustomerDetail::create([
              'customer_id'             => $customer->id,
              'sales_tax_id'            => $data['sales_tax_id'],
              'first_name'              => $data['first_name'],
              'last_name'               => $data['last_name'],
              'business_phone'          => $data['business_phone'],
              'company'                 => $data['company'],
              'customer_type'           => json_encode($data['customer_type']),
              'store_facebook'          => $data['store_facebook'],
              'store_instagram'         => $data['store_instagram'],
              'mortar_address'          => $data['mortar_address'],
              'newsletter'              => isset($data['newsletter']) ? $data['newsletter'] : 0,
              'hear_us'                 => $data['hear_us'],
              'comments'                => $data['comments'],
              'phone'                   => $data['mobile'],
              'preferred_communication' => $data['preferred_communication'],
              'events_attended'         => $data['events_attended']
          ]);

          $shipping_address = CustomerAddress::create([
              'customer_id' => $customer->id,
              'name'  => $data['shipping_first_name'].' '.$data['shipping_last_name'],
              'email' => $data['email'],
              'phone' => $data['shipping_mobile'],
              'country' => $data['shipping_country'],
              'city' => $data['shipping_city'],
              'state' => $data['shipping_state'],
              'address' => $data['shipping_address'],
              'is_default' => 1,
              'zip_code' => $data['shipping_postal_code'],
              'first_name' => $data['shipping_first_name'],
              'last_name' => $data['shipping_last_name'],
              'company' => $data['shipping_company'],
              'status' => 'active',
          ]);

          if($data['billing'] == 0){
            $billing_address = CustomerAddress::create([
                'customer_id' => $customer->id,
                'name'  => $data['billing_first_name'].' '.$data['billing_last_name'],
                'email' => $data['email'],
                'phone' => $data['billing_mobile'],
                'country' => $data['billing_country'],
                'city' => $data['billing_city'],
                'state' => $data['billing_state'],
                'address' => $data['billing_address'],
                'is_default' => 0,
                'zip_code' => $data['billing_postal_code'],
                'first_name' => $data['billing_first_name'],
                'last_name' => $data['billing_last_name'],
                'company' => $data['billing_company'],
                'status' => 'active',
            ]);
          }else{
            $shipping_address->type = 'billing';
            $shipping_address = $shipping_address->toArray();
            unset($shipping_address['id']);
            $billing_address = CustomerAddress::insert($shipping_address);
          }
          return $customer;
        }
    }

    /**
     * Show the application registration form.
     *
     * @return Response
     */

    public function showRegisterForm()
    {
      SeoHelper::setTitle(__('Register'));

      Theme::breadcrumb()->add(__('Home'), url('/'))->add(__('Login'), route('customer.login'));

      return Theme::scope('auth.register', [], 'plugins/ecommerce::themes.customers.register')->render();
    }


  /**
     * Get the guard to be used during registration.
     *
     * @return StatefulGuard
     */
    protected function guard()
    {
        return auth('customer');
    }
}
