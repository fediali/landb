<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class SaveMonthlyInventoryCost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:monthly-invent-cost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save Monthly Inventory Cost';

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
        $today = Carbon::now();
        $lastDayOfMonth = Carbon::parse($today)->endOfMonth();

        $total = [];
        $cost = [];
        $products = DB::connection('mysql2')->table('hw_products')->where('amount', '>', '0')->get();
        foreach ($products as $product) {
            $hw_price = DB::connection('mysql2')->table('hw_product_prices')->where('product_id', $product->product_id)->value('price');
            if ($product->list_price == '0.00') {
                $total[] = $hw_price * $product->amount;
            }
            if ($product->cost_price == '0.00') {
                $cost[] = ($hw_price * (60/100)) * $product->amount;
            }
            $total[] = $product->list_price * $product->amount;
            $cost[] = $product->cost_price * $product->amount;
        }
        $d_cost = array_sum($cost);
        $d_total = array_sum($total);

        $data = [
            'year' => $lastDayOfMonth->year,
            'month' => $lastDayOfMonth->monthName,
            'sum_cost' => $d_cost,
            'sum_sell' => $d_total,
        ];

        DB::connection('mysql2')->table('monthly_inventory_cost')->updateOrInsert(['year' => $lastDayOfMonth->year, 'month' => $lastDayOfMonth->monthName], $data);

        //if ($lastDayOfMonth->isToday()) {}

        echo 'success';
    }
}
