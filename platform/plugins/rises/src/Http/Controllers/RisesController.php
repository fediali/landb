<?php

namespace Botble\Rises\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Rises\Http\Requests\RisesRequest;
use Botble\Rises\Repositories\Interfaces\RisesInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Rises\Tables\RisesTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Rises\Forms\RisesForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\Auth;

class RisesController extends BaseController
{
    /**
     * @var RisesInterface
     */
    protected $risesRepository;

    /**
     * @param RisesInterface $risesRepository
     */
    public function __construct(RisesInterface $risesRepository)
    {
        $this->risesRepository = $risesRepository;
    }

    /**
     * @param RisesTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(RisesTable $table)
    {
        page_title()->setTitle(trans('plugins/rises::rises.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/rises::rises.create'));

        return $formBuilder->create(RisesForm::class)->renderForm();
    }

    /**
     * @param RisesRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(RisesRequest $request, BaseHttpResponse $response)
    {
        $data = $request->all();
        $data['created_by'] = Auth::user()->id;
        $rises = $this->risesRepository->createOrUpdate($data);

        event(new CreatedContentEvent(RISES_MODULE_SCREEN_NAME, $request, $rises));

        return $response
            ->setPreviousUrl(route('rises.index'))
            ->setNextUrl(route('rises.edit', $rises->id))
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
        $rises = $this->risesRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $rises));

        page_title()->setTitle(trans('plugins/rises::rises.edit') . ' "' . $rises->name . '"');

        return $formBuilder->create(RisesForm::class, ['model' => $rises])->renderForm();
    }

    /**
     * @param int $id
     * @param RisesRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, RisesRequest $request, BaseHttpResponse $response)
    {
        $rises = $this->risesRepository->findOrFail($id);
        $data = $request->all();
        $data['updated_by'] = Auth::user()->id;
        $rises->fill($data);

        $this->risesRepository->createOrUpdate($rises);

        event(new UpdatedContentEvent(RISES_MODULE_SCREEN_NAME, $request, $rises));

        return $response
            ->setPreviousUrl(route('rises.index'))
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
            $rises = $this->risesRepository->findOrFail($id);

            $this->risesRepository->delete($rises);

            event(new DeletedContentEvent(RISES_MODULE_SCREEN_NAME, $request, $rises));

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
            $rises = $this->risesRepository->findOrFail($id);
            $this->risesRepository->delete($rises);
            event(new DeletedContentEvent(RISES_MODULE_SCREEN_NAME, $request, $rises));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
