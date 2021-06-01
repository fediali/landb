<?php

namespace Botble\Threadvariationsamples\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Threadvariationsamples\Http\Requests\ThreadvariationsamplesRequest;
use Botble\Threadvariationsamples\Models\ThreadVariationSampleMedia;
use Botble\Threadvariationsamples\Repositories\Interfaces\ThreadvariationsamplesInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Threadvariationsamples\Tables\ThreadvariationsamplesTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Threadvariationsamples\Forms\ThreadvariationsamplesForm;
use Botble\Base\Forms\FormBuilder;

class ThreadvariationsamplesController extends BaseController
{
    /**
     * @var ThreadvariationsamplesInterface
     */
    protected $threadvariationsamplesRepository;

    /**
     * @param ThreadvariationsamplesInterface $threadvariationsamplesRepository
     */
    public function __construct(ThreadvariationsamplesInterface $threadvariationsamplesRepository)
    {
        $this->threadvariationsamplesRepository = $threadvariationsamplesRepository;
    }

    /**
     * @param ThreadvariationsamplesTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(ThreadvariationsamplesTable $table)
    {
        page_title()->setTitle(trans('plugins/threadvariationsamples::threadvariationsamples.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/threadvariationsamples::threadvariationsamples.create'));

        return $formBuilder->create(ThreadvariationsamplesForm::class)->renderForm();
    }

    /**
     * @param ThreadvariationsamplesRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(ThreadvariationsamplesRequest $request, BaseHttpResponse $response)
    {
        $requestData = $request->input();
        $requestData['created_by'] = auth()->user()->id;
        $requestData['updated_by'] = auth()->user()->id;
        $threadvariationsamples = $this->threadvariationsamplesRepository->createOrUpdate($requestData);

        event(new CreatedContentEvent(THREADVARIATIONSAMPLES_MODULE_SCREEN_NAME, $request, $threadvariationsamples));

        return $response
            ->setPreviousUrl(route('threadvariationsamples.index'))
            ->setNextUrl(route('threadvariationsamples.edit', $threadvariationsamples->id))
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
        $threadvariationsamples = $this->threadvariationsamplesRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $threadvariationsamples));

        page_title()->setTitle(trans('plugins/threadvariationsamples::threadvariationsamples.edit') . ' "' . $threadvariationsamples->name . '"');

        return $formBuilder->create(ThreadvariationsamplesForm::class, ['model' => $threadvariationsamples])->renderForm();
    }

    /**
     * @param int $id
     * @param ThreadvariationsamplesRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, ThreadvariationsamplesRequest $request, BaseHttpResponse $response)
    {
        $threadvariationsamples = $this->threadvariationsamplesRepository->findOrFail($id);

        $threadvariationsamples->fill($request->input());

        $this->threadvariationsamplesRepository->createOrUpdate($threadvariationsamples);

        event(new UpdatedContentEvent(THREADVARIATIONSAMPLES_MODULE_SCREEN_NAME, $request, $threadvariationsamples));

        return $response
            ->setPreviousUrl(route('threadvariationsamples.index'))
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
            $threadvariationsamples = $this->threadvariationsamplesRepository->findOrFail($id);

            $this->threadvariationsamplesRepository->delete($threadvariationsamples);

            event(new DeletedContentEvent(THREADVARIATIONSAMPLES_MODULE_SCREEN_NAME, $request, $threadvariationsamples));

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
            $threadvariationsamples = $this->threadvariationsamplesRepository->findOrFail($id);
            $this->threadvariationsamplesRepository->delete($threadvariationsamples);
            event(new DeletedContentEvent(THREADVARIATIONSAMPLES_MODULE_SCREEN_NAME, $request, $threadvariationsamples));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function sampleMediaList($id, Request $request, BaseHttpResponse $response)
    {
        $threadvariationsample = $this->threadvariationsamplesRepository->findOrFail($id);
        return view('plugins/threadvariationsamples::sampleMediaList', compact('threadvariationsample'));
    }

    public function uploadSampleMedia($id, Request $request, BaseHttpResponse $response)
    {
        if ($request->hasfile('media_file')) {
            $spec_file_name = time() . rand(1, 100) . '.' . $request->file('media_file')->extension();
            $request->file('media_file')->move(public_path('storage/sample_media_files'), $spec_file_name);
            ThreadVariationSampleMedia::create(['thread_variation_sample_id' => $id, 'media' => 'storage/sample_media_files/' . $spec_file_name]);
        }
        return $response->setMessage('Media Uploaded Successfully!');
    }

}
