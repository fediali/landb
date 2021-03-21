<?php

namespace Botble\Packs\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Packs\Http\Requests\PacksRequest;
use Botble\Packs\Repositories\Interfaces\PacksInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Packs\Tables\PacksTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Packs\Forms\PacksForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\Auth;

class PacksController extends BaseController
{
    /**
     * @var PacksInterface
     */
    protected $packsRepository;

    /**
     * @param PacksInterface $packsRepository
     */
    public function __construct(PacksInterface $packsRepository)
    {
        $this->packsRepository = $packsRepository;
    }

    /**
     * @param PacksTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(PacksTable $table)
    {
        page_title()->setTitle(trans('plugins/packs::packs.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/packs::packs.create'));

        return $formBuilder->create(PacksForm::class)->renderForm();
    }

    /**
     * @param PacksRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(PacksRequest $request, BaseHttpResponse $response)
    {
        $created_by = Auth::user()->id;
        $data = $request->all();
        $data['created_by'] = $created_by;
        $packs = $this->packsRepository->createOrUpdate($data);

        event(new CreatedContentEvent(PACKS_MODULE_SCREEN_NAME, $request, $packs));

        return $response
            ->setPreviousUrl(route('packs.index'))
            ->setNextUrl(route('packs.edit', $packs->id))
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
        $packs = $this->packsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $packs));

        page_title()->setTitle(trans('plugins/packs::packs.edit') . ' "' . $packs->name . '"');

        return $formBuilder->create(PacksForm::class, ['model' => $packs])->renderForm();
    }

    /**
     * @param int $id
     * @param PacksRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, PacksRequest $request, BaseHttpResponse $response)
    {
        $packs = $this->packsRepository->findOrFail($id);
        $data = $request->all();
        $data['updated_by'] = Auth::user()->id;
        $packs->fill($data);

        $this->packsRepository->createOrUpdate($packs);

        event(new UpdatedContentEvent(PACKS_MODULE_SCREEN_NAME, $request, $packs));

        return $response
            ->setPreviousUrl(route('packs.index'))
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
            $packs = $this->packsRepository->findOrFail($id);

            $this->packsRepository->delete($packs);

            event(new DeletedContentEvent(PACKS_MODULE_SCREEN_NAME, $request, $packs));

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
            $packs = $this->packsRepository->findOrFail($id);
            $this->packsRepository->delete($packs);
            event(new DeletedContentEvent(PACKS_MODULE_SCREEN_NAME, $request, $packs));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
