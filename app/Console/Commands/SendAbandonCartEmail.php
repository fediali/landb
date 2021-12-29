<?php

namespace App\Console\Commands;

use App\Mail\AbandonCartEmail;
use App\Mail\OrderCreate;
use App\Mail\PreOrderProdQty;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendAbandonCartEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:abandon-cart-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Abandon Cart Email';

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
        $sessions = DB::connection('mysql2')
            ->table('hw_user_session_products')
            ->select('hw_user_session_products.*', 'hw_users.email')
            ->join('hw_users', 'hw_users.user_id', 'hw_user_session_products.user_id')
            ->where('hw_user_session_products.email_sent_status', '!=', 2)
            ->groupBy('hw_user_session_products.user_id')
            ->orderBy('hw_user_session_products.timestamp', 'DESC')
            ->limit(1)
            ->get();

        foreach ($sessions as $session) {
            $to           = Carbon::now();
            $session_date = Carbon::createFromTimestamp($session->timestamp)->toDateTimeString();
            $from = Carbon::createFromFormat('Y-m-d H:s:i', $session_date);
            $diff_in_days = 0;
            if ($from >= $to) {
                $diff_in_days = $to->diffInDays($from);
            }
            if (in_array($diff_in_days, [0,1,3])) {
                // $session->email
                Mail::to(['farhad.surani@gmail.com'])->send(new AbandonCartEmail($session));
            }
        }

        echo 'success';
    }
}
