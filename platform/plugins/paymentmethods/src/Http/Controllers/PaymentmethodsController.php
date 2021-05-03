<?php

namespace Botble\Paymentmethods\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Paymentmethods\Http\Requests\PaymentmethodsRequest;
use Botble\Paymentmethods\Repositories\Interfaces\PaymentmethodsInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Paymentmethods\Tables\PaymentmethodsTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Paymentmethods\Forms\PaymentmethodsForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Str;

class PaymentmethodsController extends BaseController
{
    /**
     * @var PaymentmethodsInterface
     */
    protected $paymentmethodsRepository;

    /**
     * @param PaymentmethodsInterface $paymentmethodsRepository
     */
    public function __construct(PaymentmethodsInterface $paymentmethodsRepository)
    {
        $this->paymentmethodsRepository = $paymentmethodsRepository;
    }

    /**
     * @param PaymentmethodsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(PaymentmethodsTable $table)
    {
        page_title()->setTitle(trans('plugins/paymentmethods::paymentmethods.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/paymentmethods::paymentmethods.create'));

        return $formBuilder->create(PaymentmethodsForm::class)->renderForm();
    }

    /**
     * @param PaymentmethodsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(PaymentmethodsRequest $request, BaseHttpResponse $response)
    {
        $params = $request->input();
        $params['slug'] = Str::slug($request->input('name'),'-');
        $paymentmethods = $this->paymentmethodsRepository->createOrUpdate($params);

        event(new CreatedContentEvent(PAYMENTMETHODS_MODULE_SCREEN_NAME, $request, $paymentmethods));

        return $response
            ->setPreviousUrl(route('paymentmethods.index'))
            ->setNextUrl(route('paymentmethods.edit', $paymentmethods->id))
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
        $paymentmethods = $this->paymentmethodsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $paymentmethods));

        page_title()->setTitle(trans('plugins/paymentmethods::paymentmethods.edit') . ' "' . $paymentmethods->name . '"');

        return $formBuilder->create(PaymentmethodsForm::class, ['model' => $paymentmethods])->renderForm();
    }

    /**
     * @param int $id
     * @param PaymentmethodsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, PaymentmethodsRequest $request, BaseHttpResponse $response)
    {
        $paymentmethods = $this->paymentmethodsRepository->findOrFail($id);

        $params = $request->input();
        $params['slug'] = Str::slug($request->input('name'),'-');
        $paymentmethods->fill($params);

        $this->paymentmethodsRepository->createOrUpdate($paymentmethods);

        event(new UpdatedContentEvent(PAYMENTMETHODS_MODULE_SCREEN_NAME, $request, $paymentmethods));

        return $response
            ->setPreviousUrl(route('paymentmethods.index'))
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
            $paymentmethods = $this->paymentmethodsRepository->findOrFail($id);

            $this->paymentmethodsRepository->delete($paymentmethods);

            event(new DeletedContentEvent(PAYMENTMETHODS_MODULE_SCREEN_NAME, $request, $paymentmethods));

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
            $paymentmethods = $this->paymentmethodsRepository->findOrFail($id);
            $this->paymentmethodsRepository->delete($paymentmethods);
            event(new DeletedContentEvent(PAYMENTMETHODS_MODULE_SCREEN_NAME, $request, $paymentmethods));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
