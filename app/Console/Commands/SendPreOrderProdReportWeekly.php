<?php

namespace App\Console\Commands;

use App\Mail\OrderCreate;
use App\Mail\PreOrderProdQty;
use App\Mail\PreOrderProdQtyWeekly;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendPreOrderProdReportWeekly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:preorder-report-weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Pre Order Product Report Weekly';

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
        $dates = ['from_date' => $from_date, 'to_date' => $today,];

        Mail::to([
            'amir@landbapparel.com',
            'erika.ibarra@luckyfactory.com.mx',
            'heron.femat@landbapparel.com',
            'luis.garza@luckyfactory.com.mx',
            'jesus.arredondo@luckyfactory.com.mx',
            'magdalena@landbapparel.com',
            'bintou@landbapparel.com',
            'ramsha@landbapparel.com',
            'farhad.ali@luckyandblessed.com',
            'edgarb@landbapparel.com',
            'alejandro.ruiz@luckyfactory.com.mx',
            'monica.rodriguez@luckyfactory.com.mx',
        ])->send(new PreOrderProdQtyWeekly($dates));

        echo 'success';
    }
}
