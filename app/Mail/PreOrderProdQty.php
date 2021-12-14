<?php

namespace App\Mail;

use App\Exports\PreOrderProdExport;
use App\Exports\SumPreOrderProdExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class PreOrderProdQty extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fileName = 'pre-order-report.xlsx';
        $email =  $this->view('emails.pre_order_prod_qty')->subject('[L&B Pre Order Product Qty]')
        ->attach(Excel::download(new SumPreOrderProdExport, 'sum-'.$fileName)->getFile(), ['as' => 'sum-'.$fileName]);
        return $email;
    }
}
