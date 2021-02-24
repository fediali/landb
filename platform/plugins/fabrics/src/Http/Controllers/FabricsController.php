<?php

namespace Botble\Fabrics\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Fabrics\Http\Requests\FabricsRequest;
use Botble\Fabrics\Repositories\Interfaces\FabricsInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Fabrics\Tables\FabricsTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Fabrics\Forms\FabricsForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\Auth;

class FabricsController extends BaseController
{
    /**
     * @var FabricsInterface
     */
    protected $fabricsRepository;

    /**
     * @param FabricsInterface $fabricsRepository
     */
    public function __construct(FabricsInterface $fabricsRepository)
    {
        $this->fabricsRepository = $fabricsRepository;
    }

    /**
     * @param FabricsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(FabricsTable $table)
    {
        page_title()->setTitle(trans('plugins/fabrics::fabrics.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/fabrics::fabrics.create'));

        return $formBuilder->create(FabricsForm::class)->renderForm();
    }

    /**
     * @param FabricsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(FabricsRequest $request, BaseHttpResponse $response)
    {
        $data = $request->all();
        $data['created_by'] = Auth::user()->id;
        $fabrics = $this->fabricsRepository->createOrUpdate($data);

        event(new CreatedContentEvent(FABRICS_MODULE_SCREEN_NAME, $request, $fabrics));

        return $response
            ->setPreviousUrl(route('fabrics.index'))
            ->setNextUrl(route('fabrics.edit', $fabrics->id))
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
        $fabrics = $this->fabricsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $fabrics));

        page_title()->setTitle(trans('plugins/fabrics::fabrics.edit') . ' "' . $fabrics->name . '"');

        return $formBuilder->create(FabricsForm::class, ['model' => $fabrics])->renderForm();
    }

    /**
     * @param int $id
     * @param FabricsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, FabricsRequest $request, BaseHttpResponse $response)
    {
        $data = $request->all();
        $data['updated_by'] = Auth::user()->id;
        $fabrics = $this->fabricsRepository->findOrFail($id);

        $fabrics->fill($data);

        $this->fabricsRepository->createOrUpdate($fabrics);

        event(new UpdatedContentEvent(FABRICS_MODULE_SCREEN_NAME, $request, $fabrics));

        return $response
            ->setPreviousUrl(route('fabrics.index'))
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
            $fabrics = $this->fabricsRepository->findOrFail($id);

            $this->fabricsRepository->delete($fabrics);

            event(new DeletedContentEvent(FABRICS_MODULE_SCREEN_NAME, $request, $fabrics));

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
            $fabrics = $this->fabricsRepository->findOrFail($id);
            $this->fabricsRepository->delete($fabrics);
            event(new DeletedContentEvent(FABRICS_MODULE_SCREEN_NAME, $request, $fabrics));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
