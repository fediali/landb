<?php

namespace Botble\Vendorproducts\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Vendorproducts\Http\Requests\VendorproductsRequest;
use Botble\Vendorproducts\Repositories\Interfaces\VendorproductsInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Vendorproducts\Tables\VendorproductsTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Vendorproducts\Forms\VendorproductsForm;
use Botble\Base\Forms\FormBuilder;

class VendorproductsController extends BaseController
{
    /**
     * @var VendorproductsInterface
     */
    protected $vendorproductsRepository;

    /**
     * @param VendorproductsInterface $vendorproductsRepository
     */
    public function __construct(VendorproductsInterface $vendorproductsRepository)
    {
        $this->vendorproductsRepository = $vendorproductsRepository;
    }

    /**
     * @param VendorproductsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(VendorproductsTable $table)
    {
        page_title()->setTitle(trans('plugins/vendorproducts::vendorproducts.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/vendorproducts::vendorproducts.create'));

        return $formBuilder->create(VendorproductsForm::class)->renderForm();
    }

    /**
     * @param VendorproductsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(VendorproductsRequest $request, BaseHttpResponse $response)
    {
        $vendorproducts = $this->vendorproductsRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(VENDORPRODUCTS_MODULE_SCREEN_NAME, $request, $vendorproducts));

        return $response
            ->setPreviousUrl(route('vendorproducts.index'))
            ->setNextUrl(route('vendorproducts.edit', $vendorproducts->id))
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
        $vendorproducts = $this->vendorproductsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $vendorproducts));

        page_title()->setTitle(trans('plugins/vendorproducts::vendorproducts.edit') . ' "' . $vendorproducts->name . '"');

        return $formBuilder->create(VendorproductsForm::class, ['model' => $vendorproducts])->renderForm();
    }

    /**
     * @param int $id
     * @param VendorproductsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, VendorproductsRequest $request, BaseHttpResponse $response)
    {
        $vendorproducts = $this->vendorproductsRepository->findOrFail($id);

        $vendorproducts->fill($request->input());

        $this->vendorproductsRepository->createOrUpdate($vendorproducts);

        event(new UpdatedContentEvent(VENDORPRODUCTS_MODULE_SCREEN_NAME, $request, $vendorproducts));

        return $response
            ->setPreviousUrl(route('vendorproducts.index'))
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
            $vendorproducts = $this->vendorproductsRepository->findOrFail($id);

            $this->vendorproductsRepository->delete($vendorproducts);

            event(new DeletedContentEvent(VENDORPRODUCTS_MODULE_SCREEN_NAME, $request, $vendorproducts));

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
            $vendorproducts = $this->vendorproductsRepository->findOrFail($id);
            $this->vendorproductsRepository->delete($vendorproducts);
            event(new DeletedContentEvent(VENDORPRODUCTS_MODULE_SCREEN_NAME, $request, $vendorproducts));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
