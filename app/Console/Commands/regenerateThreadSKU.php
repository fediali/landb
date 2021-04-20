<?php

namespace App\Console\Commands;

use App\Imports\DesignerCatCount;
use App\Models\ThreadVariation;
use Botble\Printdesigns\Models\Printdesigns;
use Botble\Thread\Models\Thread;
use Botble\Threadorders\Models\Threadorders;
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
        $threads = Thread::all();

        foreach ($threads as $thread) {
            $checkOrderExist = Threadorders::where('thread_id', $thread->id)->first();
            if (!$checkOrderExist) {

                $reg_category = @$thread->regular_product_categories[0];
                $plu_category = @$thread->plus_product_categories[0];

                if ($reg_category && $reg_category->pivot->product_category_id > 0) {

                    $designerName = strlen($thread->designer->name_initials) > 0 ? $thread->designer->name_initials : $thread->designer->first_name;
                    $reg_sku = generate_thread_sku($reg_category->pivot->product_category_id, $thread->designer_id, $designerName);

                    if ($plu_category && $plu_category->pivot->product_category_id > 0) {
                        $plu_sku = $reg_sku . '-X';

                        $thread->regular_product_categories()->sync([
                            $reg_category->pivot->product_category_id => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku, 'product_unit_id' => $reg_category->pivot->product_unit_id, 'per_piece_qty' => $reg_category->pivot->per_piece_qty],
                            $plu_category->pivot->product_category_id => ['category_type' => Thread::PLUS, 'sku' => $plu_sku, 'product_unit_id' => $plu_category->pivot->product_unit_id, 'per_piece_qty' => $plu_category->pivot->per_piece_qty]
                        ]);
                    } else {
                        $thread->regular_product_categories()->sync([$reg_category->pivot->product_category_id => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku, 'product_unit_id' => $reg_category->pivot->product_unit_id, 'per_piece_qty' => $reg_category->pivot->per_piece_qty]]);
                    }

                    $variations = ThreadVariation::where(['thread_id' => $thread->id])->get();

                    foreach ($variations as $variation) {
                        $pdSKU = Printdesigns::where('id', $variation->print_id)->value('sku');

                        if ($reg_sku) {
                            $variation->sku = $reg_sku . strtoupper($pdSKU);
                        }

                        if ($plu_sku) {
                            $variation->plus_sku = str_replace('-X', '', $plu_sku) . strtoupper($pdSKU) . '-X';
                        }

                        $variation->save();
                    }

                }

            }
        }
        echo 'success';
    }
}
