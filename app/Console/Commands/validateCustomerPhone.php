<?php

namespace App\Console\Commands;

use Botble\Ecommerce\Models\Customer;
use Illuminate\Console\Command;

class validateCustomerPhone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:validate-phone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Customer Validate Phone';

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
        $phones = Customer::select('id', 'phone')->get();
        foreach ($phones as $phone) {
            if ($phone->phone) {
                $newPhone = str_replace('-','', $phone->phone);
                $newPhone = str_replace(' ','', $newPhone);
                $newPhone = str_replace('(','', $newPhone);
                $newPhone = str_replace(')','', $newPhone);
                $newPhone = str_replace('?','', $newPhone);
                $newPhone = str_replace('.','', $newPhone);
                $newPhone = str_replace('x','', $newPhone);
                $newPhone = str_replace('+','', $newPhone);

                $start2 = substr($newPhone, 0, 2);
                if (!in_array($start2, ['+1'])) {
                    $start1 = substr($newPhone, 0, 1);
                    if (!in_array($start1, [1])) {
                        $phone->phone = '+1'.$newPhone;
                    } else {
                        $phone->phone = '+'.$newPhone;
                    }
                }
                $phone->save();
            }
        }
        echo 'success';
    }
}
