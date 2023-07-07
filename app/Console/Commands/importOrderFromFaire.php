<?php

namespace App\Console\Commands;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class importOrderFromFaire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:faire-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importing order from faire every hour';

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
       return $this->getOrders();
    }

    public function getOrders()
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
            $queryParams['page'] = 1;
            $queryParams['limit'] = '50';
            $currentTimestamp = Carbon::today()->toIso8601String();
            $queryParams['created_at_min'] = $currentTimestamp;
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
            return $orders;
        } catch (\Exception $e) {
            // Handle the exception, log or return an error response
            return $e;
        }
    }
    public function pushtoCsCART($order)
    {
        try {
            $connection = 'mysql2';
            if (isset($order->customer)) {
                $checkOrder = DB::connection($connection)->table('hw_orders')->where('copy_order_id', $order->id)->first();
                if (!$checkOrder) {
                    $email = strtolower($order->customer->first_name).strtolower($order->customer->last_name).'@faire.com';
                    $customer = DB::connection($connection)->table('hw_users')->where('email', $email)->first();
                    if (!$customer) {
                        $customerData = [
                            'firstname' => $order->customer->first_name,
                            'lastname' => $order->customer->last_name,
                            'email' => $email,
                            'copy_customer_from' => 'faire',
                        ];
                        $customerId = DB::connection($connection)->table('hw_users')->insertGetId($customerData);
                    } else {
                        $customerId = $customer->user_id;
                    }
                    $status = 'BN';
                    $orderData = [
                        'user_id'                 => $customerId,
                        'status'                  => $status,
                        'location'                => 'faire id ' . $order->id,
                        'total'                   => $order->payout_costs->total_payout->amount_minor/ 100,
                        'shipping_cost'           => $order->payout_costs->shipping_subsidy->amount_minor/ 100,
                        'notes'                   => '',
                        'subtotal_discount'       => 0,
                        'subtotal'                => $order->payout_costs->total_payout->amount_minor / 100,
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
                    $orderId = DB::connection($connection)->table('hw_orders')->insertGetId($orderData);
                    foreach ($order->items as $key => $orderProduct) {
                        $checkProduct = DB::connection($connection)->table('hw_products')->where('product_code', $orderProduct->sku)->first();
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
                            $productData = DB::connection($connection)->table('hw_order_details')->insert($orderProductData);
                            if($productData && $checkProduct->amount){
                                $deductedAmount = $checkProduct->amount - $orderProduct->quantity;
                                // Update the amount column in the database
                                $result = DB::connection($connection)
                                    ->table('hw_products')
                                    ->where('product_code', $orderProduct->sku)
                                    ->update(['amount' => $deductedAmount]);
                            }
                        }
                    }
                }
            }
            return 1;
        }
        catch (\Exception $e) {
            return $e;
        }
    }
}
