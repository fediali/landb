<?php

namespace Botble\Chating\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Chating\Http\Requests\ChatingRequest;
use Botble\Chating\Models\ChattingRecord;
use Botble\Chating\Repositories\Interfaces\ChatingInterface;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Repositories\Eloquent\CustomerRepository;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Textmessages\Models\Textmessages;
use Botble\Textmessages\Repositories\Interfaces\TextmessagesInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Botble\Chating\Tables\ChatingTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Chating\Forms\ChatingForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Twilio\Exceptions\RestException;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;
use Twilio\Rest\Client;
use Assets;


class ChatingController extends BaseController
{
    /**
     * @var ChatingInterface
     */
    protected $chatingRepository;
    protected $customerRepository;
    protected $textmessageRepository;

    /**
     * @param ChatingInterface $chatingRepository
     */
    public function __construct(ChatingInterface $chatingRepository, CustomerInterface $customerRepository, TextmessagesInterface $textmessageRepository)
    {
        $this->chatingRepository = $chatingRepository;
        $this->customerRepository = $customerRepository;
        $this->textmessageRepository = $textmessageRepository;
    }

    /**
     * @param ChatingTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(ChatingTable $table)
    {
        page_title()->setTitle(trans('plugins/chating::chating.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/chating::chating.create'));

        return $formBuilder->create(ChatingForm::class)->renderForm();
    }

    /**
     * @param ChatingRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(ChatingRequest $request, BaseHttpResponse $response)
    {
        $chating = $this->chatingRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(CHATING_MODULE_SCREEN_NAME, $request, $chating));

        return $response
            ->setPreviousUrl(route('chating.index'))
            ->setNextUrl(route('chating.edit', $chating->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $chating = $this->chatingRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $chating));

        page_title()->setTitle(trans('plugins/chating::chating.edit') . ' "' . $chating->name . '"');

        return $formBuilder->create(ChatingForm::class, ['model' => $chating])->renderForm();
    }

    /**
     * @param int $id
     * @param ChatingRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, ChatingRequest $request, BaseHttpResponse $response)
    {
        $chating = $this->chatingRepository->findOrFail($id);

        $chating->fill($request->input());

        $this->chatingRepository->createOrUpdate($chating);

        event(new UpdatedContentEvent(CHATING_MODULE_SCREEN_NAME, $request, $chating));

        return $response
            ->setPreviousUrl(route('chating.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $chating = $this->chatingRepository->findOrFail($id);

            $this->chatingRepository->delete($chating);

            event(new DeletedContentEvent(CHATING_MODULE_SCREEN_NAME, $request, $chating));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $chating = $this->chatingRepository->findOrFail($id);
            $this->chatingRepository->delete($chating);
            event(new DeletedContentEvent(CHATING_MODULE_SCREEN_NAME, $request, $chating));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }


    public function chatRoom(Request $request)
    {
        page_title()->setTitle('Chat Room');
        $id = Auth::id();
        $customers = get_customers($id);
        //$twilio = new Client(env('TWILIO_AUTH_SID'), env('TWILIO_AUTH_TOKEN'));

        // $conversation = $twilio->conversations->v1->conversations('CH9b2aae73a6724dfc90e66a3eab72efad')->messages->read();
//        dd($conversation[0]->author);
//        foreach ($conversation as $record) {
//
//            $messages = $twilio->conversations->v1
//                ->conversations($record->sid)
//                ->messages->read();
//            (count($messages));
//        }


        return view('plugins/chating::chatRoom', compact('customers'));
    }

    public function chatMessage(Request $request, $ids)
    {
        Assets::addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/libraries/jquery.textarea_autosize.js',
                'vendor/core/plugins/ecommerce/js/chat.js',
            ])
            ->addScripts(['blockui', 'input-mask']);

        $authUser = $request->user();
        $otherUser = Customer::find(explode('-', $ids)[1]);
        $customers = Customer::where('id', '<>', $authUser->id)->where(['is_text' => 1, 'salesperson_id' => $authUser->id])->get();

        $twilio = new Client(env('TWILIO_AUTH_SID'), env('TWILIO_AUTH_TOKEN'));


        /*$conversation = $twilio->conversations->v1->conversations
            ->create([
                    "friendlyName" => "My First Conversation Fedi 2"
                ]
            );

        /*$participant = $twilio->conversations->v1->conversations($conversation->sid)
            ->participants
            ->create([
                    "messagingBindingAddress" => "+14698450619",
                    "messagingBindingProxyAddress" => "+13345390661"
                ]
            );*/

