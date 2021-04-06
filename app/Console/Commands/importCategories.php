<?php

namespace App\Console\Commands;

use App\Imports\DesignerCatCount;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class importCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Categories';

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
        /*$getCategories = DB::table('hw_categories')
            ->select('hw_categories.category_id', 'hw_category_descriptions.category', 'hw_categories.parent_id', 'hw_category_descriptions.description', 'hw_categories.status', 'hw_categories.position')
            ->join('hw_category_descriptions', 'hw_categories.category_id', '=','hw_category_descriptions.category_id')
            ->get();

        foreach ($getCategories as $getCategory) {
            $data = [
                'id' => $getCategory->category_id,
                'name' => $getCategory->category,
                'parent_id' => $getCategory->parent_id,
                'description' => $getCategory->description,
                'status' => $getCategory->status == 'A' ? 'published' : 'pending',
                'order' => $getCategory->position,
                'is_plus_cat' => str_contains(strtolower($getCategory->category), 'plus') ? 1 : 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            DB::table('ec_product_categories')->insert($data);
        }*/

        /*$getCategorySizes = DB::table('category_sizes')->orderBy('id')->get();

        foreach ($getCategorySizes as $getCategorySize) {
            $data = [
                'id' => $getCategorySize->id,
                'name' => $getCategorySize->size,
                'status' => 'published', //$getCategorySize->status ? 'published' : 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            DB::table('categorysizes')->insert($data);

            $data2 = [
                'product_category_id' => $getCategorySize->category_id,
                'category_size_id' => $getCategorySize->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            DB::table('product_categories_sizes')->insert($data2);
        }*/

        /*$getPrints = DB::table('print')->orderBy('id')->get();
        foreach ($getPrints as $getPrint) {
            $data = [
                'id' => $getPrint->id,
                'designer_id' => $getPrint->user_id,
                'name' => $getPrint->name,
                'sku' => $getPrint->sku,
                'file' => $getPrint->file,
                'file_type' => $getPrint->type,
                'status' => 'published',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            DB::table('printdesigns')->insert($data);
        }*/

        /*$file = public_path('designer_cat_count.xlsx');
        Excel::import(new DesignerCatCount(), $file);*/

    }
}
