<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class importCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Customers';


    protected $response;
    protected $productVariation;
    protected $productCategoryRepository;

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
        $file = public_path('lnb-customers.xlsx');
        Excel::import(new \App\Imports\ImportCustomers(), $file);
        echo 'success';
    }
}
