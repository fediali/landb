<?php

namespace App\Console\Commands;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Product;
use Botble\Payment\Models\Payment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PayPal\Api\Invoice;

class checkPaypalInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:paypal-invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Paypal Invoice';

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
        //LIVE
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                env('PAYPAL_SANDBOX_CLIENT_ID', 'AciZq5ZxzoDCQB03f_TzwP9dC6UB2GqihP4s_gu3DJLfcVryAB8sf2UFTCWXl5rSSagaKXHYDpwL_xpP'),     // ClientID
                env('PAYPAL_SANDBOX_CLIENT_SECRET', 'EHgN0b0AItivo9SztzVFr5ZUVshS_MdagqUcaHHjX-QLdNRbbPZGeKdVZDA2ebr9JRjyCeMDKJSrQgdQ')     // ClientSecret
            )
        );

        $apiContext->setConfig(
            array(
                'mode' => env('PAYPAL_MODE', 'live'),
                'log.LogEnabled' => false,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => 'INFO', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
            )
        );

        try {
            $paypal = Payment::where('status', 'pending')->where('payment_channel', 'paypal')->get();
            foreach ($paypal as $pay) {
                if ($pay->paypal_invoice_id) {
                    $invoice = Invoice::get($pay->paypal_invoice_id, $apiContext);
                    if ($invoice->status == 'PAID') {
                        Payment::where('id', $pay->id)->update(['status' => 'completed']);
                        /*if ($pay->type == 0) {
                            $status['invoice_status'] = 1;
                            HwOrder::where('order_id', $pay->order_id)->update($status);
                        } else {
                            $status['product_invoice_status'] = 1;
                            HwOrder::where('order_id', $pay->order_id)->update($status);
                        }*/
                    }
                }
            }
            return 'Invoice Checked';
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode(); // Prints the Error Code
            echo $ex->getData();
            die($ex);
        }
    }
}
