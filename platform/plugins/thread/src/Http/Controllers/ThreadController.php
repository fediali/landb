<?php

namespace Botble\Thread\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Thread\Http\Requests\ThreadRequest;
use Botble\Thread\Repositories\Interfaces\ThreadInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Thread\Tables\ThreadTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Thread\Forms\ThreadForm;
use Botble\Base\Forms\FormBuilder;

class ThreadController extends BaseController
{
    /**
     * @var ThreadInterface
     */
    protected $threadRepository;

    /**
     * @param ThreadInterface $threadRepository
     */
    public function __construct(ThreadInterface $threadRepository)
    {
        $this->threadRepository = $threadRepository;
    }

    /**
     * @param ThreadTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(ThreadTable $table)
    {
        page_title()->setTitle(trans('plugins/thread::thread.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/thread::thread.create'));

        return $formBuilder->create(ThreadForm::class)->renderForm();
    }

    /**
     * @param ThreadRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(ThreadRequest $request, BaseHttpResponse $response)
    {
        $thread = $this->threadRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $thread));

        return $response
            ->setPreviousUrl(route('thread.index'))
            ->setNextUrl(route('thread.edit', $thread->id))
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
        $thread = $this->threadRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $thread));

        page_title()->setTitle(trans('plugins/thread::thread.edit') . ' "' . $thread->name . '"');

        return $formBuilder->create(ThreadForm::class, ['model' => $thread])->renderForm();
    }

    /**
     * @param int $id
     * @param ThreadRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, ThreadRequest $request, BaseHttpResponse $response)
    {
        $thread = $this->threadRepository->findOrFail($id);

        $thread->fill($request->input());

        $this->threadRepository->createOrUpdate($thread);

        event(new UpdatedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $thread));

        return $response
            ->setPreviousUrl(route('thread.index'))
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
            $thread = $this->threadRepository->findOrFail($id);

            $this->threadRepository->delete($thread);

            event(new DeletedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $thread));

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
            $thread = $this->threadRepository->findOrFail($id);
            $this->threadRepository->delete($thread);
            event(new DeletedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $thread));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
