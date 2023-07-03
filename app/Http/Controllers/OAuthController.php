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
        $access_token = json_decode($token->authorization_token, true);
        $access = json_decode($access_token);
        $http = new Client();
        try {
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
            return $response->setData([
                'result' => json_decode($result),
            ]);
        } catch (\Exception $e) {
            // Handle the exception, log or return an error response
            return $response->setData([
                'error' => $e,
            ]);
        }
    }


    public function pushtoCsCART()
    {
        $newSystemOrder = Order::where('id', $id)->first();
        $bAddress = OrderAddress::where(['order_id' => $id, 'type' => 'billing'])->first();
        $sAddress = OrderAddress::where(['order_id' => $id, 'type' => 'shipping'])->first();
        $status = newToOldStatus($newSystemOrder->status);
//    dd($newSystemOrder->order_completion_date,$newSystemOrder->created_at );
        $orderData = [
            'user_id'                 => $newSystemOrder->user_id,
            'status'                  => ($newSystemOrder->order_type == 'normal') ? $status : 'B',
            'location'                => 'new portal id ' . $id,
            'total'                   => $newSystemOrder->amount,
            'shipping_cost'           => $newSystemOrder->shipping_amount,
            'notes'                   => $newSystemOrder->description,
            'subtotal_discount'       => $newSystemOrder->discount_amount,
            'subtotal'                => $newSystemOrder->sub_total,
            'issuer_id'               => $newSystemOrder->salesperson_id,
            'po_number'               => $newSystemOrder->po_number,
            'complete_date'           => ($newSystemOrder->order_completion_date) ? $newSystemOrder->order_completion_date->getTimestamp() : 0,
//            'timestamp'               => date('Y-m-d H:i:s', strtotime($newSystemOrder->created_at)),
            'timestamp'               => $newSystemOrder->created_at->getTimestamp(),
            'last_status_change_date' => $newSystemOrder->updated_at->getTimestamp(),

            'email'      => $newSystemOrder->user->email,
            'company'    => '',
            'company_id' => 1
            ,

            'b_firstname' => @$bAddress->name,
            'b_lastname'  => @$bAddress->name,
            'b_phone'     => @$bAddress->phone,
            'b_country'   => @$bAddress->country,
            'b_state'     => @$bAddress->state,
            'b_city'      => @$bAddress->city,
            'b_address_2' => @$bAddress->address,
            'b_zipcode'   => @$bAddress->zip_code,

            's_firstname' => @$sAddress->name,
            's_lastname'  => @$sAddress->name,
            's_phone'     => @$sAddress->phone,
            's_country'   => @$sAddress->country,
            's_state'     => @$sAddress->state,
            's_city'      => @$sAddress->city,
            's_address_2' => @$sAddress->address,
            's_zipcode'   => @$sAddress->zip_code,
        ];

        $checkOldSystemOrder = DB::connection('mysql2')->table('hw_orders')->where('order_id', $newSystemOrder->old_system_order_id)->first();
        if ($checkOldSystemOrder) {
            $oldSystemOrderId = $newSystemOrder->old_system_order_id;
            DB::connection('mysql2')->table('hw_order_details')->where('order_id', $oldSystemOrderId)->delete();
            DB::connection('mysql2')->table('hw_orders')->where('hw_orders.order_id', $oldSystemOrderId)->update($orderData);
        } else {
            $oldSystemOrderId = DB::connection('mysql2')->table('hw_orders')->insertGetId($orderData);
            $newSystemOrder->old_system_order_id = $oldSystemOrderId;
            $newSystemOrder->save();
        }


        foreach ($newSystemOrder->products as $key => $orderProduct) {
            $productObj = Product::join('ec_product_variations', 'ec_product_variations.product_id', 'ec_products.id')
                ->where('ec_product_variations.product_id', $orderProduct->product_id)
                ->where('ec_product_variations.is_default', 1)
                ->first();

            $item_id = rand(000000000, 000000000) + $key;

            $orderProductData = [
                'item_id'      => $item_id,
                'order_id'     => $oldSystemOrderId,
                'amount'       => $productObj->prod_pieces ? ($productObj->prod_pieces * $orderProduct->qty) : $orderProduct->qty,
                'price'        => $productObj->prod_pieces ? round($orderProduct->price / $productObj->prod_pieces, 2) : $orderProduct->price,
                'product_id'   => $productObj->configurable_product_id,
                'product_code' => $productObj->sku
            ];

            DB::connection('mysql2')->table('hw_order_details')->insert($orderProductData);
        }

        $userOmniId = CustomerCard::where('customer_id', $newSystemOrder->user_id)->value('customer_omni_id');
        $data = ['omni_customer_id' => $userOmniId];
        DB::connection('mysql2')->table('hw_users')->where('user_id', $newSystemOrder->user_id)->update($data);

        return $response
            ->setData($newSystemOrder)
            ->setMessage(trans('core/base::notices.create_success_message'));
    }
}
