<?php

namespace App\Console\Commands;

use App\Imports\DesignerCatCount;
use Botble\Thread\Models\Thread;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class regenerateThreadSKU extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regenerate:thread-sku';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate Thread SKU';

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
        $thread = Thread::all();
    }
}
