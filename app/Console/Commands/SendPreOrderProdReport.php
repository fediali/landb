<?php

namespace App\Console\Commands;

use App\Mail\OrderCreate;
use App\Mail\PreOrderProdQty;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendPreOrderProdReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:preorder-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Pre Order Product Report';

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
        $to_date = Carbon::now();
        $from_date = $to_date->subDays($to_date->dayOfWeek-1)->subWeek();//->format('Y-m-d');
        $today = Carbon::now();//->format('Y-m-d');

        $products = DB::connection('mysql2')
            ->table('hw_order_details')
            ->select('hw_order_details.product_code')
            ->selectRaw('hw_order_details.amount')
            ->join('hw_orders', 'hw_orders.order_id', 'hw_order_details.order_id')
            ->where('hw_orders.status', 'AZ')
            //->whereDate('hw_orders.created_at', '>=', $from_date)
            //->whereDate('hw_orders.created_at', '<=', $today)
            ->where('hw_orders.timestamp', '>=', strtotime($from_date))
            ->where('hw_orders.timestamp', '<=', strtotime($today))
            ->get();

        $dates = [
            'from_date' => $from_date,
            'to_date' => $today,
        ];
        $data = [];
        foreach ($products as $product) {
            if (isset($data[$product->product_code])) {
                $data[$product->product_code] += $product->amount;
            } else {
                $data = [$product->product_code => $product->amount];
            }
        }

        Mail::to(['amir@landbapparel.com ','farhad.ali@luckyandblessed.com', 'farhad.surani@gmail.com'])->send(new PreOrderProdQty($data, $dates));

        echo 'success';
    }
}
