<?php

namespace Botble\Seasons\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Seasons\Http\Requests\SeasonsRequest;
use Botble\Seasons\Repositories\Interfaces\SeasonsInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Seasons\Tables\SeasonsTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Seasons\Forms\SeasonsForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\Auth;

class SeasonsController extends BaseController
{
    /**
     * @var SeasonsInterface
     */
    protected $seasonsRepository;

    /**
     * @param SeasonsInterface $seasonsRepository
     */
    public function __construct(SeasonsInterface $seasonsRepository)
    {
        $this->seasonsRepository = $seasonsRepository;
    }

    /**
     * @param SeasonsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(SeasonsTable $table)
    {
        page_title()->setTitle(trans('plugins/seasons::seasons.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/seasons::seasons.create'));

        return $formBuilder->create(SeasonsForm::class)->renderForm();
    }

    /**
     * @param SeasonsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(SeasonsRequest $request, BaseHttpResponse $response)
    {
        $data = $request->all();
        $data['created_by'] = Auth::user()->id;
        $seasons = $this->seasonsRepository->createOrUpdate($data);

        event(new CreatedContentEvent(SEASONS_MODULE_SCREEN_NAME, $request, $seasons));

        return $response
            ->setPreviousUrl(route('seasons.index'))
            ->setNextUrl(route('seasons.edit', $seasons->id))
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
        $seasons = $this->seasonsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $seasons));

        page_title()->setTitle(trans('plugins/seasons::seasons.edit') . ' "' . $seasons->name . '"');

        return $formBuilder->create(SeasonsForm::class, ['model' => $seasons])->renderForm();
    }

    /**
     * @param int $id
     * @param SeasonsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, SeasonsRequest $request, BaseHttpResponse $response)
    {
        $seasons = $this->seasonsRepository->findOrFail($id);
        $data = $request->all();
        $data['updated_by'] = Auth::user()->id;
        $seasons->fill($data);

        $this->seasonsRepository->createOrUpdate($seasons);

        event(new UpdatedContentEvent(SEASONS_MODULE_SCREEN_NAME, $request, $seasons));

        return $response
            ->setPreviousUrl(route('seasons.index'))
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
            $seasons = $this->seasonsRepository->findOrFail($id);

            $this->seasonsRepository->delete($seasons);

            event(new DeletedContentEvent(SEASONS_MODULE_SCREEN_NAME, $request, $seasons));

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
            $seasons = $this->seasonsRepository->findOrFail($id);
            $this->seasonsRepository->delete($seasons);
            event(new DeletedContentEvent(SEASONS_MODULE_SCREEN_NAME, $request, $seasons));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
