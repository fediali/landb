<?php

namespace Botble\Orderstatuses\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Orderstatuses\Http\Requests\OrderstatusesRequest;
use Botble\Orderstatuses\Repositories\Interfaces\OrderstatusesInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Orderstatuses\Tables\OrderstatusesTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Orderstatuses\Forms\OrderstatusesForm;
use Botble\Base\Forms\FormBuilder;

class OrderstatusesController extends BaseController
{
    /**
     * @var OrderstatusesInterface
     */
    protected $orderstatusesRepository;

    /**
     * @param OrderstatusesInterface $orderstatusesRepository
     */
    public function __construct(OrderstatusesInterface $orderstatusesRepository)
    {
        $this->orderstatusesRepository = $orderstatusesRepository;
    }

    /**
     * @param OrderstatusesTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(OrderstatusesTable $table)
    {
        page_title()->setTitle(trans('plugins/orderstatuses::orderstatuses.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/orderstatuses::orderstatuses.create'));

        return $formBuilder->create(OrderstatusesForm::class)->renderForm();
    }

    /**
     * @param OrderstatusesRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(OrderstatusesRequest $request, BaseHttpResponse $response)
    {
        $orderstatuses = $this->orderstatusesRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(ORDERSTATUSES_MODULE_SCREEN_NAME, $request, $orderstatuses));

        return $response
            ->setPreviousUrl(route('orderstatuses.index'))
            ->setNextUrl(route('orderstatuses.edit', $orderstatuses->id))
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
        $orderstatuses = $this->orderstatusesRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $orderstatuses));

        page_title()->setTitle(trans('plugins/orderstatuses::orderstatuses.edit') . ' "' . $orderstatuses->name . '"');

        return $formBuilder->create(OrderstatusesForm::class, ['model' => $orderstatuses])->renderForm();
    }

    /**
     * @param int $id
     * @param OrderstatusesRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, OrderstatusesRequest $request, BaseHttpResponse $response)
    {
        $orderstatuses = $this->orderstatusesRepository->findOrFail($id);

        $orderstatuses->fill($request->input());

        $this->orderstatusesRepository->createOrUpdate($orderstatuses);

        event(new UpdatedContentEvent(ORDERSTATUSES_MODULE_SCREEN_NAME, $request, $orderstatuses));

        return $response
            ->setPreviousUrl(route('orderstatuses.index'))
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
            $orderstatuses = $this->orderstatusesRepository->findOrFail($id);

            $this->orderstatusesRepository->delete($orderstatuses);

            event(new DeletedContentEvent(ORDERSTATUSES_MODULE_SCREEN_NAME, $request, $orderstatuses));

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
            $orderstatuses = $this->orderstatusesRepository->findOrFail($id);
            $this->orderstatusesRepository->delete($orderstatuses);
            event(new DeletedContentEvent(ORDERSTATUSES_MODULE_SCREEN_NAME, $request, $orderstatuses));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
