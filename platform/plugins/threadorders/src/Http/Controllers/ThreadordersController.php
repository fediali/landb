<?php

namespace Botble\Threadorders\Http\Controllers;

use Botble\ACL\Models\UserOtherEmail;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Thread\Models\Thread;
use Botble\Thread\Repositories\Interfaces\ThreadInterface;
use Botble\Threadorders\Http\Requests\ThreadordersRequest;
use Botble\Threadorders\Models\Threadorders;
use Botble\Threadorders\Repositories\Interfaces\ThreadordersInterface;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Botble\Threadorders\Tables\ThreadordersTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Threadorders\Forms\ThreadordersForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ThreadordersController extends BaseController
{
    /**
     * @var ThreadordersInterface
     */
    protected $threadordersRepository;

    /**
     * @var ThreadInterface
     */
    protected $threadRepository;

    /**
     * @param ThreadordersInterface $threadordersRepository
     * @param ThreadInterface $threadRepository
     */
    public function __construct(ThreadordersInterface $threadordersRepository, ThreadInterface $threadRepository)
    {
        $this->threadordersRepository = $threadordersRepository;
        $this->threadRepository = $threadRepository;
    }

    /**
     * @param ThreadordersTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(ThreadordersTable $table)
    {
        page_title()->setTitle(trans('plugins/threadorders::threadorders.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/threadorders::threadorders.create'));

        return $formBuilder->create(ThreadordersForm::class)->renderForm();
    }

    /**
     * @param ThreadordersRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(ThreadordersRequest $request, BaseHttpResponse $response)
    {
        $threadorders = $this->threadordersRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(THREADORDERS_MODULE_SCREEN_NAME, $request, $threadorders));

        return $response
            ->setPreviousUrl(route('threadorders.index'))
            ->setNextUrl(route('threadorders.edit', $threadorders->id))
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
        $threadorders = $this->threadordersRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $threadorders));

        page_title()->setTitle(trans('plugins/threadorders::threadorders.edit') . ' "' . $threadorders->name . '"');

        return $formBuilder->create(ThreadordersForm::class, ['model' => $threadorders])->renderForm();
    }

    /**
     * @param int $id
     * @param ThreadordersRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, ThreadordersRequest $request, BaseHttpResponse $response)
    {
        $threadorders = $this->threadordersRepository->findOrFail($id);

        $threadorders->fill($request->input());

        $this->threadordersRepository->createOrUpdate($threadorders);

        event(new UpdatedContentEvent(THREADORDERS_MODULE_SCREEN_NAME, $request, $threadorders));

        return $response
            ->setPreviousUrl(route('threadorders.index'))
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
            $threadorders = $this->threadordersRepository->findOrFail($id);

            $this->threadordersRepository->delete($threadorders);

            event(new DeletedContentEvent(THREADORDERS_MODULE_SCREEN_NAME, $request, $threadorders));

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
            $threadorders = $this->threadordersRepository->findOrFail($id);
            $this->threadordersRepository->delete($threadorders);
            event(new DeletedContentEvent(THREADORDERS_MODULE_SCREEN_NAME, $request, $threadorders));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function createThreadOrder($id, FormBuilder $formBuilder, Request $request)
    {
        $thread = $this->threadRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $thread));

        page_title()->setTitle(trans('plugins/threadorders::threadorders.create'));

        return $formBuilder->create(ThreadordersForm::class, ['model' => $thread])->renderForm();
    }

    /**
     * @param int $id
     * @param ThreadordersRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function storeThreadOrder($id, ThreadordersRequest $request, BaseHttpResponse $response)
    {
        $requestData = $request->input();

        $thread = $this->threadRepository->findOrFail($id);
        $thread_order_status = $thread->thread_has_order ? Thread::REORDER: Thread::NEW;

        unset($thread->id);
        unset($thread->created_at);
        unset($thread->updated_at);
        unset($thread->deleted_at);

        $getTodayThreadOrderCnt = Threadorders::whereDate('created_at', date('Y-m-d'))->count();
        if ($getTodayThreadOrderCnt) {
            $po_gen = str_replace('-','',date('d-m-Y')).($getTodayThreadOrderCnt+1);
        } else {
            $po_gen = str_replace('-','',date('d-m-Y')).'1';
        }

        $threadData = $thread->toArray();
        $threadData['order_no'] = $po_gen;
        $threadData['thread_id'] = $requestData['thread_id'];
        $threadData['name'] = $requestData['name'];
        $threadData['pp_sample'] = $requestData['pp_sample'];
        $threadData['material'] = $requestData['material'];
        $threadData['shipping_method'] = $requestData['shipping_method'];
        $threadData['order_date'] = $requestData['order_date'];
        $threadData['ship_date'] = $requestData['ship_date'];
        $threadData['cancel_date'] = $requestData['cancel_date'];

        $threadData['status'] = BaseStatusEnum::PENDING;
        $threadData['order_status'] = $thread_order_status;
        $threadData['created_by'] = auth()->user()->id;
        $threadData['updated_by'] = auth()->user()->id;

        $threadorders = $this->threadordersRepository->createOrUpdate($threadData);

        $thread2 = $this->threadRepository->findOrFail($id);

        foreach ($thread2->thread_variations as $thread_variation) {
            $threadOrderVar = [
                'thread_order_id' => $threadorders->id,
                'category_type' => 'regular',
                'product_category_id' => $thread2->regular_product_categories[0]->pivot->product_category_id,
                'thread_variation_id' => $thread_variation->id,
                'print_design_id' => $thread_variation->print_id,
                'name' => $thread_variation->name,
                'sku' => $thread2->regular_product_categories[0]->pivot->sku,
                'quantity' => $requestData['regular_qty'][$thread_variation->id],
                'cost' => $requestData['cost'][$thread_variation->id],
                'notes' => $thread_variation->notes,
            ];
            DB::table('thread_order_variations')->insert($threadOrderVar);
            if (isset($thread2->plus_product_categories[0])) {
                $threadOrderVar = [
                    'thread_order_id' => $threadorders->id,
                    'category_type' => 'plus',
                    'product_category_id' => $thread2->plus_product_categories[0]->pivot->product_category_id,
                    'thread_variation_id' => $thread_variation->id,
                    'print_design_id' => $thread_variation->print_id,
                    'name' => $thread_variation->name,
                    'sku' => $thread2->plus_product_categories[0]->pivot->sku,
                    'quantity' => $requestData['plus_qty'][$thread_variation->id],
                    'cost' => $requestData['cost'][$thread_variation->id],
                    'notes' => $thread_variation->notes,
                ];
                DB::table('thread_order_variations')->insert($threadOrderVar);
            }
        }

        $emails_send_to = UserOtherEmail::where('user_id', $threadData['vendor_id'])->pluck('email')->all();
        $emails_send_to[] = $thread2->vendor->email;

        Mail::send('emails.thread_order_created', $threadData, function($message) use($emails_send_to) {
            $message->to($emails_send_to)->subject('[L&B New Thread Order]');
        });

        event(new CreatedContentEvent(THREADORDERS_MODULE_SCREEN_NAME, $request, $threadorders));

        return $response
            ->setPreviousUrl(route('threadorders.index'))
            ->setNextUrl(route('threadorders.edit', $threadorders->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     */
    public function changeStatus(Request $request, BaseHttpResponse $response)
    {
        $threadorders = $this->threadordersRepository->findOrFail($request->input('pk'));

        $requestData['status'] = $request->input('value');
        $requestData['updated_by'] = auth()->user()->id;

        $threadorders->fill($requestData);

        $this->threadordersRepository->createOrUpdate($threadorders);

        event(new UpdatedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $threadorders));

        return $response;
    }
}
