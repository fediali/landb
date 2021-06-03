<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use App\Models\CardPreAuth;
use App\Models\CustomerAddress;
use App\Models\CustomerCard;
use Assets;
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
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Tables\CustomerTable;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

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

    /**
     * @param CustomerInterface $customerRepository
     * @param AddressInterface $addressRepository
     */
    public function __construct(CustomerInterface $customerRepository, AddressInterface $addressRepository)
    {
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

        $customer = Customer::with(['details', 'shippingAddress', 'BillingAddress', 'storeLocator'])->findOrFail($id);
        //dd($customer);
        page_title()->setTitle(trans('plugins/ecommerce::customer.edit', ['name' => $customer->name]));
        $card = [];
        $customer->password = null;
        if (!$customer->card->isEmpty()) {
            $url = (env("OMNI_URL") . "customer/" . $customer->card[0]->customer_omni_id . "/payment-method");
            list($card, $info) = omni_api($url);
            $card = json_decode($card);
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

        $customer = $this->customerRepository->createOrUpdate($data, ['id' => $id]);
        $data = $request->all();
        $remove = ['_token', 'name', 'email', 'password', 'password_confirmation', 'submit', 'status'];
        $data = array_diff_key($data, array_flip($remove));
        $data['customer_type'] = json_encode($data['customer_type']);
        $customer->details()->update($data);
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
    public function getListCustomerForSelect(BaseHttpResponse $response)
    {
        $customers = $this->customerRepository
            ->allBy([], [], ['id', 'name'])
            ->toArray();

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

    public function updateCustomerAddress(Request $request){
      $data = $request->all();
      $newData = array();
      foreach ($data as $key => $value){
        $newData[str_replace('address_', '', $key)] = $value;
      }
      $id = $newData['id'];
      unset($newData['id']);
      $update = CustomerAddress::updateOrCreate(['id' => $id], $newData);
      if($update){
        return response()->json(['message' => 'Address updated successfully'], 200);
      }else{
        return response()->json(['message' => 'Server Error'], 500);
      }
    }

}