        /*$participant2 = $twilio->conversations->v1->conversations($conversation->sid)
            ->participants
            ->create([
                    "identity" => "testPineapple"
                ]
            );*/

        /*$twilio->messages
            ->create("+14698450619", // to 4698450619
                ["body" => "Hi there", "from" => "+13345390661"]
            );

        $conversation = $twilio->conversations->v1->conversations($conversation->sid)->fetch();*/

        // Fetch channel or create a new one if it doesn't exist
        try {
            // $channel = $twilio->conversations->v1->conversations/*(env('TWILIO_SERVICE_SID'))*/->create(['uniqueName' => $ids])->fetch();
        } catch (\Twilio\Exceptions\RestException $e) {
            // $channel = $twilio->conversations->v1->conversations/*(env('TWILIO_SERVICE_SID'))->channels*/->create(['uniqueName' => $ids, 'type' => 'private']);
        }

        // Add first user to the channel
        try {
            // $firstUser = $authUser->email;
            /*$firstUser = '+13345390661';
            $a = $twilio->conversations->v1->conversations($channel->sid)->participants->create([
                "messagingBindingAddress" => $otherUser->phone,
                "messagingBindingProxyAddress" => $firstUser
            ])->fetch();*/
        } catch (\Twilio\Exceptions\RestException $e) {
            // $a = $twilio->conversations->v1->conversations($channel->sid)->participants;
        }

        // Add second user to the channel
        /*try {
            $twilio->conversations->v1->services(env('TWILIO_SERVICE_SID'))->channels($ids)->members($otherUser->phone)->fetch();
        } catch (\Twilio\Exceptions\RestException $e) {
            $twilio->conversations->v1->services(env('TWILIO_SERVICE_SID'))->channels($ids)->members->create($otherUser->phone);
        }*/

//        $twilio->chat->v2->services("ISc03e88eff7084c42b74f61b34e750747")
//            ->channels("CH5cd6cb9ec79f4005b1a060780da974d6")
//            ->delete();
//
//        $twilio->chat->v2->services("ISc03e88eff7084c42b74f61b34e750747")
//            ->channels("CH5cd6cb9ec79f4005b1a060780da974d6")
//            ->delete();
//
//      $d =   $twilio->chat->v2->services("IS791f2cbcd4484a3cafda2e2c7200f4d0")
//                    ->delete();
//dd($d);
        //Text Message
        //$author = '+13345390661';
        //$body = 'Gand Marwao Gathiye Khaoo';
        //Making Conversation
        //$conversation = $this->makeConversation($ids);
        // New Conversation Participant Add
//        $participant = $this->createSMSParticipant($conversation->sid, $otherUser->phone);
        // Adding Participant
        //$chat = $this->createChatParticipant($conversation->sid, $otherUser->phone);

        //Sending Text
        //$sendMessage = $this->createMessage('CHdf9c1182df404518b8270608826ce544', $author, $body);
        //$sid = 'CH286197bbcbf3448a9f89d46e70691a1b';
        //$messages = $this->listMessages($sid);


        $conversation = $this->makeConversation($ids, $otherUser->detail->business_phone, '', $authUser->twilio_number);
        $sid = $conversation->sid;
        $messages = json_encode($this->listMessages($sid));
        $chatting = get_chat($sid, $authUser->twilio_number);
        $chat = ChattingRecord::where([
            'user_id'     => $authUser->id,
            'customer_id' => $otherUser->id,
            'message_sid' => $conversation->sid,
        ])->first();
        if ($chat) {
            $record['chat_count'] = ($chatting == 0) ? 0 : $chatting;
            ChattingRecord::where('id', $chat->id)->update($record);
        } else {
            $record = [];
            $record['user_id'] = $authUser->id;
            $record['customer_id'] = $otherUser->id;
            $record['message_sid'] = $conversation->sid;
            $record['chat_count'] = 0;
            ChattingRecord::updateOrCreate($record);
        }


        //unread message

        // $participants = $twilio->conversations->v1->conversations($conversation->sid)->participants('MB7b0a555c2dfb47e49ca719a6643bdec4')->fetch();

