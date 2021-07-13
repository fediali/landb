<?php

namespace App\Console\Commands;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Chating\Http\Controllers\ChatingController;
use Botble\Chating\Repositories\Interfaces\ChatingInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Textmessages\Models\Textmessages;
use Botble\Textmessages\Repositories\Interfaces\TextmessagesInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;

class sendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Schedule SMS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $chatingRepository;
    protected $customerRepository;
    protected $textmessageRepository;

    public function __construct(
        ChatingInterface $chatingRepository, CustomerInterface $customerRepository, TextmessagesInterface $textmessageRepository)
    {
        parent::__construct();
        $this->chatingRepository = $chatingRepository;
        $this->customerRepository = $customerRepository;
        $this->textmessageRepository = $textmessageRepository;
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
        $text_message = Textmessages::where('schedule_date', '<', $time)->where('status', BaseStatusEnum::SCHEDULE)->pluck('id')->toArray();

        $controller = app(ChatingController::class);
        $d = app()->call([$controller, 'smsCampaign'], ['text_id' => $text_message]);
        return 'Success';
    }
}
