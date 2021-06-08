<?php

namespace Botble\Textmessages\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Textmessages\Http\Requests\TextmessagesRequest;
use Botble\Textmessages\Repositories\Interfaces\TextmessagesInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Textmessages\Tables\TextmessagesTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Textmessages\Forms\TextmessagesForm;
use Botble\Base\Forms\FormBuilder;

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
