<?php

namespace Botble\Vendororderstatuses\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Vendororderstatuses\Http\Requests\VendororderstatusesRequest;
use Botble\Vendororderstatuses\Repositories\Interfaces\VendororderstatusesInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Vendororderstatuses\Tables\VendororderstatusesTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Vendororderstatuses\Forms\VendororderstatusesForm;
use Botble\Base\Forms\FormBuilder;

class VendororderstatusesController extends BaseController
{
    /**
     * @var VendororderstatusesInterface
     */
    protected $vendororderstatusesRepository;

    /**
     * @param VendororderstatusesInterface $vendororderstatusesRepository
     */
    public function __construct(VendororderstatusesInterface $vendororderstatusesRepository)
    {
        $this->vendororderstatusesRepository = $vendororderstatusesRepository;
    }

    /**
     * @param VendororderstatusesTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(VendororderstatusesTable $table)
    {
        page_title()->setTitle(trans('plugins/vendororderstatuses::vendororderstatuses.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/vendororderstatuses::vendororderstatuses.create'));

        return $formBuilder->create(VendororderstatusesForm::class)->renderForm();
    }

    /**
     * @param VendororderstatusesRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(VendororderstatusesRequest $request, BaseHttpResponse $response)
    {
        $vendororderstatuses = $this->vendororderstatusesRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(VENDORORDERSTATUSES_MODULE_SCREEN_NAME, $request, $vendororderstatuses));

        return $response
            ->setPreviousUrl(route('vendororderstatuses.index'))
            ->setNextUrl(route('vendororderstatuses.edit', $vendororderstatuses->id))
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
        $vendororderstatuses = $this->vendororderstatusesRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $vendororderstatuses));

        page_title()->setTitle(trans('plugins/vendororderstatuses::vendororderstatuses.edit') . ' "' . $vendororderstatuses->name . '"');

        return $formBuilder->create(VendororderstatusesForm::class, ['model' => $vendororderstatuses])->renderForm();
    }

    /**
     * @param int $id
     * @param VendororderstatusesRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, VendororderstatusesRequest $request, BaseHttpResponse $response)
    {
        $vendororderstatuses = $this->vendororderstatusesRepository->findOrFail($id);

        $vendororderstatuses->fill($request->input());

        $this->vendororderstatusesRepository->createOrUpdate($vendororderstatuses);

        event(new UpdatedContentEvent(VENDORORDERSTATUSES_MODULE_SCREEN_NAME, $request, $vendororderstatuses));

        return $response
            ->setPreviousUrl(route('vendororderstatuses.index'))
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
            $vendororderstatuses = $this->vendororderstatusesRepository->findOrFail($id);

            $this->vendororderstatusesRepository->delete($vendororderstatuses);

            event(new DeletedContentEvent(VENDORORDERSTATUSES_MODULE_SCREEN_NAME, $request, $vendororderstatuses));

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
            $vendororderstatuses = $this->vendororderstatusesRepository->findOrFail($id);
            $this->vendororderstatusesRepository->delete($vendororderstatuses);
            event(new DeletedContentEvent(VENDORORDERSTATUSES_MODULE_SCREEN_NAME, $request, $vendororderstatuses));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
