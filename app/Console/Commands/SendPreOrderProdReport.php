<?php

namespace App\Console\Commands;

use App\Exports\PreOrderProdExport;
use App\Mail\PreOrderProdQty;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

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
        $dates = ['from_date' => $from_date, 'to_date' => $today,];

        Mail::to(['shakir@bargoventures.com', 'farhad.ali@luckyandblessed.com', 'farhad.surani@gmail.com'])->send(new PreOrderProdQty($dates));

        echo 'success';
    }
}
