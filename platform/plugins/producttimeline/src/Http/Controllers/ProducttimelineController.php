<?php

namespace Botble\Producttimeline\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Producttimeline\Http\Requests\ProducttimelineRequest;
use Botble\Producttimeline\Repositories\Interfaces\ProducttimelineInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Producttimeline\Tables\ProducttimelineTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Producttimeline\Forms\ProducttimelineForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\DB;

class ProducttimelineController extends BaseController
{
    /**
     * @var ProducttimelineInterface
     */
    protected $producttimelineRepository;

    /**
     * @param ProducttimelineInterface $producttimelineRepository
     */
    public function __construct(ProducttimelineInterface $producttimelineRepository)
    {
        $this->producttimelineRepository = $producttimelineRepository;
    }

    /**
     * @param ProducttimelineTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(ProducttimelineTable $table)
    {
        page_title()->setTitle(trans('plugins/producttimeline::producttimeline.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/producttimeline::producttimeline.create'));

        return $formBuilder->create(ProducttimelineForm::class)->renderForm();
    }

    /**
     * @param ProducttimelineRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(ProducttimelineRequest $request, BaseHttpResponse $response)
    {
        $producttimeline = $this->producttimelineRepository->createOrUpdate($request->only(
            'name',
            'status',
            'date',
            'schedule_date',
        ));
        $data = [];
        $data['product_timeline_id'] = $producttimeline->id;
        $data['product_link'] = $request->product_link;
        $data['product_desc'] = $request->product_desc;
        $data['product_image'] = $request->product_image;
        DB::table('producttimelinesdetail')->insert($data);

        event(new CreatedContentEvent(PRODUCTTIMELINE_MODULE_SCREEN_NAME, $request, $producttimeline));

        return $response
            ->setPreviousUrl(route('producttimeline.index'))
            ->setNextUrl(route('producttimeline.edit', $producttimeline->id))
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
        $producttimeline = $this->producttimelineRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $producttimeline));

        page_title()->setTitle(trans('plugins/producttimeline::producttimeline.edit') . ' "' . $producttimeline->name . '"');

        return $formBuilder->create(ProducttimelineForm::class, ['model' => $producttimeline])->renderForm();
    }

    /**
     * @param int $id
     * @param ProducttimelineRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, ProducttimelineRequest $request, BaseHttpResponse $response)
    {
        $producttimeline = $this->producttimelineRepository->findOrFail($id);

        $producttimeline->fill($request->input());

        $this->producttimelineRepository->createOrUpdate($producttimeline);

        event(new UpdatedContentEvent(PRODUCTTIMELINE_MODULE_SCREEN_NAME, $request, $producttimeline));

        return $response
            ->setPreviousUrl(route('producttimeline.index'))
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
            $producttimeline = $this->producttimelineRepository->findOrFail($id);

            $this->producttimelineRepository->delete($producttimeline);

            event(new DeletedContentEvent(PRODUCTTIMELINE_MODULE_SCREEN_NAME, $request, $producttimeline));

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
            $producttimeline = $this->producttimelineRepository->findOrFail($id);
            $this->producttimelineRepository->delete($producttimeline);
            event(new DeletedContentEvent(PRODUCTTIMELINE_MODULE_SCREEN_NAME, $request, $producttimeline));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
