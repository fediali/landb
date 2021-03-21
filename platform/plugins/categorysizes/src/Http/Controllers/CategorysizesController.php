<?php

namespace Botble\Categorysizes\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Categorysizes\Http\Requests\CategorysizesRequest;
use Botble\Categorysizes\Repositories\Interfaces\CategorysizesInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Categorysizes\Tables\CategorysizesTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Categorysizes\Forms\CategorysizesForm;
use Botble\Base\Forms\FormBuilder;

class CategorysizesController extends BaseController
{
    /**
     * @var CategorysizesInterface
     */
    protected $categorysizesRepository;

    /**
     * @param CategorysizesInterface $categorysizesRepository
     */
    public function __construct(CategorysizesInterface $categorysizesRepository)
    {
        $this->categorysizesRepository = $categorysizesRepository;
    }

    /**
     * @param CategorysizesTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(CategorysizesTable $table)
    {
        page_title()->setTitle(trans('plugins/categorysizes::categorysizes.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/categorysizes::categorysizes.create'));

        return $formBuilder->create(CategorysizesForm::class)->renderForm();
    }

    /**
     * @param CategorysizesRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(CategorysizesRequest $request, BaseHttpResponse $response)
    {
        $requestData = $request->input();
        $requestData['created_by'] = auth()->user()->id;
        $requestData['updated_by'] = auth()->user()->id;

        $categorysizes = $this->categorysizesRepository->createOrUpdate($requestData);

        event(new CreatedContentEvent(CATEGORYSIZES_MODULE_SCREEN_NAME, $request, $categorysizes));

        return $response
            ->setPreviousUrl(route('categorysizes.index'))
            ->setNextUrl(route('categorysizes.edit', $categorysizes->id))
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
        $categorysizes = $this->categorysizesRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $categorysizes));

        page_title()->setTitle(trans('plugins/categorysizes::categorysizes.edit') . ' "' . $categorysizes->name . '"');

        return $formBuilder->create(CategorysizesForm::class, ['model' => $categorysizes])->renderForm();
    }

    /**
     * @param int $id
     * @param CategorysizesRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, CategorysizesRequest $request, BaseHttpResponse $response)
    {
        $categorysizes = $this->categorysizesRepository->findOrFail($id);

        $requestData = $request->input();
        $requestData['updated_by'] = auth()->user()->id;

        $categorysizes->fill($requestData);

        $this->categorysizesRepository->createOrUpdate($categorysizes);

        event(new UpdatedContentEvent(CATEGORYSIZES_MODULE_SCREEN_NAME, $request, $categorysizes));

        return $response
            ->setPreviousUrl(route('categorysizes.index'))
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
            $categorysizes = $this->categorysizesRepository->findOrFail($id);

            $this->categorysizesRepository->delete($categorysizes);

            event(new DeletedContentEvent(CATEGORYSIZES_MODULE_SCREEN_NAME, $request, $categorysizes));

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
            $categorysizes = $this->categorysizesRepository->findOrFail($id);
            $this->categorysizesRepository->delete($categorysizes);
            event(new DeletedContentEvent(CATEGORYSIZES_MODULE_SCREEN_NAME, $request, $categorysizes));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
