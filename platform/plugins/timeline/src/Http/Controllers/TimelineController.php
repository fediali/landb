<?php

namespace Botble\Timeline\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Timeline\Http\Requests\TimelineRequest;
use Botble\Timeline\Repositories\Interfaces\TimelineInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Timeline\Tables\TimelineTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Timeline\Forms\TimelineForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\DB;

class TimelineController extends BaseController
{
    /**
     * @var TimelineInterface
     */
    protected $timelineRepository;

    /**
     * @param TimelineInterface $timelineRepository
     */
    public function __construct(TimelineInterface $timelineRepository)
    {
        $this->timelineRepository = $timelineRepository;
    }

    /**
     * @param TimelineTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(TimelineTable $table)
    {
        page_title()->setTitle(trans('plugins/timeline::timeline.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/timeline::timeline.create'));

        return view('plugins/timeline::timeline');
//        return $formBuilder->create(TimelineForm::class)->renderForm();
    }

    /**
     * @param TimelineRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(TimelineRequest $request, BaseHttpResponse $response)
    {

        $timeline = $this->timelineRepository->createOrUpdate($request->only(
            'name',
            'status',
            'date',
        ));
        foreach ($request->product_link as $key => $value) {
            $data = [
                'product_timeline_id' => $timeline->id,
                'product_link'        => $request->product_link[$key],
                'product_desc'        => $request->product_desc[$key],
                'product_image'       => $request->product_image[$key]

            ];
            DB::table('timelines_detail')->insert($data);
        }


        event(new CreatedContentEvent(TIMELINE_MODULE_SCREEN_NAME, $request, $timeline));

        return $response
            ->setPreviousUrl(route('timeline.index'))
            ->setNextUrl(route('timeline.edit', $timeline->id))
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
        $timeline = $this->timelineRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $timeline));

        page_title()->setTitle(trans('plugins/timeline::timeline.edit') . ' "' . $timeline->name . '"');
        return view('plugins/timeline::timeline', compact($timeline));
//        return $formBuilder->create(TimelineForm::class, ['model' => $timeline])->renderForm();
    }

    /**
     * @param int $id
     * @param TimelineRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, TimelineRequest $request, BaseHttpResponse $response)
    {
        $timeline = $this->timelineRepository->findOrFail($id);

        $timeline->fill($request->input());

        $this->timelineRepository->createOrUpdate($timeline);

        event(new UpdatedContentEvent(TIMELINE_MODULE_SCREEN_NAME, $request, $timeline));

        return $response
            ->setPreviousUrl(route('timeline.index'))
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
            $timeline = $this->timelineRepository->findOrFail($id);

            $this->timelineRepository->delete($timeline);

            event(new DeletedContentEvent(TIMELINE_MODULE_SCREEN_NAME, $request, $timeline));

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
            $timeline = $this->timelineRepository->findOrFail($id);
            $this->timelineRepository->delete($timeline);
            event(new DeletedContentEvent(TIMELINE_MODULE_SCREEN_NAME, $request, $timeline));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
