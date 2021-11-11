<?php

namespace App\Console\Commands;

use App\Models\CustomerCard;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MapReps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Map:reps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Map reps';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $paymentRepository;

    public function __construct(PaymentInterface $paymentRepository)
    {
        parent::__construct();
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $customers = DB::connection('mysql2')
//            ->table('hw_users')
//            ->orderBy('hw_users.user_id', 'ASC')
//            ->where(['hw_users.user_type' => 'C', 'srep_id' => '100'])->get();
//
//        $count = DB::connection('mysql2')
//            ->table('hw_users')
//            ->orderBy('hw_users.user_id', 'ASC')
//            ->where(['hw_users.user_type' => 'C', 'srep_id' => '100'])->count();
//        echo $count;
//        foreach ($customers as $customer) {
//            echo '<pre>' . $customer->user_id . '</pre>';
//            $rep['salesperson_id'] = 87;
//            Customer::where('id', $customer->user_id)->update($rep);
//        }

//        $orders = DB::connection('mysql2')
//            ->table('hw_orders')
//            ->orderBy('hw_orders.order_id', 'ASC')
//            ->where(['issuer_id' => '16397'])->get();

//        foreach ($orders as $order) {
//            echo '<pre>' . $order->order_id . '</pre>';
//            $rep['salesperson_id'] = 87;
//            Order::where('id', $order->order_id)->update($rep);
//        }


    }
}
