<?php

namespace App\Http\Controllers;

use App\Models\CustomerCard;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Exception;

class OAuthController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function redirect(Request $request, BaseHttpResponse $response)
    {
        $checkAuth = DB::table('faire_oauth_token')->where('type', 'code')->get();
        if($checkAuth){
            DB::table('faire_oauth_token')->where('type', 'code')->delete();
        }
        $data =[
            'type' => 'code',
            'authorization_token' => $request->authorization_code
        ];
     $auth = DB::table('faire_oauth_token')->insert($data);
      return $response
            ->setData([
                'auth' => $auth
            ]);
    }

    public function getToken(Request $request, BaseHttpResponse $response)
    {
        $checkAuth = DB::table('faire_oauth_token')->where('type', 'BEARER')->get();
        if($checkAuth){
            $revoke = $this->revokeToken();
        }
        $token = DB::table('faire_oauth_token')->where(
            'type', 'code'
        )->first();
        $http = new Client();

        $data = $http->request('POST', 'https://www.faire.com/api/external-api-oauth2/token', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'application_token' => getenv('CLIENT_ID'),
                'application_secret' =>  getenv('CLIENT_SECRET'),
                'redirect_url' => 'https://landb-laravel.test/api/authorize',
                'scope' => ['READ_ORDERS'],
                'grant_type' => 'AUTHORIZATION_CODE',
                'authorization_code' => $token->authorization_token,
            ],
            'verify' => false,
        ]);

        $result = $data->getBody()->getContents();

        try {
            $data =[
                'type' => 'BEARER',
                'authorization_token' => json_encode($result)
            ];
            $auth = DB::table('faire_oauth_token')->insert($data);
        }
        catch (Exception $e){
            return $response
                ->setData([
                    'result' => $result,
                    'error'=> $e
                ]);
        }
        return $response
            ->setData([
                'result' => json_decode($result)
            ]);
    }

    public function revokeToken()
    {
        $token = DB::table('faire_oauth_token')->where('type', 'BEARER')->first();
        if ($token) {
            $access_token = json_decode($token->authorization_token, true);
            $access = json_decode($access_token);
            $http = new Client();
            $result = $http->request('POST', 'https://www.faire.com/api/external-api-oauth2/revoke', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json'    => [
                    'application_token'   => getenv('CLIENT_ID'),
                    'application_secret'  => getenv('CLIENT_SECRET'),
                    'access_token_o_auth' => $access->access_token,
                ],
                'verify'  => false,
            ]);
            if ($result) {
                DB::table('faire_oauth_token')->where('type', 'BEARER')->delete();
            }
            return response()->json(['message' => 'Token revoked successfully']);
        }
        return response()->json(['message' => 'Token not found']);
    }

    public function getOrders(Request $request,BaseHttpResponse $response)
    {
        $credentials = base64_encode(getenv('CLIENT_ID') . ':' . getenv('CLIENT_SECRET'));
        $token = DB::table('faire_oauth_token')->where('type', 'BEARER')->first();
        try {
            $access_token = json_decode($token->authorization_token, true);
            $access = json_decode($access_token);
            $http = new Client();

            $url = 'https://www.faire.com/external-api/v2/orders';
            $queryParams = [
                'application_token' => getenv('CLIENT_ID'),
                'application_secret' => getenv('CLIENT_SECRET'),
                'access_token_o_auth' => $access->access_token,
            ];
            if ($request->has('page')) {
                $queryParams['page'] = $request->input('page');
            }
            if ($request->has('limit')) {
                $queryParams['limit'] = $request->input('limit');
            }
            $url .= '?' . http_build_query($queryParams);
            $result = $http->request('GET', $url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-FAIRE-APP-CREDENTIALS' => $credentials,
                    'X-FAIRE-OAUTH-ACCESS-TOKEN' => $access->access_token,
                ],
                'verify' => false,
            ]);
            $result = $result->getBody()->getContents();
            $orders = json_decode($result);
            foreach ($orders->orders as $k => $order) {
                $this->pushtoCsCART($order);
            }
            return $response->setData([
                'result' => $orders,
            ]);
        } catch (\Exception $e) {
            // Handle the exception, log or return an error response
            return $response->setData([
                'error' => $e,
            ]);
        }
    }


    public function pushtoCsCART($order)
    {
        if (isset($order->customer)) {

            $checkOrder = DB::connection('mysql3')->table('hw_orders')->where('copy_order_id', $order->id)->first();
            if (!$checkOrder) {

                $email = strtolower($order->customer->first_name).strtolower($order->customer->last_name).'@faire.com';
                $customer = DB::connection('mysql3')->table('hw_users')->where('email', $email)->first();
                if (!$customer) {
                    $customerData = [
                        'firstname' => $order->customer->first_name,
                        'lastname' => $order->customer->last_name,
                        'email' => $email,
                        'copy_customer_from' => 'faire',
                    ];
                    $customerId = DB::connection('mysql3')->table('hw_users')->insertGetId($customerData);
                } else {
                    $customerId = $customer->user_id;
                }

                $status = newToOldStatus($order->state);
                $orderData = [
                    'user_id'                 => $customerId,
                    'status'                  => $status,
                    'location'                => 'faire id ' . $order->id,
                    'total'                   => $order->payout_costs->total_payout->amount_minor,
                    'shipping_cost'           => $order->payout_costs->shipping_subsidy->amount_minor,
                    'notes'                   => '',
                    'subtotal_discount'       => 0,
                    'subtotal'                => $order->payout_costs->total_payout->amount_minor,
                    'issuer_id'               => 0,
                    'po_number'               => 0,
                    'complete_date'           => 0,
                    'timestamp'               => strtotime($order->created_at),
                    'last_status_change_date' => strtotime($order->updated_at),

                    'email'      => $email,
                    'company'    => '',
                    'company_id' => 1,

                    'copy_order_from' => 'faire',
                    'copy_order_id' => $order->id,

                    'b_firstname' => $order->address->name,
                    'b_lastname'  => $order->address->name,
                    'b_phone'     => $order->address->phone_number,
                    'b_country'   => $order->address->country,
                    'b_state'     => $order->address->state,
                    'b_city'      => $order->address->city,
                    'b_address_2' => $order->address->address1,
                    'b_zipcode'   => $order->address->postal_code,

                    's_firstname' => $order->address->name,
                    's_lastname'  => $order->address->name,
                    's_phone'     => $order->address->phone_number,
                    's_country'   => $order->address->country,
                    's_state'     => $order->address->state,
                    's_city'      => $order->address->city,
                    's_address_2' => $order->address->address1,
                    's_zipcode'   => $order->address->postal_code,
                ];

                $orderId = DB::connection('mysql3')->table('hw_orders')->insertGetId($orderData);

                foreach ($order->items as $key => $orderProduct) {
                    $checkProduct = DB::connection('mysql3')->table('hw_products')->where('product_code', $orderProduct->sku)->first();
                    if ($checkProduct) {
                        $item_id = rand(000000000, 000000000) + $key;
                        $orderProductData = [
                            'item_id'      => $item_id,
                            'order_id'     => $orderId,
                            'amount'       => $orderProduct->quantity,
                            'price'        => $orderProduct->price->amount_minor,
                            'product_id'   => $checkProduct->product_id,
                            'product_code' => $orderProduct->sku
                        ];
                        DB::connection('mysql3')->table('hw_order_details')->insert($orderProductData);
                    }
                }
            }
        }
    }
}
