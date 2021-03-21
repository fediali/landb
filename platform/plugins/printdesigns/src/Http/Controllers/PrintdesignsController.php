<?php

namespace Botble\Printdesigns\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Printdesigns\Http\Requests\PrintdesignsRequest;
use Botble\Printdesigns\Repositories\Interfaces\PrintdesignsInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Printdesigns\Tables\PrintdesignsTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Printdesigns\Forms\PrintdesignsForm;
use Botble\Base\Forms\FormBuilder;

class PrintdesignsController extends BaseController
{
    /**
     * @var PrintdesignsInterface
     */
    protected $printdesignsRepository;

    /**
     * @param PrintdesignsInterface $printdesignsRepository
     */
    public function __construct(PrintdesignsInterface $printdesignsRepository)
    {
        $this->printdesignsRepository = $printdesignsRepository;
    }

    /**
     * @param PrintdesignsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(PrintdesignsTable $table)
    {
        page_title()->setTitle(trans('plugins/printdesigns::printdesigns.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/printdesigns::printdesigns.create'));

        return $formBuilder->create(PrintdesignsForm::class)->renderForm();
    }

    /**
     * @param PrintdesignsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(PrintdesignsRequest $request, BaseHttpResponse $response)
    {
        $printdesigns = $this->printdesignsRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(PRINTDESIGNS_MODULE_SCREEN_NAME, $request, $printdesigns));

        return $response
            ->setPreviousUrl(route('printdesigns.index'))
            ->setNextUrl(route('printdesigns.edit', $printdesigns->id))
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
        $printdesigns = $this->printdesignsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $printdesigns));

        page_title()->setTitle(trans('plugins/printdesigns::printdesigns.edit') . ' "' . $printdesigns->name . '"');

        return $formBuilder->create(PrintdesignsForm::class, ['model' => $printdesigns])->renderForm();
    }

    /**
     * @param int $id
     * @param PrintdesignsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, PrintdesignsRequest $request, BaseHttpResponse $response)
    {
        $printdesigns = $this->printdesignsRepository->findOrFail($id);

        $printdesigns->fill($request->input());

        $this->printdesignsRepository->createOrUpdate($printdesigns);

        event(new UpdatedContentEvent(PRINTDESIGNS_MODULE_SCREEN_NAME, $request, $printdesigns));

        return $response
            ->setPreviousUrl(route('printdesigns.index'))
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
            $printdesigns = $this->printdesignsRepository->findOrFail($id);

            $this->printdesignsRepository->delete($printdesigns);

            event(new DeletedContentEvent(PRINTDESIGNS_MODULE_SCREEN_NAME, $request, $printdesigns));

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
            $printdesigns = $this->printdesignsRepository->findOrFail($id);
            $this->printdesignsRepository->delete($printdesigns);
            event(new DeletedContentEvent(PRINTDESIGNS_MODULE_SCREEN_NAME, $request, $printdesigns));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
