<?php

namespace Botble\Accountingsystem\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Accountingsystem\Http\Requests\AccountingsystemRequest;
use Botble\Accountingsystem\Repositories\Interfaces\AccountingsystemInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Accountingsystem\Tables\AccountingsystemTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Accountingsystem\Forms\AccountingsystemForm;
use Botble\Base\Forms\FormBuilder;

class AccountingsystemController extends BaseController
{
    /**
     * @var AccountingsystemInterface
     */
    protected $accountingsystemRepository;

    /**
     * @param AccountingsystemInterface $accountingsystemRepository
     */
    public function __construct(AccountingsystemInterface $accountingsystemRepository)
    {
        $this->accountingsystemRepository = $accountingsystemRepository;
    }

    /**
     * @param AccountingsystemTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(AccountingsystemTable $table)
    {
        page_title()->setTitle(trans('plugins/accountingsystem::accountingsystem.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/accountingsystem::accountingsystem.create'));

        return $formBuilder->create(AccountingsystemForm::class)->renderForm();
    }

    /**
     * @param AccountingsystemRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(AccountingsystemRequest $request, BaseHttpResponse $response)
    {
        $requestData = $request->input();
        $requestData['business_id'] = 1;
        $requestData['created_by'] = auth()->user()->id;
        $requestData['updated_by'] = auth()->user()->id;

        $accountingsystem = $this->accountingsystemRepository->createOrUpdate($requestData);

        event(new CreatedContentEvent(ACCOUNTINGSYSTEM_MODULE_SCREEN_NAME, $request, $accountingsystem));

        return $response
            ->setPreviousUrl(route('accountingsystem.index'))
            ->setNextUrl(route('accountingsystem.edit', $accountingsystem->id))
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
        $accountingsystem = $this->accountingsystemRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $accountingsystem));

        page_title()->setTitle(trans('plugins/accountingsystem::accountingsystem.edit') . ' "' . $accountingsystem->name . '"');

        return $formBuilder->create(AccountingsystemForm::class, ['model' => $accountingsystem])->renderForm();
    }

    /**
     * @param int $id
     * @param AccountingsystemRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, AccountingsystemRequest $request, BaseHttpResponse $response)
    {
        $accountingsystem = $this->accountingsystemRepository->findOrFail($id);

        $accountingsystem->fill($request->input());

        $this->accountingsystemRepository->createOrUpdate($accountingsystem);

        event(new UpdatedContentEvent(ACCOUNTINGSYSTEM_MODULE_SCREEN_NAME, $request, $accountingsystem));

        return $response
            ->setPreviousUrl(route('accountingsystem.index'))
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
            $accountingsystem = $this->accountingsystemRepository->findOrFail($id);

            $this->accountingsystemRepository->delete($accountingsystem);

            event(new DeletedContentEvent(ACCOUNTINGSYSTEM_MODULE_SCREEN_NAME, $request, $accountingsystem));

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
            $accountingsystem = $this->accountingsystemRepository->findOrFail($id);
            $this->accountingsystemRepository->delete($accountingsystem);
            event(new DeletedContentEvent(ACCOUNTINGSYSTEM_MODULE_SCREEN_NAME, $request, $accountingsystem));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
