<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class SumPreOrderProdExport implements FromCollection, ShouldAutoSize, WithHeadings, WithEvents, WithHeadingRow
{
    public $dates;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $to_date = Carbon::now();
        $from_date = $to_date->subDays($to_date->dayOfWeek - 1)->subWeek();//->format('Y-m-d');
        $today = Carbon::now();//->format('Y-m-d');

        $this->dates = date('m-d-Y', strtotime($from_date)) . ' to ' . date('m-d-Y', strtotime($today));

        $products = DB::connection('mysql2')
            ->table('hw_order_details')
            ->select('hw_order_details.product_code')
            ->selectRaw('SUM(hw_order_details.amount) AS sum_quantity')
            ->join('hw_orders', 'hw_orders.order_id', 'hw_order_details.order_id')
            ->where('hw_orders.status', 'AZ')
            //->whereDate('hw_orders.created_at', '>=', $from_date)
            //->whereDate('hw_orders.created_at', '<=', $today)
            ->where('hw_orders.timestamp', '>=', strtotime($from_date))
            ->where('hw_orders.timestamp', '<=', strtotime($today))
            ->groupBy('hw_order_details.product_code')
            ->get();

        /*$data = [];
        foreach ($products as $product) {
            if (isset($data[$product->product_code])) {
                $data[$product->product_code] += $product->amount;
            } else {
                $data = [$product->product_code => $product->amount];
            }
        }*/

        return $products;
    }

    public function headings(): array
    {
        return [
            'Product Code',
            'Sum Quantity',
        ];
    }



    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A2:B2'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(16)->getBold();
            },
        ];
    }

}
