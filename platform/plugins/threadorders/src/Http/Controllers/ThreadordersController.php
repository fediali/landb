<?php

namespace Botble\Threadorders\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Thread\Repositories\Interfaces\ThreadInterface;
use Botble\Threadorders\Http\Requests\ThreadordersRequest;
use Botble\Threadorders\Repositories\Interfaces\ThreadordersInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Threadorders\Tables\ThreadordersTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Threadorders\Forms\ThreadordersForm;
use Botble\Base\Forms\FormBuilder;

class ThreadordersController extends BaseController
{
    /**
     * @var ThreadordersInterface
     */
    protected $threadordersRepository;

    /**
     * @var ThreadInterface
     */
    protected $threadRepository;

    /**
     * @param ThreadordersInterface $threadordersRepository
     * @param ThreadInterface $threadRepository
     */
    public function __construct(ThreadordersInterface $threadordersRepository, ThreadInterface $threadRepository)
    {
        $this->threadordersRepository = $threadordersRepository;
        $this->threadRepository = $threadRepository;
    }

    /**
     * @param ThreadordersTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(ThreadordersTable $table)
    {
        page_title()->setTitle(trans('plugins/threadorders::threadorders.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/threadorders::threadorders.create'));

        return $formBuilder->create(ThreadordersForm::class)->renderForm();
    }

    /**
     * @param ThreadordersRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(ThreadordersRequest $request, BaseHttpResponse $response)
    {
        $threadorders = $this->threadordersRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(THREADORDERS_MODULE_SCREEN_NAME, $request, $threadorders));

        return $response
            ->setPreviousUrl(route('threadorders.index'))
            ->setNextUrl(route('threadorders.edit', $threadorders->id))
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
        $threadorders = $this->threadordersRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $threadorders));

        page_title()->setTitle(trans('plugins/threadorders::threadorders.edit') . ' "' . $threadorders->name . '"');

        return $formBuilder->create(ThreadordersForm::class, ['model' => $threadorders])->renderForm();
    }

    /**
     * @param int $id
     * @param ThreadordersRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, ThreadordersRequest $request, BaseHttpResponse $response)
    {
        $threadorders = $this->threadordersRepository->findOrFail($id);

        $threadorders->fill($request->input());

        $this->threadordersRepository->createOrUpdate($threadorders);

        event(new UpdatedContentEvent(THREADORDERS_MODULE_SCREEN_NAME, $request, $threadorders));

        return $response
            ->setPreviousUrl(route('threadorders.index'))
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
            $threadorders = $this->threadordersRepository->findOrFail($id);

            $this->threadordersRepository->delete($threadorders);

            event(new DeletedContentEvent(THREADORDERS_MODULE_SCREEN_NAME, $request, $threadorders));

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
            $threadorders = $this->threadordersRepository->findOrFail($id);
            $this->threadordersRepository->delete($threadorders);
            event(new DeletedContentEvent(THREADORDERS_MODULE_SCREEN_NAME, $request, $threadorders));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function createThreadOrder($id, FormBuilder $formBuilder, Request $request)
    {
        $thread = $this->threadRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $thread));

        page_title()->setTitle(trans('plugins/threadorders::threadorders.create'));

        return $formBuilder->create(ThreadordersForm::class, ['model' => $thread])->renderForm();
    }

}
