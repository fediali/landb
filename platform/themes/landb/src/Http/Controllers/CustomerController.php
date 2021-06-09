<?php

namespace Theme\Landb\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\CustomerStoreLocator;
use App\Models\CustomerTaxCertificate;
use Botble\ACL\Traits\AuthenticatesUsers;
use Botble\ACL\Traits\LogoutGuardTrait;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\CustomerDetail;
use Botble\Ecommerce\Models\StoreLocator;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use SeoHelper;
use Symfony\Component\HttpFoundation\Response;
use Theme;
use URL;
use CountryState;
/*use Botble\Theme\Http\Controllers\PublicController;*/

class CustomerController extends Controller
{
  protected $customer;

  public function __construct(Customer $customer) {
    $this->customer = $customer;
  }

  public function edit(){
    $data['user'] = $this->customer->with(['details','shippingAddress', 'BillingAddress', 'storeLocator'])->find(auth('customer')->user()->id);
    //dd($data['user']->shippingAddress[0]->first_name);
    return Theme::scope('customer.edit', $data)->render();
  }

  public function show(){
    $data['user'] = $this->customer->with(['details','shippingAddress', 'BillingAddress', 'storeLocator',
        'orders' => function($query){
            $query->orderBy('id', 'desc');
        }
    ])->find(auth('customer')->user()->id);
    //dd($data['user']->shippingAddress[0]->first_name);
    return Theme::scope('customer.profile', $data)->render();
  }

  public function update($type, Request $request){
    switch ($type){
      case 'account':
        $this->updateAccount($request);
        break;
      case 'address':
        $this->updateAddresses($request);
        break;
      case 'tax_certificate':
        $this->updateTaxCertificate($request);
        break;
      case 'store_locator':
        $this->updateStoreLocator($request);
        break;
      case 'add-address':
        $this->addNewAddress($request);
        break;
      default:
        return redirect()->back();
    }
    return redirect()->back()->with('success','Account updated');
  }

  public function updateAccount($request){
    $user = auth('customer')->user();
    $data = $request->all();
    if(!is_null($data['new_password'])){
      $validate =  $request->validate([
          'old_password' => [
                                'required',
                                function ($attribute, $value, $fail) {
                                  $user = auth('customer')->user();
                                  if (!Hash::check($value, $user->password)) {
                                    $fail('Old password is incorrect.');
                                  }
                                },
                            ],
          'new_password' => 'confirmed|min:8|different:old_password',
      ]);

      if (Hash::check($request->old_password, $user->password)) {
        $user->fill([
            'password' => Hash::make($request->new_password)
        ])->save();
        dd('saved');
      }
    }
    $validateAccount = $this->accountValidator($request);

    $cutomer_details = CustomerDetail::where('customer_id', $user->id)->update([
        'sales_tax_id'            => $data['sales_tax_id'],
        'first_name'              => $data['first_name'],
        'last_name'               => $data['last_name'],
        'business_phone'          => $data['business_phone'],
        'company'                 => $data['company'],
        'customer_type'           => json_encode($data['customer_type']),
        'store_facebook'          => $data['store_facebook'],
        'store_instagram'         => $data['store_instagram'],
        'mortar_address'          => $data['mortar_address'],
        'hear_us'                 => isset($data['hear_us']) ? $data['hear_us']:null,
        'comments'                => $data['comments'],
        'phone'                   => $data['phone'],
        'preferred_communication' => isset($data['preferred_communication']) ? $data['preferred_communication']:null,
        'events_attended'         => $data['events_attended']
    ]);
    $user->fill([
        'name' => $data['first_name'].' '.$data['last_name']
    ])->save();

    return true;
  }

  public function updateAddresses($request) {
    $user = auth('customer')->user();
    $validate = $this->addressesValidator($request);
    $data = $request->all();
    $shippingUpdate = CustomerAddress::where('id', $request->shipping_id)->update([
        'name'        => $data['shipping_first_name'].' '.$data['shipping_last_name'],
        'phone'       => $data['shipping_phone'],
        'country'     => $data['shipping_country'],
        'city'        => $data['shipping_city'],
        'state'       => $data['shipping_state'],
        'address'     => $data['shipping_address'],
        'zip_code'    => $data['shipping_zip_code'],
        'first_name'  => $data['shipping_first_name'],
        'last_name'   => $data['shipping_last_name'],
        'company'     => $data['shipping_company'],
        'is_default'  => isset($data['set_default']) ? $data['set_default'] : 0,
    ]);
    $billing = ($request->billing == 0) ? 'billing' : 'shipping';
    $billingUpdate = CustomerAddress::where('id', $request->billing_id)->update([
        'name'        => $data[$billing.'_first_name'].' '.$data['billing_last_name'],
        'phone'       => $data[$billing.'_phone'],
        'country'     => $data[$billing.'_country'],
        'city'        => $data[$billing.'_city'],
        'state'       => $data[$billing.'_state'],
        'address'     => $data[$billing.'_address'],
        'zip_code'    => $data[$billing.'_zip_code'],
        'first_name'  => $data[$billing.'_first_name'],
        'last_name'   => $data[$billing.'_last_name'],
        'company'     => $data[$billing.'_company']
    ]);
    if(isset($data['set_default']) && $data['set_default'] == 1){
      CustomerAddress::where('id', '!=', $data['shipping_id'])->where('customer_id', $user->id)->update(['is_default' => 0]);
    }
  }

