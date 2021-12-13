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

    public $dates;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($dates)
    {
        $this->dates = $dates;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $date = date('m-d-Y', strtotime($this->dates['from_date'])).' to '.date('m-d-Y', strtotime($this->dates['to_date']));
        $fileName = 'report-' . $date . '.xlsx';
        $email =  $this->view('emails.pre_order_prod_qty')->subject('[L&B Pre Order Product Qty]['.$date.']')
        ->attach(Excel::download(new SumPreOrderProdExport, 'sum--'.$fileName)->getFile(), ['as' => 'sum--'.$fileName]);
        //->attach(Excel::download(new PreOrderProdExport, $fileName)->getFile(), ['as' => $fileName]);
        return $email;
            //->from('')
            //->replyTo($this->data['email'])
            //->with(['data' => $this->data]);
    }
}
