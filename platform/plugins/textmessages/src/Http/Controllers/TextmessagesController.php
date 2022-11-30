<?php

namespace Botble\Textmessages\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Chating\Http\Controllers\ChatingController;
use Botble\Textmessages\Http\Requests\TextmessagesRequest;
use Botble\Textmessages\Repositories\Interfaces\TextmessagesInterface;
use Botble\Base\Http\Controllers\BaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Botble\Textmessages\Tables\TextmessagesTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Textmessages\Forms\TextmessagesForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\DB;

class TextmessagesController extends BaseController
{
    /**
     * @var TextmessagesInterface
     */
    protected $textmessagesRepository;

    /**
     * @param TextmessagesInterface $textmessagesRepository
     */
    public function __construct(TextmessagesInterface $textmessagesRepository)
    {
        $this->textmessagesRepository = $textmessagesRepository;
    }

    /**
     * @param TextmessagesTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(TextmessagesTable $table)
    {
        page_title()->setTitle(trans('plugins/textmessages::textmessages.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/textmessages::textmessages.create'));

        return $formBuilder->create(TextmessagesForm::class)->renderForm();
    }

    /**
     * @param TextmessagesRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(TextmessagesRequest $request, BaseHttpResponse $response)
    {
        $request['status'] = 'published';
        $request['schedule'] = Carbon::now();
        $request['created_by'] = auth()->id();
        $request['updated_by'] = auth()->id();

        if ($request->has('customer_type')) {
            $phones = [];
            $message = $request->text;
            if ($request->customer_type == 'auto') {
                $limit = $request->customer_range ?? 0;
                $phones = DB::connection('mysql2')->table('hw_users')
                    ->where('status', 'A')
                    ->where('user_type', 'C')
                    ->orderBy('last_login', 'DESC')
                    ->limit($limit)
                    ->pluck('phone')
                    ->all();
            } elseif($request->customer_type == 'manual') {
                if ($request->manual_phone) {
                    $phones = explode(',', $request->manual_phone);
                }
            }
            //4698450619,2142705837,2149846569,2147747178,2149738953,9727622218,2148598121,2148501109,9724086273
            if ($message && count($phones)) {
                app(ChatingController::class)->sendCustomSms($phones, $message);
            }
        }

        /*if (!count($request->customer_ids)) {
            $request['customer_ids'] = get_customers_by_sales_rep();
        }

        $request['customer_ids'] = implode(',', $request['customer_ids']);*/

        $textmessages = $this->textmessagesRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(TEXTMESSAGES_MODULE_SCREEN_NAME, $request, $textmessages));

        return $response
            ->setPreviousUrl(route('textmessages.index'))
            ->setNextUrl(route('textmessages.edit', $textmessages->id))
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
        $textmessages = $this->textmessagesRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $textmessages));

        page_title()->setTitle(trans('plugins/textmessages::textmessages.edit') . ' "' . $textmessages->name . '"');

        return $formBuilder->create(TextmessagesForm::class, ['model' => $textmessages])->renderForm();
    }

    /**
     * @param int $id
     * @param TextmessagesRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, TextmessagesRequest $request, BaseHttpResponse $response)
    {

        $textmessages = $this->textmessagesRepository->findOrFail($id);


        $request['updated_by'] = auth()->id();

        if ($request->customer_ids == null) {
            $request['customer_ids'] = array_keys(get_customers_by_sales_rep());
        }

        $request['customer_ids'] = implode(',', $request['customer_ids']);


        $textmessages->fill($request->input());

        $this->textmessagesRepository->createOrUpdate($textmessages);

        event(new UpdatedContentEvent(TEXTMESSAGES_MODULE_SCREEN_NAME, $request, $textmessages));

        return $response
            ->setPreviousUrl(route('textmessages.index'))
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
            $textmessages = $this->textmessagesRepository->findOrFail($id);

            $this->textmessagesRepository->delete($textmessages);

            event(new DeletedContentEvent(TEXTMESSAGES_MODULE_SCREEN_NAME, $request, $textmessages));

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
            $textmessages = $this->textmessagesRepository->findOrFail($id);
            $this->textmessagesRepository->delete($textmessages);
            event(new DeletedContentEvent(TEXTMESSAGES_MODULE_SCREEN_NAME, $request, $textmessages));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
