<?php

namespace Botble\Vendororder\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Vendororder\Http\Requests\VendororderRequest;
use Botble\Vendororder\Repositories\Interfaces\VendororderInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Vendororder\Tables\VendororderTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Vendororder\Forms\VendororderForm;
use Botble\Base\Forms\FormBuilder;

class VendororderController extends BaseController
{
    /**
     * @var VendororderInterface
     */
    protected $vendororderRepository;

    /**
     * @param VendororderInterface $vendororderRepository
     */
    public function __construct(VendororderInterface $vendororderRepository)
    {
        $this->vendororderRepository = $vendororderRepository;
    }

    /**
     * @param VendororderTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(VendororderTable $table)
    {
        page_title()->setTitle(trans('plugins/vendororder::vendororder.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/vendororder::vendororder.create'));

        return $formBuilder->create(VendororderForm::class)->renderForm();
    }

    /**
     * @param VendororderRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(VendororderRequest $request, BaseHttpResponse $response)
    {
        $vendororder = $this->vendororderRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(VENDORORDER_MODULE_SCREEN_NAME, $request, $vendororder));

        return $response
            ->setPreviousUrl(route('vendororder.index'))
            ->setNextUrl(route('vendororder.edit', $vendororder->id))
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
        $vendororder = $this->vendororderRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $vendororder));

        page_title()->setTitle(trans('plugins/vendororder::vendororder.edit') . ' "' . $vendororder->name . '"');

        return $formBuilder->create(VendororderForm::class, ['model' => $vendororder])->renderForm();
    }

    /**
     * @param int $id
     * @param VendororderRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, VendororderRequest $request, BaseHttpResponse $response)
    {
        $vendororder = $this->vendororderRepository->findOrFail($id);

        $vendororder->fill($request->input());

        $this->vendororderRepository->createOrUpdate($vendororder);

        event(new UpdatedContentEvent(VENDORORDER_MODULE_SCREEN_NAME, $request, $vendororder));

        return $response
            ->setPreviousUrl(route('vendororder.index'))
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
            $vendororder = $this->vendororderRepository->findOrFail($id);

            $this->vendororderRepository->delete($vendororder);

            event(new DeletedContentEvent(VENDORORDER_MODULE_SCREEN_NAME, $request, $vendororder));

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
            $vendororder = $this->vendororderRepository->findOrFail($id);
            $this->vendororderRepository->delete($vendororder);
            event(new DeletedContentEvent(VENDORORDER_MODULE_SCREEN_NAME, $request, $vendororder));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
