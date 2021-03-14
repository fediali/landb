<?php

namespace Botble\Thread\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Thread\Forms\ThreadOrderForm;
use Botble\Thread\Http\Requests\ThreadRequest;
use Botble\Thread\Models\Thread;
use Botble\Thread\Repositories\Interfaces\ThreadInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Thread\Tables\ThreadTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Thread\Forms\ThreadForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Str;

class ThreadController extends BaseController
{
    /**
     * @var ThreadInterface
     */
    protected $threadRepository;

    /**
     * @param ThreadInterface $threadRepository
     */
    public function __construct(ThreadInterface $threadRepository)
    {
        $this->threadRepository = $threadRepository;
    }

    /**
     * @param ThreadTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(ThreadTable $table)
    {
        page_title()->setTitle(trans('plugins/thread::thread.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/thread::thread.create'));

        return $formBuilder->create(ThreadForm::class)->renderForm();
    }

    /**
     * @param ThreadRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(ThreadRequest $request, BaseHttpResponse $response)
    {
        $requestData = $request->input();

        $requestData['order_no'] = strtoupper(Str::random(8));
        $requestData['status'] = BaseStatusEnum::PENDING;
        $requestData['created_by'] = auth()->user()->id;
        $requestData['updated_by'] = auth()->user()->id;

        $thread = $this->threadRepository->createOrUpdate($requestData);

        $reg = ProductCategory::where('id', $requestData['regular_category_id'])->value('name');
        $plu = ProductCategory::where('id', $requestData['plus_category_id'])->value('name');

        $reg_sku = strtoupper(substr($thread->designer->first_name,0,2).substr($reg,0,2).Str::random(4));
        $plu_sku = strtoupper(substr($thread->designer->first_name,0,2).substr($plu,0,2).Str::random(4));

        if (isset($requestData['regular_category_id']) && $requestData['regular_category_id'] > 0) {
            if (isset($requestData['plus_category_id']) && $requestData['plus_category_id'] > 0) {
                $thread->regular_product_categories()->sync([
                    $requestData['regular_category_id'] => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku],
                    $requestData['plus_category_id'] => ['category_type' => Thread::PLUS, 'sku' => $plu_sku]
                ]);
            } else {
                $thread->regular_product_categories()->sync([$requestData['regular_category_id'] => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku]]);
            }
        }

        event(new CreatedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $thread));

        return $response
            ->setPreviousUrl(route('thread.index'))
            ->setNextUrl(route('thread.edit', $thread->id))
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
        $thread = $this->threadRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $thread));

        page_title()->setTitle(trans('plugins/thread::thread.edit') . ' "' . $thread->name . '"');

        return $formBuilder->create(ThreadForm::class, ['model' => $thread])->renderForm();
    }

    /**
     * @param int $id
     * @param ThreadRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, ThreadRequest $request, BaseHttpResponse $response)
    {
        $thread = $this->threadRepository->findOrFail($id);

        $requestData = $request->input();
        $requestData['updated_by'] = auth()->user()->id;

        $thread->fill($requestData);

        $this->threadRepository->createOrUpdate($thread);

        $reg = ProductCategory::where('id', $requestData['regular_category_id'])->value('name');
        $plu = ProductCategory::where('id', $requestData['plus_category_id'])->value('name');

        $reg_sku = strtoupper(substr($thread->designer->first_name,0,2).substr($reg,0,2).Str::random(4));
        $plu_sku = strtoupper(substr($thread->designer->first_name,0,2).substr($plu,0,2).Str::random(4));

        if (isset($requestData['regular_category_id']) && $requestData['regular_category_id'] > 0) {
            if (isset($requestData['plus_category_id']) && $requestData['plus_category_id'] > 0) {
                $thread->regular_product_categories()->sync([
                    $requestData['regular_category_id'] => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku],
                    $requestData['plus_category_id'] => ['category_type' => Thread::PLUS, 'sku' => $plu_sku]
                ]);
            } else {
                $thread->regular_product_categories()->sync([$requestData['regular_category_id'] => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku]]);
            }
        }

        event(new UpdatedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $thread));

        return $response
            ->setPreviousUrl(route('thread.index'))
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
            $thread = $this->threadRepository->findOrFail($id);

            $this->threadRepository->delete($thread);

            event(new DeletedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $thread));

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
            $thread = $this->threadRepository->findOrFail($id);
            $this->threadRepository->delete($thread);
            event(new DeletedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $thread));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function cloneItem($id, BaseHttpResponse $response, Request $request)
    {
        $requestData = $this->threadRepository->findOrFail($id);

        $reg_category = $requestData->regular_product_categories()->value('product_category_id');
        $plu_category = $requestData->plus_product_categories()->value('product_category_id');

        $reg = ProductCategory::where('id', $reg_category)->value('name');
        $plu = ProductCategory::where('id', $plu_category)->value('name');

        $reg_sku = strtoupper(substr($requestData->designer->first_name,0,2).substr($reg,0,2).Str::random(4));
        $plu_sku = strtoupper(substr($requestData->designer->first_name,0,2).substr($plu,0,2).Str::random(4));

        unset($requestData->id);
        unset($requestData->created_at);
        unset($requestData->updated_at);
        unset($requestData->deleted_at);

        $requestData->order_no = strtoupper(Str::random(8));

        $thread = $this->threadRepository->createOrUpdate($requestData->toArray());

        if ($reg_category > 0) {
            if ($plu_category > 0) {
                $thread->regular_product_categories()->sync([
                    $reg_category => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku],
                    $plu_category => ['category_type' => Thread::PLUS, 'sku' => $plu_sku]
                ]);
            } else {
                $thread->regular_product_categories()->sync([$reg_category => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku]]);
            }
        }

        event(new CreatedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $thread));

        return $response
            ->setPreviousUrl(route('thread.index'))
            ->setNextUrl(route('thread.edit', $thread->id))
            ->setMessage(trans('core/base::notices.create_success_message'));

    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function createOrder($id, FormBuilder $formBuilder, Request $request)
    {
        $thread = $this->threadRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $thread));

        page_title()->setTitle('Create Order "' . $thread->name . '"');

        return $formBuilder->create(ThreadOrderForm::class, ['model' => $thread])->renderForm();
    }

}
