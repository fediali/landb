<?php

namespace Botble\Wash\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Wash\Http\Requests\WashRequest;
use Botble\Wash\Repositories\Interfaces\WashInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Wash\Tables\WashTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Wash\Forms\WashForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\Auth;

class WashController extends BaseController
{
    /**
     * @var WashInterface
     */
    protected $washRepository;

    /**
     * @param WashInterface $washRepository
     */
    public function __construct(WashInterface $washRepository)
    {
        $this->washRepository = $washRepository;
    }

    /**
     * @param WashTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(WashTable $table)
    {
        page_title()->setTitle(trans('plugins/wash::wash.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/wash::wash.create'));

        return $formBuilder->create(WashForm::class)->renderForm();
    }

    /**
     * @param WashRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(WashRequest $request, BaseHttpResponse $response)
    {
        $data = $request->all();
        $data['created_by'] = Auth::user()->id;
        $wash = $this->washRepository->createOrUpdate($data);

        event(new CreatedContentEvent(WASH_MODULE_SCREEN_NAME, $request, $wash));

        return $response
            ->setPreviousUrl(route('wash.index'))
            ->setNextUrl(route('wash.edit', $wash->id))
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
        $wash = $this->washRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $wash));

        page_title()->setTitle(trans('plugins/wash::wash.edit') . ' "' . $wash->name . '"');

        return $formBuilder->create(WashForm::class, ['model' => $wash])->renderForm();
    }

    /**
     * @param int $id
     * @param WashRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, WashRequest $request, BaseHttpResponse $response)
    {
        $wash = $this->washRepository->findOrFail($id);
        $data = $request->all();
        $data['updated_by'] = Auth::user()->id;
        $wash->fill($data);

        $this->washRepository->createOrUpdate($wash);

        event(new UpdatedContentEvent(WASH_MODULE_SCREEN_NAME, $request, $wash));

        return $response
            ->setPreviousUrl(route('wash.index'))
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
            $wash = $this->washRepository->findOrFail($id);

            $this->washRepository->delete($wash);

            event(new DeletedContentEvent(WASH_MODULE_SCREEN_NAME, $request, $wash));

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
            $wash = $this->washRepository->findOrFail($id);
            $this->washRepository->delete($wash);
            event(new DeletedContentEvent(WASH_MODULE_SCREEN_NAME, $request, $wash));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
