<?php

namespace Botble\Threadsample\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Thread\Repositories\Interfaces\ThreadInterface;
use Botble\Threadsample\Http\Requests\ThreadsampleRequest;
use Botble\Threadsample\Repositories\Interfaces\ThreadsampleInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Threadsample\Tables\ThreadsampleTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Threadsample\Forms\ThreadsampleForm;
use Botble\Base\Forms\FormBuilder;

class ThreadsampleController extends BaseController
{
    /**
     * @var ThreadsampleInterface
     */
    protected $threadsampleRepository;
    protected $threadRepository;

    /**
     * @param ThreadsampleInterface $threadsampleRepository
     */
    public function __construct(ThreadsampleInterface $threadsampleRepository, ThreadInterface $threadRepository)
    {
        $this->threadsampleRepository = $threadsampleRepository;
        $this->threadRepository = $threadRepository;
    }

    /**
     * @param ThreadsampleTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(ThreadsampleTable $table)
    {
        page_title()->setTitle(trans('plugins/threadsample::threadsample.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/threadsample::threadsample.create'));

        return $formBuilder->create(ThreadsampleForm::class)->renderForm();
    }

    /**
     * @param ThreadsampleRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(ThreadsampleRequest $request, BaseHttpResponse $response)
    {
        $threadsample = $this->threadsampleRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(THREADSAMPLE_MODULE_SCREEN_NAME, $request, $threadsample));

        return $response
            ->setPreviousUrl(route('threadsample.index'))
            ->setNextUrl(route('threadsample.edit', $threadsample->id))
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
        $threadsample = $this->threadsampleRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $threadsample));

        page_title()->setTitle(trans('plugins/threadsample::threadsample.edit') . ' "' . $threadsample->name . '"');

        return $formBuilder->create(ThreadsampleForm::class, ['model' => $threadsample])->renderForm();
    }

    /**
     * @param int $id
     * @param ThreadsampleRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, ThreadsampleRequest $request, BaseHttpResponse $response)
    {
        $threadsample = $this->threadsampleRepository->findOrFail($id);

        $threadsample->fill($request->input());

        $this->threadsampleRepository->createOrUpdate($threadsample);

        event(new UpdatedContentEvent(THREADSAMPLE_MODULE_SCREEN_NAME, $request, $threadsample));

        return $response
            ->setPreviousUrl(route('threadsample.index'))
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
            $threadsample = $this->threadsampleRepository->findOrFail($id);

            $this->threadsampleRepository->delete($threadsample);

            event(new DeletedContentEvent(THREADSAMPLE_MODULE_SCREEN_NAME, $request, $threadsample));

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
            $threadsample = $this->threadsampleRepository->findOrFail($id);
            $this->threadsampleRepository->delete($threadsample);
            event(new DeletedContentEvent(THREADSAMPLE_MODULE_SCREEN_NAME, $request, $threadsample));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function show(Request $request, $id)
    {
        $thread = $this->threadRepository->findOrFail($id);
        $request['thread_id'] = $thread->id;
        $request['name'] = $thread->name;
        $threadsample = $this->threadsampleRepository->createOrUpdate($request->input());

        return redirect()->route('threadsample.edit', $threadsample->id);
    }
}
