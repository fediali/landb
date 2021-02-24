<?php

namespace Botble\Fits\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Fits\Http\Requests\FitsRequest;
use Botble\Fits\Repositories\Interfaces\FitsInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Fits\Tables\FitsTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Fits\Forms\FitsForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\Auth;

class FitsController extends BaseController
{
    /**
     * @var FitsInterface
     */
    protected $fitsRepository;

    /**
     * @param FitsInterface $fitsRepository
     */
    public function __construct(FitsInterface $fitsRepository)
    {
        $this->fitsRepository = $fitsRepository;
    }

    /**
     * @param FitsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(FitsTable $table)
    {
        page_title()->setTitle(trans('plugins/fits::fits.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/fits::fits.create'));

        return $formBuilder->create(FitsForm::class)->renderForm();
    }

    /**
     * @param FitsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(FitsRequest $request, BaseHttpResponse $response)
    {
        $data = $request->all();
        $data['created_by'] = Auth::user()->id;
        $fits = $this->fitsRepository->createOrUpdate($data);

        event(new CreatedContentEvent(FITS_MODULE_SCREEN_NAME, $request, $fits));

        return $response
            ->setPreviousUrl(route('fits.index'))
            ->setNextUrl(route('fits.edit', $fits->id))
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
        $fits = $this->fitsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $fits));

        page_title()->setTitle(trans('plugins/fits::fits.edit') . ' "' . $fits->name . '"');

        return $formBuilder->create(FitsForm::class, ['model' => $fits])->renderForm();
    }

    /**
     * @param int $id
     * @param FitsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, FitsRequest $request, BaseHttpResponse $response)
    {
        $fits = $this->fitsRepository->findOrFail($id);
        $data = $request->all();
        $data['updated_by'] = Auth::user()->id;
        $fits->fill($data);

        $this->fitsRepository->createOrUpdate($fits);

        event(new UpdatedContentEvent(FITS_MODULE_SCREEN_NAME, $request, $fits));

        return $response
            ->setPreviousUrl(route('fits.index'))
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
            $fits = $this->fitsRepository->findOrFail($id);

            $this->fitsRepository->delete($fits);

            event(new DeletedContentEvent(FITS_MODULE_SCREEN_NAME, $request, $fits));

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
            $fits = $this->fitsRepository->findOrFail($id);
            $this->fitsRepository->delete($fits);
            event(new DeletedContentEvent(FITS_MODULE_SCREEN_NAME, $request, $fits));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
