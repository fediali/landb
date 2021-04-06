<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DesignerCatCount implements ToModel, WithHeadingRow
{
    public function __construct() {}

    public function model(array $row)
    {
        if ($row['designer'] && $row['category'] && $row['count']) {
            $getCurrCnt = DB::table('category_designer_count')->where(['user_id' => $row['designer'], 'product_category_id' => $row['category']])->value('count');
            if ($getCurrCnt) {
                $newCnt = $getCurrCnt + $row['count'];
                DB::table('category_designer_count')->where(['user_id' => $row['designer'], 'product_category_id' => $row['category']])->update(['count' => $newCnt]);
            } else {
                DB::table('category_designer_count')->insert(['user_id' => $row['designer'], 'product_category_id' => $row['category'], 'count' => $row['count']]);
            }
        }
    }
}