        return view('plugins/chating::chatMessage', compact('customers', 'otherUser', 'messages', 'sid'));
    }

    public function generateToken(Request $request)
    {
        $token = new AccessToken(env('TWILIO_AUTH_SID'), env('TWILIO_API_SID'), env('TWILIO_API_SECRET'), 3600, $request->email);

        $chatGrant = new ChatGrant();
        $chatGrant->setServiceSid(env('TWILIO_SERVICE_SID'));
        $token->addGrant($chatGrant);

        return response()->json(['token' => $token->toJWT()]);
    }

    public function makeConversation($uniqueName, $number = false, $text = false, $author_number = false)
    {

        $twilio = new Client(env('TWILIO_AUTH_SID'), env('TWILIO_AUTH_TOKEN'));
        try {
            $conversation = $twilio->conversations->v1->conversations($uniqueName)->fetch();
        } catch (RestException $exception) {
            $conversation = $twilio->conversations->v1->conversations->create([
                "friendlyName" => "Conversation-" . $uniqueName,
                "uniqueName"   => $uniqueName,
            ]);
            $this->createSMSParticipant($conversation->sid, $number, $author_number);
        }
        return $conversation;
    }

    public function createSMSParticipant($sid, $number, $author_number = false)
    {

        $twilio = new Client(env('TWILIO_AUTH_SID'), env('TWILIO_AUTH_TOKEN'));
        $participant = $twilio->conversations->v1
            ->conversations($sid)
            ->participants
            ->create([
                'messagingBindingAddress'      => $number,
                'messagingBindingProxyAddress' => $author_number
            ]);

        return $participant;
    }

    public function createChatParticipant($sid, $chat_id)
    {
        $twilio = new Client(env('TWILIO_AUTH_SID'), env('TWILIO_AUTH_TOKEN'));
        $participant = $twilio->conversations->v1
            ->conversations($sid)
            ->participants
            ->create([
                'identity' => $chat_id
            ]);
        return $participant;
    }

    public function createMessage($sid, $author, $body)
    {
        $twilio = new Client(env('TWILIO_AUTH_SID'), env('TWILIO_AUTH_TOKEN'));
        $message = $twilio->conversations->v1
            ->conversations($sid)
            ->messages
            ->create([
                'author' => $author,
                'body'   => $body
            ]);
        return $message;
    }

    public function listMessages($sid)
    {
        $twilio = new Client(env('TWILIO_AUTH_SID'), env('TWILIO_AUTH_TOKEN'));
        $messages = $twilio->conversations->v1
            ->conversations($sid)
            ->messages
            ->read();
        $array = array();
        foreach ($messages as $message) {
            array_push($array, [
                'id'     => $message->sid,
                'author' => $message->author,
                'body'   => $message->body,
                'date'   => $this->convertTime($message->dateCreated)
            ]);
        }
        return $array;
    }

    private function convertTime($date)
    {
        $dt = Carbon::parse($date);
        $new = $dt->toDayDateTimeString();

        return $new;
    }

    public function sendSMS(Request $request)
    {
        $sid = $request->sid;
        $author = $request->author;
        $body = $request->body;
        $this->createMessage($sid, $author, $body);
        $messages = $this->listMessages($sid);
        return response()->json(['messages' => $messages]);
    }

    public function getSMS(Request $request)
    {
        $sid = $request->sid;
        $messages = $this->listMessages($sid);
        return response()->json(['messages' => $messages]);
    }

    public function smsCampaign($text_id)
    {
        //28, 29

        foreach ($text_id as $row) {
            $text = $this->textmessageRepository->findOrFail($row);
            $author = '+13345390661';
            $customer = Customer::where('is_text', 1)->get();
            dd($customer);
            foreach ($customer as $c) {
                $uniqueName = '41-' . $c->id;
                $conversation = $this->makeConversation($uniqueName, $c->detail->business_phone);
                $message = $this->createMessage($conversation->sid, $author, $text->text);
            }
            $status['status'] = BaseStatusEnum::PUBLISHED;
            Textmessages::where('id', $text_id)->update($status);
        }

    }

    public function listAll()
    {
        $twilio = new Client(env('TWILIO_AUTH_SID'), env('TWILIO_AUTH_TOKEN'));
        $messages = $twilio->conversations->v1->conversations->read();

    }


}
