<?php

namespace App\Console\Commands;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Chating\Http\Controllers\ChatingController;
use Botble\Chating\Repositories\Interfaces\ChatingInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Textmessages\Models\Textmessages;
use Botble\Textmessages\Repositories\Interfaces\TextmessagesInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class sendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Schedule SMS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $chatingRepository;
    protected $customerRepository;
    protected $textmessageRepository;

    public function __construct(
        ChatingInterface $chatingRepository,
        CustomerInterface $customerRepository,
        TextmessagesInterface $textmessageRepository
    )
    {
        parent::__construct();
        $this->chatingRepository = $chatingRepository;
        $this->customerRepository = $customerRepository;
        $this->textmessageRepository = $textmessageRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $tz = Carbon::now('America/Chicago')->toDateTimeString();
//        $time = Carbon::createFromFormat('Y-m-d H:i:s', $tz)->toDateTimeString();
//        $text_message = Textmessages::where('schedule_date', '<', $time)->where('status', BaseStatusEnum::SCHEDULE)->pluck('id')->toArray();
//
//        $controller = app(ChatingController::class);
//        $d = app()->call([$controller, 'smsCampaign'], ['text_id' => $text_message]);
//        return 'Success';

//        $products = DB::connection('mysql2')
//            ->table('hw_products')
//            ->where('count_preorder', '>', 0)
//            ->where('quantity_preorder', '>', 0)
////            ->where('product_id', 76777)
//            ->get();
//        foreach ($products as $pro) {
//            $orders = DB::connection('mysql2')
//                ->table('hw_order_details')
////                ->select('hw_order_details.product_id', 'hw_orders.order_id')
//                ->join('hw_orders', 'hw_orders.order_id', 'hw_order_details.order_id')
//                ->where('hw_orders.status', 'B')
//                ->where('hw_order_details.product_id', $pro->product_id)
//                ->count();
//            if (!$orders) {
//                $data ['count_preorder'] = 0;
//                $data ['quantity_preorder'] = 0;
//                DB::connection('mysql2')
//                    ->table('hw_products')
//                    ->where('product_id', $pro->product_id)
//                    ->update($data);
//            }
//        }

        $products = DB::connection('mysql2')
            ->table('hw_order_details')
            ->select('hw_order_details.product_id', 'hw_order_details.product_code')
            ->selectRaw('SUM(hw_order_details.amount) AS sum_quantity')
            ->join('hw_orders', 'hw_orders.order_id', 'hw_order_details.order_id')
            ->where(function ($q) {
                $q->where('hw_orders.status', 'AZ');
                $q->orWhere('hw_orders.status', 'BB');
                $q->orWhere('hw_orders.status', 'BC');
            })
            ->groupBy('hw_order_details.product_id')
            ->get();

        foreach ($products as $product) {
            $data['paid_preorder'] = $product->sum_quantity;
            DB::connection('mysql2')
                ->table('hw_products')
                ->where('product_id', $product->product_id)
                ->update($data);
        }
    }
}
