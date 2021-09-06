<?php

namespace App\Console\Commands;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;

class scheduledProductActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'active:scheduled-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Scheduled Product Active';

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
        $tz = Carbon::now('America/Chicago')->toDateTimeString();
        $time = Carbon::createFromFormat('Y-m-d H:i:s', $tz)->toDateTimeString();
        $getProducts = Product::where('ec_products.schedule_date', '<=', $time)->where('ec_products.is_variation', 0)->where('status', '!=', BaseStatusEnum::ACTIVE)->get();
        foreach ($getProducts as $getProduct) {
            $getProduct->status = BaseStatusEnum::ACTIVE;
            $getProduct->save();
            Product::where('id', $getProduct->defaultVariation->product_id)->where('is_variation', 1)->update(['status' => BaseStatusEnum::ACTIVE]);
        }
        return 'Success';
    }
}
