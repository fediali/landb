<?php

namespace Botble\Sourcing\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Sourcing\Http\Requests\SourcingRequest;
use Botble\Sourcing\Repositories\Interfaces\SourcingInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Sourcing\Tables\SourcingTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Sourcing\Forms\SourcingForm;
use Botble\Base\Forms\FormBuilder;

class SourcingController extends BaseController
{
    /**
     * @var SourcingInterface
     */
    protected $sourcingRepository;

    /**
     * @param SourcingInterface $sourcingRepository
     */
    public function __construct(SourcingInterface $sourcingRepository)
    {
        $this->sourcingRepository = $sourcingRepository;
    }

    /**
     * @param SourcingTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(SourcingTable $table)
    {
        page_title()->setTitle(trans('plugins/sourcing::sourcing.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/sourcing::sourcing.create'));

        return $formBuilder->create(SourcingForm::class)->renderForm();
    }

    /**
     * @param SourcingRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(SourcingRequest $request, BaseHttpResponse $response)
    {
        $data = $request->input();
        $data['file'] = json_encode($data['file']);

        $sourcing = $this->sourcingRepository->createOrUpdate($data);

        event(new CreatedContentEvent(SOURCING_MODULE_SCREEN_NAME, $request, $sourcing));

        return $response
            ->setPreviousUrl(route('sourcing.index'))
            ->setNextUrl(route('sourcing.edit', $sourcing->id))
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
        $sourcing = $this->sourcingRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $sourcing));

        page_title()->setTitle(trans('plugins/sourcing::sourcing.edit') . ' "' . $sourcing->name . '"');

        return $formBuilder->create(SourcingForm::class, ['model' => $sourcing])->renderForm();
    }

    /**
     * @param int $id
     * @param SourcingRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, SourcingRequest $request, BaseHttpResponse $response)
    {
        $sourcing = $this->sourcingRepository->findOrFail($id);

        $sourcing->fill($request->input());

        $this->sourcingRepository->createOrUpdate($sourcing);

        event(new UpdatedContentEvent(SOURCING_MODULE_SCREEN_NAME, $request, $sourcing));

        return $response
            ->setPreviousUrl(route('sourcing.index'))
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
            $sourcing = $this->sourcingRepository->findOrFail($id);

            $this->sourcingRepository->delete($sourcing);

            event(new DeletedContentEvent(SOURCING_MODULE_SCREEN_NAME, $request, $sourcing));

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
            $sourcing = $this->sourcingRepository->findOrFail($id);
            $this->sourcingRepository->delete($sourcing);
            event(new DeletedContentEvent(SOURCING_MODULE_SCREEN_NAME, $request, $sourcing));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }


    public function show($id){
      $sourcing = $this->sourcingRepository->findOrFail($id);
      return view('plugins/sourcing::details', compact('sourcing'));
    }
}
