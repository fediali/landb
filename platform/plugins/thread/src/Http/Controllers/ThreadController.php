<?php

namespace Botble\Thread\Http\Controllers;

use App\Events\NotifyManager;
use App\Models\ThreadVariationPPSample;
use App\Models\ThreadVariationTrim;
use App\Models\User;
use Botble\Base\Enums\BaseStatusEnum;
use App\Models\ThreadComment;
use App\Models\ThreadVariation;
use App\Models\VariationFabric;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\UserSearch;
use Botble\Ecommerce\Models\UserSearchItem;
use Botble\Printdesigns\Models\Printdesigns;
use Botble\Thread\Forms\ThreadDetailsForm;
use Botble\Thread\Http\Requests\ThreadRequest;
use Botble\Thread\Models\Thread;
use Botble\Thread\Models\ThreadPvtCatSizesQty;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
        $requestData['status'] = BaseStatusEnum::PENDING;
        $requestData['order_status'] = Thread::NEW;
        $requestData['created_by'] = auth()->user()->id;
        $requestData['updated_by'] = auth()->user()->id;

        if (isset($requestData['is_pieces'])) {
            $requestData['is_pieces'] = 1;
        } else {
            $requestData['is_pieces'] = 0;
        }

        $thread = $this->threadRepository->createOrUpdate($requestData);

        $designerName = strlen($thread->designer->name_initials) > 0 ? $thread->designer->name_initials : substr($thread->designer->first_name, 0, 3);
        $reg_sku = isset($requestData['reg_sku']) ? $requestData['reg_sku'] : generate_thread_sku($requestData['regular_category_id'], $requestData['designer_id'], $designerName);

        if (isset($requestData['plus_category_id']) && $requestData['plus_category_id'] > 0) {
            // $plu_sku = isset($requestData['plus_sku']) ? $requestData['plus_sku'] : generate_thread_sku($requestData['plus_category_id'], $requestData['designer_id'], $designerName, true);
            $plu_sku = $reg_sku . '-X';
        }

        if (isset($requestData['regular_category_id']) && $requestData['regular_category_id'] > 0) {
            if (isset($requestData['plus_category_id']) && $requestData['plus_category_id'] > 0) {
                $thread->regular_product_categories()->sync([
                    $requestData['regular_category_id'] => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku, 'product_unit_id' => $requestData['regular_product_unit_id'], 'per_piece_qty' => $requestData['regular_per_piece_qty']],
                    $requestData['plus_category_id']    => ['category_type' => Thread::PLUS, 'sku' => $plu_sku, 'product_unit_id' => $requestData['plus_product_unit_id'], 'per_piece_qty' => $requestData['plus_per_piece_qty']]
                ]);
            } else {
                $thread->regular_product_categories()->sync([$requestData['regular_category_id'] => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku, 'product_unit_id' => $requestData['regular_product_unit_id'], 'per_piece_qty' => $requestData['regular_per_piece_qty']]]);
            }
        }

        if ($request->hasfile('spec_files')) {
            foreach ($request->file('spec_files') as $spec_file) {
                $spec_file_name = time() . rand(1, 100) . '.' . $spec_file->extension();
                $spec_file->move(public_path('storage/spec_files'), $spec_file_name);
                ThreadSpecFile::create(['thread_id' => $thread->id, 'spec_file' => 'storage/spec_files/' . $spec_file_name]);
            }
        }

        /*if(!empty($thread->vendor_id)){
          $designer = User::find($thread->designer_id);
          $vendor = User::find($thread->vendor_id);
          broadcast(new NotifyManager($vendor, $designer, $thread));
        }*/

        $notification = generate_notification('thread_created', $thread);
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

        if (isset($requestData['is_pieces'])) {
            $requestData['is_pieces'] = 1;
        } else {
            $requestData['is_pieces'] = 0;
        }

        $thread->fill($requestData);

        $this->threadRepository->createOrUpdate($thread);

        $reg_category = $thread->regular_product_categories()->value('product_category_id');
        $plu_category = $thread->plus_product_categories()->value('product_category_id');

        if (isset($requestData['regular_category_id']) && $reg_category && $reg_category != $requestData['regular_category_id']) {
            if ($reg_category) {
                $regCnt = DB::table('category_designer_count')->where(['user_id' => $thread->designer_id, 'product_category_id' => $reg_category])->value('count') - 1;
                $cnt = $regCnt > 0 ? $regCnt : 0;
                DB::table('category_designer_count')->updateOrInsert(['user_id' => $thread->designer_id, 'product_category_id' => $reg_category], ['user_id' => $thread->designer_id, 'product_category_id' => $reg_category, 'count' => $cnt]);
            }
            /*if ($plu_category) {
                $regCnt = DB::table('category_designer_count')->where(['user_id' => $thread->designer_id, 'product_category_id' => $plu_category])->value('count') - 1;
                DB::table('category_designer_count')->updateOrInsert(['user_id' => $thread->designer_id, 'product_category_id' => $plu_category], ['user_id' => $thread->designer_id, 'product_category_id' => $plu_category, 'count' => $regCnt]);
            }*/

            $designerName = strlen($thread->designer->name_initials) > 0 ? $thread->designer->name_initials : $thread->designer->first_name;
            $reg_sku = isset($requestData['reg_sku']) ? $requestData['reg_sku'] : generate_thread_sku($requestData['regular_category_id'], $requestData['designer_id'], $designerName);

            if (isset($requestData['plus_category_id']) && $requestData['plus_category_id'] > 0) {
                // $plu_sku = isset($requestData['plus_sku']) ? $requestData['plus_sku'] : generate_thread_sku($requestData['plus_category_id'], $requestData['designer_id'], $designerName, true);
                $plu_sku = $reg_sku . '-X';
            }

            if (isset($requestData['regular_category_id']) && $requestData['regular_category_id'] > 0) {
                if (isset($requestData['plus_category_id']) && $requestData['plus_category_id'] > 0) {
                    $thread->regular_product_categories()->sync([
                        $requestData['regular_category_id'] => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku, 'product_unit_id' => $requestData['regular_product_unit_id'], 'per_piece_qty' => $requestData['regular_per_piece_qty']],
                        $requestData['plus_category_id']    => ['category_type' => Thread::PLUS, 'sku' => $plu_sku, 'product_unit_id' => $requestData['plus_product_unit_id'], 'per_piece_qty' => $requestData['plus_per_piece_qty']]
                    ]);
                } else {
                    $thread->regular_product_categories()->sync([$requestData['regular_category_id'] => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku, 'product_unit_id' => $requestData['regular_product_unit_id'], 'per_piece_qty' => $requestData['regular_per_piece_qty']]]);
                }
            }
        } elseif (isset($requestData['reg_sku'])) {
            $thread->regular_product_categories()->sync([$requestData['regular_category_id'] => ['category_type' => Thread::REGULAR, 'sku' => $requestData['reg_sku'], 'product_unit_id' => $requestData['regular_product_unit_id'], 'per_piece_qty' => $requestData['regular_per_piece_qty']]]);

        } elseif (isset($requestData['plus_category_id']) && $requestData['plus_category_id'] > 0 && $requestData['plus_category_id'] != $plu_category) {
            $reg_sku = $thread->regular_product_categories()->value('sku');
            $plu_sku = $reg_sku . '-X';
            $thread->regular_product_categories()->attach([
                $requestData['plus_category_id'] => ['category_type' => Thread::PLUS, 'sku' => $plu_sku, 'product_unit_id' => @$requestData['plus_product_unit_id'], 'per_piece_qty' => @$requestData['plus_per_piece_qty']]
            ]);
        }

        if ($request->hasfile('spec_files')) {
            foreach ($request->file('spec_files') as $spec_file) {
                $spec_file_name = time() . rand(1, 100) . '.' . $spec_file->extension();
                $spec_file->move(public_path('storage/spec_files'), $spec_file_name);
                ThreadSpecFile::create(['thread_id' => $thread->id, 'spec_file' => 'storage/spec_files/' . $spec_file_name]);
            }
        }

        generate_notification('thread_updated', $thread);
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

            $reg_category = $thread->regular_product_categories()->value('product_category_id');
            if ($reg_category) {
                $regCnt = DB::table('category_designer_count')->where(['user_id' => $thread->designer_id, 'product_category_id' => $reg_category])->value('count') - 1;
                $cnt = $regCnt > 0 ? $regCnt : 0;
                DB::table('category_designer_count')->updateOrInsert(['user_id' => $thread->designer_id, 'product_category_id' => $reg_category], ['user_id' => $thread->designer_id, 'product_category_id' => $reg_category, 'count' => $cnt]);
            }

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

            $reg_category = $thread->regular_product_categories()->value('product_category_id');
            if ($reg_category) {
                $regCnt = DB::table('category_designer_count')->where(['user_id' => $thread->designer_id, 'product_category_id' => $reg_category])->value('count') - 1;
                $cnt = $regCnt > 0 ? $regCnt : 0;
                DB::table('category_designer_count')->updateOrInsert(['user_id' => $thread->designer_id, 'product_category_id' => $reg_category], ['user_id' => $thread->designer_id, 'product_category_id' => $reg_category, 'count' => $cnt]);
            }

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
        $spec_files = $requestData->spec_files;

        $reg_category = @$requestData->regular_product_categories[0];//->value('product_category_id');
        $plu_category = @$requestData->plus_product_categories[0];//->value('product_category_id');

        $designerName = strlen($requestData->designer->name_initials) > 0 ? $requestData->designer->name_initials : $requestData->designer->first_name;
        $reg_sku = generate_thread_sku($reg_category->pivot->product_category_id, $requestData->designer_id, $designerName);

        if ($plu_category && $plu_category->pivot->product_category_id > 0) {
            // $plu_sku = generate_thread_sku($plu_category->pivot->product_category_id, $requestData->designer_id, $designerName, true);
            $plu_sku = $reg_sku . '-X';
        }

        unset($requestData->id);
        unset($requestData->created_at);
        unset($requestData->updated_at);
        unset($requestData->deleted_at);

        $thread = $this->threadRepository->createOrUpdate($requestData->toArray());

        if ($reg_category && $reg_category->pivot->product_category_id > 0) {
            if ($plu_category && $plu_category->pivot->product_category_id > 0) {
                $thread->regular_product_categories()->sync([
                    $reg_category->pivot->product_category_id => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku, 'product_unit_id' => $reg_category->pivot->product_unit_id, 'per_piece_qty' => $reg_category->pivot->per_piece_qty],
                    $plu_category->pivot->product_category_id => ['category_type' => Thread::PLUS, 'sku' => $plu_sku, 'product_unit_id' => $plu_category->pivot->product_unit_id, 'per_piece_qty' => $plu_category->pivot->per_piece_qty]
                ]);
            } else {
                $thread->regular_product_categories()->sync([$reg_category->pivot->product_category_id => ['category_type' => Thread::REGULAR, 'sku' => $reg_sku, 'product_unit_id' => $reg_category->pivot->product_unit_id, 'per_piece_qty' => $reg_category->pivot->per_piece_qty]]);
            }
        }

        if ($spec_files) {
            foreach ($spec_files as $spec_file) {
                ThreadSpecFile::create(['thread_id' => $thread->id, 'spec_file' => $spec_file->spec_file]);
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

        $thread = Thread::with(['designer', 'season', 'vendor', 'fabric', 'spec_files'])->findorFail($id);
        event(new BeforeEditContentEvent($request, $thread));

        page_title()->setTitle('Thread Details' . ' "' . $thread->name . '"');

        return $formBuilder->create(ThreadDetailsForm::class, ['model' => $thread])->renderForm();

    }

    public function addVariation(Request $request)
    {
        $data = $request->all();

        $thread = Thread::with(['designer', 'season', 'vendor', 'fabric'])->find($data['thread_id']);

        $exist = false;
        for ($i = 0; $i <= count($data['name']) - 1; $i++) {
            $checkDupli = ThreadVariation::where(['thread_id' => $data['thread_id'], 'print_id' => $data['print_id']])->first();
            if (!$checkDupli) {
                $variation = new ThreadVariation();
                $input = array();
                $input['thread_id'] = @$data['thread_id'];
                $input['is_denim'] = @$data['is_denim'];
                $input['name'] = @$data['name'][$i];
                $input['print_id'] = @$data['print_id'][$i];
                $input['wash_id'] = @$data['wash_id'][$i];
                $input['fabric_id'] = @$data['fabric_id'][$i];
                $input['cost'] = @$data['cost'][$i];
                $input['notes'] = @$data['notes'][$i];
                $input['status'] = 'active';

                $input['reg_sku'] = isset($data['reg_sku'][$i]) ? @$data['reg_sku'][$i] : null;
                $input['plus_sku'] = isset($data['plus_sku'][$i]) ? @$data['plus_sku'][$i] : null;

                $pdSKU = Printdesigns::where('id', $input['print_id'])->value('sku');
                $selRegCat = $thread->regular_product_categories()->pluck('sku')->first();

                if ($selRegCat) {
                    $input['regular_qty'] = @$data['regular_qty'][$i];
                    $input['sku'] = ($input['reg_sku'] != null) ? $input['reg_sku'] : $selRegCat . '-' . strtoupper($pdSKU);
                }

                $selPluCat = $thread->plus_product_categories()->pluck('sku')->first();
                if ($selPluCat) {
                    $input['plus_qty'] = @$data['plus_qty'][$i];
                    $input['plus_sku'] = ($input['plus_sku'] != null) ? $input['plus_sku'] : str_replace('-X', '', $selPluCat) . '-' . $pdSKU . '-X';
                }

                $input['created_by'] = Auth::user()->id;
                $create = $variation->create($input);
                if (!$create) {
                    return response()->json(['status' => 'error'], 500);
                }
            } else {
                $exist = true;
            }
        }

        if ($exist) {
            return response()->json(['status' => 'warning'], 500);
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function editVariation(Request $request)
    {

        $data = $request->all();
        $id = $data['var_id'];
        $checkDupli = ThreadVariation::where(['thread_id' => $data['thread_id'], 'print_id' => $data['print_id']])->where('id', '!=', $id)->first();
        if (!$checkDupli) {
            $thread = Thread::with(['designer', 'season', 'vendor', 'fabric'])->find($data['thread_id']);
            $variation = ThreadVariation::find($id);
            $input = array();
            $input['thread_id'] = @$data['thread_id'];
            $input['is_denim'] = @$data['is_denim'];
            $input['name'] = @$data['name'];
            $input['print_id'] = @$data['print_id'];
            $input['wash_id'] = @$data['wash_id'];
            $input['fabric_id'] = @$data['fabric_id'];
            $input['cost'] = @$data['cost'];
            $input['notes'] = @$data['notes'];

            $pdSKU = Printdesigns::where('id', $input['print_id'])->value('sku');

            $selRegCat = $thread->regular_product_categories()->pluck('sku')->first();
            if ($selRegCat) {
                $input['regular_qty'] = @$data['regular_qty'];
                $input['sku'] = $data['regular_sku'];
//                    : $selRegCat . strtoupper(substr($pdSKU, 0, 3) /*. rand(10, 999)*/);
            }
            $selPluCat = $thread->plus_product_categories()->pluck('sku')->first();
            if ($selPluCat) {
                $input['plus_qty'] = @$data['plus_qty'];
                $input['plus_sku'] = isset($data['plus_sku']) ? $data['regular_sku'] . '-X' : str_replace('-X', '', $selPluCat) . strtoupper(substr($pdSKU, 0, 3) /*. rand(10, 999)*/) . '-X';
            }

            $input['updated_by'] = auth()->user()->id;
            $update = $variation->update($input);
            if (!$update) {
                return response()->json(['status' => 'error'], 500);
            }
        } else {
            return response()->json(['status' => 'warning'], 500);
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
            $thread = $this->threadRepository->findOrFail($data['thread_id']);
            $notification = generate_notification('thread_discussion', $thread);
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

    public function addVariationTrim(Request $request)
    {
        if ($request->hasfile('file')) {
            $type = strtolower($request['file']->getClientOriginalExtension());
            $image = str_replace(' ', '_', rand(1, 100) . '_' . substr(microtime(), 2, 7)) . '.' . $type;
            $spec_file_name = time() . rand(1, 100) . '.' . $type;
            $move = $request->file('file')->move(public_path('storage/spec_files'), $spec_file_name);
            if ($move) {
                $request['trim_image'] = 'storage/spec_files/' . $spec_file_name;
            }
        }
        $data = $request->only(['thread_variation_id', 'trim_note', 'trim_image']);
        $input = DB::table('thread_variation_trims')->insertGetId($data);
        if ($input) {
            return response()->json(['status' => 'success'], 200);
        } else {
            return response()->json(['status' => 'error'], 500);
        }
    }

    public function removeVariationTrim($id)
    {
        $remove = DB::table('thread_variation_trims')->delete($id);
        if ($remove) {
            return redirect()->back()->with('success', 'Trim deleted');
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
        if (isset($request->ready)) {
            $requestData['ready'] = $request->input('ready');
        } else {
            $requestData['status'] = $request->input('value');
        }
        $requestData['updated_by'] = auth()->user()->id;
        $thread->fill($requestData);

        $notification = generate_notification('thread_status_updated', $thread);
        event(new UpdatedContentEvent(THREAD_MODULE_SCREEN_NAME, $request, $thread));
        $this->threadRepository->createOrUpdate($thread);
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

    public function readNotification(Request $request)
    {
        $notification = DB::table('user_notifications')->where('id', $request->notification_id)->update(['seen' => 1]);
    }

    public function variationPPSample(Request $request)
    {
        $pp_sample = ThreadVariationPPSample::create($request->all());
        if ($pp_sample) {
            return redirect()->back()->with('success', 'PP Sample Updated');
        } else {
            return redirect()->back()->with('error', 'Server error');
        }
    }

    public function addPvtCatSizesQty(Request $request)
    {
        $data = $request->all();

        if (isset($data['thread_id']) && isset($data['product_category_id']) && isset($data['cat_sizes']) && isset($data['cat_sizes_qty'])) {
            ThreadPvtCatSizesQty::where('thread_id', $data['thread_id'])->delete();

            $postData['thread_id'] = $data['thread_id'];
            $postData['product_category_id'] = $data['product_category_id'];

            foreach ($data['cat_sizes'] as $key => $cat_size) {
                $postData['category_size_id'] = $cat_size;
                $postData['qty'] = $data['cat_sizes_qty'][$key] ? $data['cat_sizes_qty'][$key] : 0;
                ThreadPvtCatSizesQty::create($postData);
            }

            return response()->json(['status' => 'success'], 200);

        } else {
            return response()->json(['status' => 'error'], 500);
        }
    }

    public function saveAdvanceSearch($type, Request $request)
    {
        $params = $request->all();
        $searchData = ['user_id' => auth()->user()->id, 'search_type' => $type, 'name' => $params['search_name'], 'status' => 1];
        $search = UserSearch::create($searchData);
        $searchItems = [];
        unset($params['search_name']);
        foreach ($params as $key => $value) {
            if ($value) {
                $searchItems[] = ['user_search_id' => $search->id, 'key' => $key, 'value' => $value];
            }
        }
        if (!empty($searchItems)) {
            UserSearchItem::insert($searchItems);
        }

        if ($search) {
            return response()->json(['status' => 'success'], 200);
        } else {
            return response()->json(['status' => 'error'], 500);
        }
    }
}