  public function updateTaxCertificate($request) {

    $user = auth('customer')->user();

    $validate = $request->validate([
        'purchaser_name' => 'required|max:255',
        'purchaser_phone' => 'required|max:12',
        'purchaser_address' => 'required|max:255',
        'purchaser_city' => 'required|max:255',
        'permit_no' => 'required|min:11|max:11',
        'registration_no' => 'required|max:255',
        'items_description' => 'required',
        'business_description' => 'required',
        'title' => 'required|max:255',
        'date' => 'required',
        'purchaser_sign' => 'required',
    ]);

    $image = $request->purchaser_sign;  // your base64 encoded
    $image = str_replace('data:image/png;base64,', '', $image);
    $image = str_replace(' ', '+', $image);
    $imageName = 'signatures/'.substr(microtime(), 2,7).'.'.'png';
    /*\File::put(storage_path(). '/' . $imageName, base64_decode($image));*/
    Storage::disk('public')->put($imageName, base64_decode($image));

    $data = $request->all();
      unset($data['_token']);

      $data['purchaser_sign'] = 'storage/' . $imageName;

    $submit = CustomerTaxCertificate::updateOrCreate(['customer_id' => $user->id] , $data);

  }

  public function updateStoreLocator($request) {
    $user = auth('customer')->user();

    $validate = $request->validate([
        'locator_company' => 'required|max:255',
        'locator_phone' => 'required|max:12',
        'locator_website' => 'required|max:255',
        'locator_address' => 'required|max:255',
        'locator_city' => 'required|max:255',
        'locator_country' => 'required|max:255',
        'locator_state' => 'required|max:255',
        'locator_zip_code' => 'required|max:255',
        'locator_customer_type' => 'required|max:255',
    ]);
    $data = $request->all();
      unset($data['_token']);

    $store = CustomerStoreLocator::updateOrCreate(['customer_id' => $user->id] , $data);

    if(isset($data['add_store_locator'])){
      StoreLocator::updateOrCreate(['customer_store_locator_id' => $store->id],
          [
              'name' => $data['locator_company'],
              'email' => auth('customer')->user()->email,
              'phone' => $data['locator_phone'],
              'address' => $data['locator_address'],
              'city' => $data['locator_city'],
              'state' => $data['locator_state'],
              'country' => $data['locator_country']
          ]);
    }

  }

  protected function accountValidator($request)
  {
    // dd($data);
    $rules = [
        'first_name'     => 'required|max:255',
        'last_name'     => 'required|max:255',
        'business_phone'     => 'required|max:12',
        'phone'     => 'required|max:12',
        'company'     => 'required|max:255',
        "customer_type"    => "required|array|min:1",
        "sales_tax_id"    => "required|max:15",
    ];

    return $request->validate($rules);
  }

  protected function addressesValidator($request)
  {
    // dd($data);
    $rules = [
        'shipping_first_name'     => 'required|max:255',
        'shipping_last_name'     => 'required|max:255',
        'shipping_country'     => 'required|max:255',
        'shipping_phone'     => 'required|max:12',
        'shipping_company'     => 'required|max:255',
        'shipping_city'     => 'required|max:255',
        'shipping_state'     => 'required|max:255',
    ];
    if($request->billing == 0){
      $rules1 = [
          'billing_first_name'     => 'required|max:255',
          'billing_last_name'     => 'required|max:255',
          'billing_country'     => 'required|max:255',
          'billing_phone'     => 'required|max:12',
          'billing_company'     => 'required|max:255',
          'billing_city'     => 'required|max:255',
          'billing_state'     => 'required|max:255',
      ];
      $rules = array_merge($rules, $rules1);
    }

    return $request->validate($rules);
  }

  public function pendingNotification(){
    return Theme::scope('customer.verify')->render();
  }

  public function contractForm(){
    $data['user'] = $this->customer->with(['details','shippingAddress', 'BillingAddress', 'storeLocator'])->find(auth('customer')->user()->id);
    return Theme::scope('customer.contract', $data)->render();
  }

  public function getStates(Request $request){
    $country = $request->input('country');
    return CountryState::getStates($country);
  }

  public function getCountries(Request $request){
    return CountryState::getCountries();
  }

  public function addNewAddress($request){
    $request->validate([
        'first_name'     => 'required|max:255',
        'last_name'     => 'required|max:255',
        'country'     => 'required|max:255',
        'phone'     => 'required|max:12',
        'company'     => 'required|max:255',
        'city'     => 'required|max:255',
        'state'     => 'required|max:255',
        'type'      => 'required'
    ]);
$data = $request->all();
unset($data['_token']);

    $create = CustomerAddress::create($data);

    return true;

  }
}
