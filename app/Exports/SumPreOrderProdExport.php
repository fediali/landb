<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class SumPreOrderProdExport implements FromCollection, ShouldAutoSize, WithHeadings, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $products = DB::connection('mysql2')
            ->table('hw_order_details')
            ->select('hw_order_details.product_code')
            ->selectRaw('SUM(hw_order_details.amount) AS sum_quantity')
            ->join('hw_orders', 'hw_orders.order_id', 'hw_order_details.order_id')
            //->where('hw_orders.status', 'AZ')
            ->where(function ($q) {
                $q->where('hw_orders.status', 'AZ');
                $q->orWhere('hw_orders.status', 'BB');
                $q->orWhere('hw_orders.status', 'BC');
            })
            ->groupBy('hw_order_details.product_code')
            ->get();

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
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:B1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(16)->getBold();
            },
        ];
    }

}
