<?php

namespace Botble\Vendorproductunits\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Vendorproductunits\Http\Requests\VendorproductunitsRequest;
use Botble\Vendorproductunits\Repositories\Interfaces\VendorproductunitsInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Vendorproductunits\Tables\VendorproductunitsTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Vendorproductunits\Forms\VendorproductunitsForm;
use Botble\Base\Forms\FormBuilder;

class VendorproductunitsController extends BaseController
{
    /**
     * @var VendorproductunitsInterface
     */
    protected $vendorproductunitsRepository;

    /**
     * @param VendorproductunitsInterface $vendorproductunitsRepository
     */
    public function __construct(VendorproductunitsInterface $vendorproductunitsRepository)
    {
        $this->vendorproductunitsRepository = $vendorproductunitsRepository;
    }

    /**
     * @param VendorproductunitsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(VendorproductunitsTable $table)
    {
        page_title()->setTitle(trans('plugins/vendorproductunits::vendorproductunits.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/vendorproductunits::vendorproductunits.create'));

        return $formBuilder->create(VendorproductunitsForm::class)->renderForm();
    }

    /**
     * @param VendorproductunitsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(VendorproductunitsRequest $request, BaseHttpResponse $response)
    {
        $vendorproductunits = $this->vendorproductunitsRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(VENDORPRODUCTUNITS_MODULE_SCREEN_NAME, $request, $vendorproductunits));

        return $response
            ->setPreviousUrl(route('vendorproductunits.index'))
            ->setNextUrl(route('vendorproductunits.edit', $vendorproductunits->id))
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
        $vendorproductunits = $this->vendorproductunitsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $vendorproductunits));

        page_title()->setTitle(trans('plugins/vendorproductunits::vendorproductunits.edit') . ' "' . $vendorproductunits->name . '"');

        return $formBuilder->create(VendorproductunitsForm::class, ['model' => $vendorproductunits])->renderForm();
    }

    /**
     * @param int $id
     * @param VendorproductunitsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, VendorproductunitsRequest $request, BaseHttpResponse $response)
    {
        $vendorproductunits = $this->vendorproductunitsRepository->findOrFail($id);

        $vendorproductunits->fill($request->input());

        $this->vendorproductunitsRepository->createOrUpdate($vendorproductunits);

        event(new UpdatedContentEvent(VENDORPRODUCTUNITS_MODULE_SCREEN_NAME, $request, $vendorproductunits));

        return $response
            ->setPreviousUrl(route('vendorproductunits.index'))
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
            $vendorproductunits = $this->vendorproductunitsRepository->findOrFail($id);

            $this->vendorproductunitsRepository->delete($vendorproductunits);

            event(new DeletedContentEvent(VENDORPRODUCTUNITS_MODULE_SCREEN_NAME, $request, $vendorproductunits));

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
            $vendorproductunits = $this->vendorproductunitsRepository->findOrFail($id);
            $this->vendorproductunitsRepository->delete($vendorproductunits);
            event(new DeletedContentEvent(VENDORPRODUCTUNITS_MODULE_SCREEN_NAME, $request, $vendorproductunits));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
