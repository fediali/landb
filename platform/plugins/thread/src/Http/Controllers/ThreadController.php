<?php

namespace Botble\Thread\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use App\Models\ThreadComment;
use App\Models\ThreadVariation;
use App\Models\VariationFabric;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Thread\Forms\ThreadDetailsForm;
use Botble\Thread\Http\Requests\ThreadRequest;
use Botble\Thread\Models\Thread;
use Botble\Thread\Models\ThreadSpecFile;
use Botble\Thread\Repositories\Interfaces\ThreadInterface;
use Botble\Base\Http\Controllers\BaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Botble\Thread\Tables\ThreadTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Thread\Forms\ThreadForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

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
        $requestData['order_status'] = Thread::NEW;
        $requestData['created_by'] = auth()->user()->id;
        $requestData['updated_by'] = auth()->user()->id;

        $thread = $this->threadRepository->createOrUpdate($requestData);

        $reg = ProductCategory::where('id', $requestData['regular_category_id'])->value('name');
        $plu = ProductCategory::where('id', $requestData['plus_category_id'])->value('name');

        $reg_sku = strtoupper(substr($thread->designer->first_name, 0, 2) . substr($reg, 0, 2) . Str::random(4));
        $plu_sku = strtoupper(substr($thread->designer->first_name, 0, 2) . substr($plu, 0, 2) . Str::random(4));

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

        if ($request->hasfile('spec_files')) {
            foreach ($request->file('spec_files') as $spec_file) {
                $spec_file_name = time() . rand(1, 100) . '.' . $spec_file->extension();
                $spec_file->move(public_path('storage/spec_files'), $spec_file_name);
                ThreadSpecFile::create(['thread_id' => $thread->id, 'spec_file' => 'storage/spec_files/' . $spec_file_name]);
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

        $reg_sku = strtoupper(substr($thread->designer->first_name, 0, 2) . substr($reg, 0, 2) . Str::random(4));
        $plu_sku = strtoupper(substr($thread->designer->first_name, 0, 2) . substr($plu, 0, 2) . Str::random(4));

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

        if ($request->hasfile('spec_files')) {
            foreach ($request->file('spec_files') as $spec_file) {
                $spec_file_name = time() . rand(1, 100) . '.' . $spec_file->extension();
                $spec_file->move(public_path('storage/spec_files'), $spec_file_name);
                ThreadSpecFile::create(['thread_id' => $thread->id, 'spec_file' => 'storage/spec_files/' . $spec_file_name]);
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

        $reg_sku = strtoupper(substr($requestData->designer->first_name, 0, 2) . substr($reg, 0, 2) . Str::random(4));
        $plu_sku = strtoupper(substr($requestData->designer->first_name, 0, 2) . substr($plu, 0, 2) . Str::random(4));

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

    public function show($id, Request $request, FormBuilder $formBuilder)
    {
        $thread = Thread::with(['designer', 'season', 'vendor', 'fabric'])->find($id);

        event(new BeforeEditContentEvent($request, $thread));

        page_title()->setTitle('Thread Details' . ' "' . $thread->name . '"');

        return $formBuilder->create(ThreadDetailsForm::class, ['model' => $thread])->renderForm();

    }

    public function addVariation(Request $request)
    {
        $data = $request->all();
        //dd($data);
        $thread = Thread::with(['designer', 'season', 'vendor', 'fabric'])->find($data['thread_id']);
        for ($i = 0; $i <= count($data['name']) - 1; $i++) {
            $variation = new ThreadVariation();
            $input = array();
            $input['thread_id'] = @$data['thread_id'];
            $input['is_denim'] = @$data['is_denim'];
            $input['name'] = @$data['name'][$i];
            $input['print_id'] = @$data['print_id'][$i];
            $input['wash_id'] = @$data['wash_id'][$i];
            $input['regular_qty'] = @$data['regular_qty'][$i];
            $input['plus_qty'] = @$data['plus_qty'][$i];
            $input['cost'] = @$data['cost'][$i];
            $input['notes'] = @$data['notes'][$i];
            $input['status'] = 'active';
            $skus = generate_sku_by_thread_variation($thread, $input['print_id']);
            $input['sku'] = $skus['reg'];
            $input['plus_sku'] = $skus['plus'];
            $input['created_by'] = Auth::user()->id;
            $create = $variation->create($input);
            if (!$create) {
                return response()->json(['status' => 'error'], 500);
            }
        }
        return response()->json(['status' => 'success'], 200);


    }

    public function updateVariationStatus($id, $status)
    {
        $update = ThreadVariation::find($id)->update(['status' => $status]);
        if ($update) {
            return redirect()->back()->with('success', 'Status updated');
        } else {
            return redirect()->back()->with('error', 'Server error');
        }
    }

    public function postComment(Request $request)
    {
        $data = $request->all();
        $file = $request->file('image');
        if ($file) {
            $type = strtolower($file->getClientOriginalExtension());
            $image = str_replace(' ', '_', $data['comment'] . '_' . substr(microtime(), 2, 7)) . '.' . $type;
            $imageFile = Image::make($request->file('image'))->stream();
            $move = Storage::disk('public')->put('images/comments/' . $image, $imageFile);
            if ($move) {
                $data['image'] = 'storage/images/comments/' . $image;
            }
        }

        $input = ThreadComment::create($data);

        if ($input) {
            $time = $input->created_at->diffForHumans();
            $input->time = $time;
            return response()->json(['comment' => $input], 200);
        } else {
            return response()->json(500);
        }
    }

    public function addVariationPrints(Request $request)
    {
        $data = $request->all();

        $data['created_by'] = Auth::user()->id;

        $input = VariationFabric::create($data);

        if ($input) {
            return response()->json(['status' => 'success'], 200);
        } else {
            return response()->json(['status' => 'error'], 500);
        }
    }

    public function removeFabric($id)
    {

        $remove = VariationFabric::find($id)->delete();
        if ($remove) {
            return redirect()->back()->with('success', 'Fabric deleted');
        } else {
            return redirect()->back()->with('error', 'Server error');
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     */
    public function changeStatus(Request $request, BaseHttpResponse $response)
    {
        $thread = $this->threadRepository->findOrFail($request->input('pk'));
        $requestData['status'] = $request->input('value');
        $requestData['updated_by'] = auth()->user()->id;

        $thread->fill($requestData);

        $this->threadRepository->createOrUpdate($thread);

        event(new UpdatedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $thread));

        return $response;
    }

    public function removeVariation($id)
    {
        $variation = ThreadVariation::find($id);
        $variation->fabrics()->delete();
        $remove = $variation->delete();
        if ($remove) {
            return redirect()->back()->with('success', 'Variation deleted');
        } else {
            return redirect()->back()->with('error', 'Server error');
        }
    }

    public function removeThreadSpecFile($id)
    {
        $remove = ThreadSpecFile::find($id)->delete();
        if ($remove) {
            return redirect()->back()->with('success', 'Thread Spec File deleted');
        } else {
            return redirect()->back()->with('error', 'Server error');
        }
    }

}
