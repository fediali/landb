<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use App\Models\CardPreAuth;
use App\Models\CustomerAddress;
use App\Models\CustomerCard;
use App\Models\MergeAccount;
use Assets;
use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Forms\CustomerForm;
use Botble\Ecommerce\Http\Requests\AddCustomerWhenCreateOrderRequest;
use Botble\Ecommerce\Http\Requests\AddressRequest;
use Botble\Ecommerce\Http\Requests\CustomerCreateRequest;
use Botble\Ecommerce\Http\Requests\CustomerEditRequest;
use Botble\Ecommerce\Http\Requests\CustomerUpdateEmailRequest;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\CustomerDetail;
use Botble\Ecommerce\Models\UserSearch;
use Botble\Ecommerce\Models\UserSearchItem;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Tables\CustomerTable;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;
use Twilio\Rest\Client;

class CustomerController extends BaseController
{

    /**
     * @var CustomerInterface
     */
    protected $customerRepository;

    /**
     * @var AddressInterface
     */
    protected $addressRepository;
    protected $userRepository;

    /**
     * @param CustomerInterface $customerRepository
     * @param AddressInterface $addressRepository
     */
    public function __construct(CustomerInterface $customerRepository, AddressInterface $addressRepository, UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param CustomerTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function index(CustomerTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/ecommerce::customer.name'));

        return $dataTable->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/ecommerce::customer.create'));

        Assets::addScriptsDirectly('vendor/core/plugins/ecommerce/js/customer.js');

        return $formBuilder->create(CustomerForm::class)->remove('is_change_password')->renderForm();
    }

    /**
     * @param CustomerCreateRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(CustomerCreateRequest $request, BaseHttpResponse $response)
    {
        $request->merge(['password' => bcrypt($request->input('password'))]);
        $customer = $this->customerRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        return $response
            ->setPreviousUrl(route('customer.index'))
            ->setNextUrl(route('customer.edit', $customer->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder)
    {
        Assets::addScriptsDirectly('vendor/core/plugins/ecommerce/js/customer.js');

        $customer = Customer::with(['details', 'shippingAddress', 'BillingAddress', 'storeLocator', 'taxCertificate'])->findOrFail($id);
        //dd($customer);
        page_title()->setTitle(trans('plugins/ecommerce::customer.edit', ['name' => $customer->name]));
        $card = [];
        $customer->password = null;
        if (!$customer->card->isEmpty()) {
            if ($customer->card[0]->customer_omni_id !== null) {
                $url = (env("OMNI_URL") . "customer/" . $customer->card[0]->customer_omni_id . "/payment-method");
                list($card, $info) = omni_api($url);
                $card = json_decode($card);
            }
        }


        return view('plugins/ecommerce::customers.edit', compact('customer', 'card'));
        //return $formBuilder->create(CustomerForm::class, ['model' => $customer])->renderForm();
    }

    public function addAddress($id)
    {
        $user = $id;
        return view('plugins/ecommerce::customers.address', [$id], compact('user'));
    }

    public function postCustomerAddress(AddressRequest $request, BaseHttpResponse $response)
    {
        if ($request->input('is_default') == 1) {
            $this->addressRepository->update([
                'is_default'  => 1,
                'customer_id' => $request->input('customer_id'),
            ], ['is_default' => 0]);
        }
        $request->merge([
            'customer_id' => $request->input('customer_id'),
            'is_default'  => $request->input('is_default', 0),
        ]);

        $address = $this->addressRepository->createOrUpdate($request->input());

        return $response
            ->setNextUrl(route('customer.edit', [$request->customer_id]))
            ->setMessage(trans('core/base::notices.create_success_message'));

    }

    /**
     * @param int $id
     * @param CustomerEditRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, CustomerEditRequest $request, BaseHttpResponse $response)
    {
        if ($request->input('is_change_password') == 1) {
            $request->merge(['password' => bcrypt($request->input('password'))]);
            $data = $request->input();
        } else {
            $data = $request->except('password');
        }
        $data['is_private'] = isset($data['is_private']) ? $data['is_private'] : 0;

        $customer = $this->customerRepository->createOrUpdate($data, ['id' => $id]);
        $data = $request->all();
        $remove = ['_token', 'name', 'email', 'password', 'password_confirmation', 'submit', 'status', 'salesperson_id'];
        $data = array_diff_key($data, array_flip($remove));
        $data['customer_type'] = json_encode($data['customer_type']);
        CustomerDetail::updateOrCreate(['customer_id' => $id], $data);

        //dd($customer, $request->all());

        event(new UpdatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        return $response
            ->setPreviousUrl(route('customer.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $customer = $this->customerRepository->findOrFail($id);
            $this->customerRepository->delete($customer);
            event(new DeletedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $customer = $this->customerRepository->findOrFail($id);
            $this->customerRepository->delete($customer);
            event(new DeletedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getListCustomerForSelect($id, BaseHttpResponse $response)
    {
        if ($id) {
            $customer = $this->customerRepository
                ->allBy([], [], ['id', 'name', 'email'])->where('id', '!=', $id)->take(200)->toArray();
            $account = MergeAccount::where('user_id_one', $id)->pluck('user_id_two');
            $merge = Customer::whereIn('id', $account)->get()->toArray();
            $customers['customer'] = $customer;
            $customers['merge'] = $merge;

        } else {
            $customers = $this->customerRepository
                ->allBy([], [], ['id', 'name'])
                ->toArray();
        }
        return $response->setData($customers);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getListCustomerForSearch(Request $request, BaseHttpResponse $response)
    {
        $customers = $this->customerRepository
            ->getModel()
            ->where('name', 'LIKE', '%' . $request->input('keyword') . '%')
            ->simplePaginate(5);

        foreach ($customers as &$customer) {
            $customer->avatar_url = (string)$customer->avatar_url;
        }

        return $response->setData($customers);
    }

    /**
     * @param int $id
     * @param CustomerUpdateEmailRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postUpdateEmail($id, CustomerUpdateEmailRequest $request, BaseHttpResponse $response)
    {
        $this->customerRepository->createOrUpdate(['email' => $request->input('email')], ['id' => $id]);

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getCustomerAddresses($id, BaseHttpResponse $response)
    {
        $addresses = $this->addressRepository->allBy(['customer_id' => $id]);
        return $response->setData($addresses);
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getCustomerOrderNumbers($id, BaseHttpResponse $response)
    {
        $customer = $this->customerRepository->findById($id);
        if (!$customer) {
            return $response->setData(0);
        }

        return $response->setData($customer->orders()->count());
    }

    /**
     * @param AddCustomerWhenCreateOrderRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postCreateCustomerWhenCreatingOrder(
        AddCustomerWhenCreateOrderRequest $request,
        BaseHttpResponse $response
    )
    {
        $request->merge(['password' => bcrypt(time())]);
        $customer = $this->customerRepository->createOrUpdate($request->input());
        $customer->avatar = (string)$customer->avatar_url;

        event(new CreatedContentEvent(CUSTOMER_MODULE_SCREEN_NAME, $request, $customer));

        $request->merge([
            'customer_id' => $customer->id,
            'is_default'  => true,
        ]);

        $address = $this->addressRepository->createOrUpdate($request->input());

        return $response
            ->setData(compact('address', 'customer'))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function getAddresses($id, BaseHttpResponse $response)
    {
        $addresses = $this->addressRepository->findOrFail($id);
        return $response->setData($addresses);
    }

    public function getCustomer($id, BaseHttpResponse $response)
    {
        $customer = $this->customerRepository->findOrFail($id);
        return $response->setData($customer);
    }

    public function postCustomerCard(Request $request)
    {
        $request['customer_omni_id'] = $request->customer_data['customer_id'];
        $request['customer_data'] = json_encode($request->customer_data);
        $card = CustomerCard::create($request->all());
        $customer = json_decode($request->customer_data);
        $data = [
            'payment_method_id' => $customer->id,
            'meta'              => [
                'reference' => 'Card Validation',
                'tax'       => 0,
                'subtotal'  => 1,
                'lineItems' => []
            ],
            'total'             => 1,
            'pre_auth'          => 1
        ];
        $url = (env("OMNI_URL") . "charge/");
        list($response, $info) = omni_api($url, $data, 'POST');

        $status = $info['http_code'];

        if (floatval($status) == 200) {
            $response = json_decode($response, true);
        } else {
            $errors = [
                422 => 'The transaction didn\'t reach a gateway',
                400 => 'The transaction didn\'t reach a gateway but there weren\'t validation errors',
                401 => 'The account is not yet activated or ready to process payments.',
                500 => 'Unknown issue - Please contact Fattmerchant'
            ];
            return $errors;
        }
        return $card;
    }

    public function getCustomerCard($id, BaseHttpResponse $response)
    {
        $customer = $this->customerRepository->findOrFail($id);
        if (count($customer->card) > 0) {
            $url = (env("OMNI_URL") . "customer/" . $customer->card[0]->customer_omni_id . "/payment-method");
            list($card, $info) = omni_api($url);
            $cards = collect(json_decode($card));
        } else {
            $cards = 0;
        }
        return $response->setData($cards);

    }

    public function updateCustomerAddress(Request $request)
    {
        $data = $request->all();
        $newData = array();
        foreach ($data as $key => $value) {
            $newData[str_replace('address_', '', $key)] = $value;
        }
        $id = $newData['id'];
        unset($newData['id']);
        $update = CustomerAddress::updateOrCreate(['id' => $id], $newData);
        if ($update) {
            return response()->json(['message' => 'Address updated successfully'], 200);
        } else {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    public function deleteAddress(Request $request, BaseHttpResponse $response)
    {
        $id = $request->get('id');

        $address = CustomerAddress::find($id);
        if ($address) {
            $address->delete();
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function verifyphone($id, BaseHttpResponse $response)
    {
        $customer = $this->customerRepository->findOrFail($id);
        $twilio = new Client(env('TWILIO_AUTH_SID'), env('TWILIO_AUTH_TOKEN'));
        try {
            if ($customer->detail->business_phone) {
                $start2 = substr($customer->detail->business_phone, 2);
                if ((int)$start2) {
                    if (strlen($start2) != 10) {
                        return $response->setError()->setMessage('Number Should be atleast 10 digit');
                    }
                    $phone_number = $twilio->lookups->v1->phoneNumbers($customer->detail->business_phone)->fetch(["type" => ["carrier"]]);
                    try {
                        if ($phone_number->carrier['type'] == 'mobile') {
                            $is_text['is_text'] = 1;
                            Customer::where('id', $customer->id)->update($is_text);
                            return $response->setMessage('Number is valid');
                        } else {
                            $is_text['is_text'] = 2;
                            Customer::where('id', $customer->id)->update($is_text);
                            return $response->setError()->setMessage('Number is not valid');
                        }
                    } catch (Exception $error) {
                        $result['phone_validation_error'] = 'Number is not valid';
                        $result['is_text'] = 2;
                        Customer::where('id', $customer->id)->update($result);
                        return $response->setError()->setMessage('Number is not valid');
                    }

                } else {
                    return $response->setError()->setMessage('Number is not valid');
                }
            }
        } catch (Exception $error) {
            $result['phone_validation_error'] = 'Customer Phone number missing from profile';
            $result['is_text'] = 2;
            Customer::where('id', $customer->id)->update($result);
            return $response->setError()->setMessage('Customer Phone number missing from profile');
        }


    }

    public function verifyphonebulk($id, BaseHttpResponse $response)
    {

        $user = $this->userRepository->findOrFail($id);

        $twilio = new Client(env('TWILIO_AUTH_SID'), env('TWILIO_AUTH_TOKEN'));
        $customers = Customer::where('salesperson_id', $user->id)->whereIn('is_text', [Customer::VERIFY, Customer::UNVERIFIED])->get();

        if (!$customers->isEmpty()) {

            foreach ($customers as $customer) {
                $result = [];
                try {
                    if ($customer->detail->business_phone) {
                        $start2 = substr($customer->detail->business_phone, 2);
                        try {
                            if ((int)$start2) {
                                if (strlen($start2) != 10) {
                                    $result['phone_validation_error'] = 'Phone Number must be 10 digit or Add +1.';
                                    $result['is_text'] = 2;
                                    Customer::where('id', $customer->id)->update($result);
                                    break;
                                }
                                $phone_number = $twilio->lookups->v1->phoneNumbers($customer->detail->business_phone)->fetch(["type" => ["carrier"]]);
                                if ($phone_number->carrier['type'] == 'mobile') {
                                    $is_text['is_text'] = 1;
                                    Customer::where('id', $customer->id)->update($is_text);
                                } else {
                                    $is_text['is_text'] = 2;
                                    Customer::where('id', $customer->id)->update($is_text);
                                }
                            } else {
                                $result['phone_validation_error'] = 'Number is not valid';
                                $result['is_text'] = 2;
                                Customer::where('id', $customer->id)->update($result);
                            }
                        } catch (Exception $error) {
                            continue;
                        }
                    }
                } catch (Exception $error) {
                    $result['phone_validation_error'] = 'Customer Phone number missing from profile';
                    $result['is_text'] = 2;
                    Customer::where('id', $customer->id)->update($result);
                    continue;
                }

            }
        }


        return $response->setError()->setMessage('No Customer Found in your list. Only sales rep can verify customers number ');
    }

    public function mergeCustomer(Request $request, BaseHttpResponse $response)
    {

        $merge = DB::table('ec_customers_merge')->insert($request->only('user_id_one', 'user_id_two'));
        if ($merge) {
            return $response->setMessage('Customer Merge Successfully');
        } else {
            return $response->setError()->setMessage('Something Went Wrong');
        }
    }

    public function mergeDelete($id, BaseHttpResponse $response)
    {
        MergeAccount::where('user_id_two', $id)->delete();
        return $response->setMessage('Customer Merge Delete Successfully');
    }

    public function saveAdvanceSearch(Request $request)
    {
        $params = $request->all();

        $searchData = ['user_id' => auth()->user()->id, 'search_type' => 'customers', 'name' => $params['search_name'], 'status' => 1];
        $search = UserSearch::create($searchData);
        $searchItems = [];
        unset($params['search_name']);
        foreach ($params as $key => $value) {
            if ($value) {
                $searchItems[] = ['user_search_id' => $search->id, 'key' => $key, 'value' => $value];
            }
        }
        if (!empty($searchItems)) {
            UserSearchItem::insert($searchItems);
        }

        if ($search) {
            return response()->json(['status' => 'success'], 200);
        } else {
            return response()->json(['status' => 'error'], 500);
        }
    }
}
