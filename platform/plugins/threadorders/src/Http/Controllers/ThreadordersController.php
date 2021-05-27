<?php

namespace Botble\Threadorders\Http\Controllers;

use App\Models\InventoryHistory;
use Botble\ACL\Models\UserOtherEmail;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Categorysizes\Models\Categorysizes;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductAttribute;
use Botble\Ecommerce\Models\ProductAttributeSet;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Slug\Models\Slug;
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
use Illuminate\Support\Str;
use SlugHelper;

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
     * @var ProductVariationInterface
     */
    protected $productVariation;

    protected $productCategoryRepository;

    /**
     * @param ThreadordersInterface $threadordersRepository
     * @param ThreadInterface $threadRepository
     */
    public function __construct(ThreadordersInterface $threadordersRepository, ThreadInterface $threadRepository, ProductVariationInterface $productVariation, ProductCategoryInterface $productCategoryRepository)
    {
        $this->threadordersRepository = $threadordersRepository;
        $this->threadRepository = $threadRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->productVariation = $productVariation;
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
        $thread_order_status = $thread->thread_has_order ? Thread::REORDER : Thread::NEW;

        unset($thread->id);
        unset($thread->created_at);
        unset($thread->updated_at);
        unset($thread->deleted_at);

        $getTodayThreadOrderCnt = Threadorders::whereDate('created_at', date('Y-m-d'))->count();
        if ($getTodayThreadOrderCnt) {
            $po_gen = str_replace('-', '', date('d-m-Y')) . ($getTodayThreadOrderCnt + 1);
        } else {
            $po_gen = str_replace('-', '', date('d-m-Y')) . '1';
        }

        $threadData = $thread->toArray();
        $threadData['order_no'] = $po_gen;
        $threadData['thread_id'] = $requestData['thread_id'];
        $threadData['name'] = $requestData['name'];
        //$threadData['pp_sample'] = $requestData['pp_sample'];
        $threadData['material'] = $requestData['material'];
        $threadData['shipping_method'] = $requestData['shipping_method'];
        $threadData['vendor_product_id'] = $requestData['vendor_product_id'];
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
            // TODO:: check unique barcode
            $barcode = get_barcode();
            $threadOrderVar = [
                'thread_order_id'     => $threadorders->id,
                'category_type'       => 'regular',
                'product_category_id' => $thread2->regular_product_categories[0]->pivot->product_category_id,
                'product_unit_id'     => $requestData['regular_product_unit_id'],
                'per_piece_qty'       => $requestData['regular_per_piece_qty'],
                'thread_variation_id' => $thread_variation->id,
                'print_design_id'     => $thread_variation->print_id,
                'name'                => $thread_variation->name,
                'parent_sku'          => $thread2->regular_product_categories[0]->pivot->sku,
                'sku'                 => $thread_variation->sku,
                'quantity'            => $requestData['regular_qty'][$thread_variation->id],
                'cost'                => $requestData['cost'][$thread_variation->id],
                'notes'               => $thread_variation->notes,
                'upc'                 => $barcode['upc'],
                'barcode'             => $barcode['barcode'],
            ];
            DB::table('thread_order_variations')->insert($threadOrderVar);

            if ($thread_order_status == Thread::REORDER) {
                $prodId = Product::where('sku', $thread_variation->sku)->value('id');
                if ($prodId) {
                    $logParam = [
                        'parent_product_id' => $prodId,
                        'product_id' => $prodId,
                        'sku' => $thread_variation->sku,
                        'thread_order_id' => $threadorders->id,
                        'quantity' => $requestData['regular_qty'][$thread_variation->id],
                        'created_by' => auth()->user()->id,
                        'reference' => InventoryHistory::PROD_REORDER
                    ];
                    log_product_history($logParam);
                }
            }

            if (isset($thread2->plus_product_categories[0])) {
                $barcode = get_barcode();
                $threadOrderVar = [
                    'thread_order_id'     => $threadorders->id,
                    'category_type'       => 'plus',
                    'product_category_id' => $thread2->plus_product_categories[0]->pivot->product_category_id,
                    'product_unit_id'     => $requestData['plus_product_unit_id'],
                    'per_piece_qty'       => $requestData['plus_per_piece_qty'],
                    'thread_variation_id' => $thread_variation->id,
                    'print_design_id'     => $thread_variation->print_id,
                    'name'                => $thread_variation->name,
                    'parent_sku'          => $thread2->plus_product_categories[0]->pivot->sku,
                    'sku'                 => $thread_variation->plus_sku,
                    'quantity'            => $requestData['plus_qty'][$thread_variation->id],
                    'cost'                => $requestData['cost'][$thread_variation->id],
                    'notes'               => $thread_variation->notes,
                    'upc'                 => $barcode['upc'],
                    'barcode'             => $barcode['barcode'],
                ];
                DB::table('thread_order_variations')->insert($threadOrderVar);

                if ($thread_order_status == Thread::REORDER) {
                    $prodId = Product::where('sku', $thread_variation->plus_sku)->value('id');
                    if ($prodId) {
                        $logParam = [
                            'parent_product_id' => $prodId,
                            'product_id' => $prodId,
                            'sku' => $thread_variation->plus_sku,
                            'thread_order_id' => $threadorders->id,
                            'quantity' => $requestData['plus_qty'][$thread_variation->id],
                            'created_by' => auth()->user()->id,
                            'reference' => InventoryHistory::PROD_REORDER
                        ];
                        log_product_history($logParam);
                    }
                }

            }
        }

        $emails_send_to = UserOtherEmail::where('user_id', $threadData['vendor_id'])->pluck('email')->all();
        $emails_send_to[] = $thread2->vendor->email;

        /*Mail::send('emails.thread_order_created', $threadData, function ($message) use ($emails_send_to) {
            $message->to($emails_send_to)->subject('[L&B New Thread Order]');
        });*/

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

    public function quantityCalculate($id)
    {
        $category = $this->productCategoryRepository->findOrFail($id);
        $totalQuantity = 0;
        foreach ($category->category_sizes as $cat) {
            $quan = substr($cat->name, strpos($cat->name, "-") + 1);
            $totalQuantity += $quan;
        }
        return $totalQuantity;
    }

    public function pushToEcommerce($id, BaseHttpResponse $response)
    {
        $threadorder = $this->threadordersRepository->findOrFail($id);
        $success = true;
        $exist = false;
        if ($threadorder) {
            $variations = $threadorder->threadOrderVariations();
            if (!count($variations)) {
                return $response->setPreviousUrl(route('thread.index'))->setError('No variations exists against this order!');
            }
            foreach ($variations as $key => $variation) {
                $check = Product::where('sku', $variation->sku)->first();
                if (!$check) {
                    $packQuantity = $this->quantityCalculate($variation->product_category_id);
                    $product = new Product();
                    $product->name = $variation->name;
                    $product->description = $variation->name;
                    $product->content = $variation->name;
                    $product->status = BaseStatusEnum::PUBLISHED;
                    if ($threadorder->thread_status == Thread::PRIVATE) {
                        $product->status = BaseStatusEnum::DRAFT;
                        $product->private_label = 1;
                    }
                    $product->sku = $variation->sku;
                    $product->category_id = $variation->product_category_id;
                    $product->quantity = 0;

                    $percentage = !is_null(setting('sales_percentage')) ? setting('sales_percentage') : 0;

                    $extras = ($variation->cost * $packQuantity) * $percentage / 100;
                    $packPrice = $variation->cost * $packQuantity + $extras;
                    $single = $variation->cost * $percentage / 100;
                    $singlePrice = $variation->cost + $single;

                    $product->price = $packPrice;
                    // $product->sale_price = $variation->cost + $extras;
                    $product->images = json_encode([$variation->design_file]);
                    $product->tax_id = 1;
                    $product->upc = $variation->upc;
                    $product->barcode = $variation->barcode;
                    if ($product->save()) {
                        $product->categories()->sync([$variation->product_category_id]);
                        $product->productCollections()->detach();
                        $product->productCollections()->attach([1]);//new arrival
                        Slug::create([
                            'reference_type' => Product::class,
                            'reference_id'   => $product->id,
                            'key'            => Str::slug($product->name),
                            'prefix'         => SlugHelper::getPrefix(Product::class),
                        ]);

                        $getTypeAttrSet = ProductAttributeSet::where('slug', 'type')->value('id');
                        if ($getTypeAttrSet) {
                            $getTypeAttrs = ProductAttribute::where('attribute_set_id', $getTypeAttrSet)->pluck('id')->all();
                            if ($getTypeAttrs) {
                                $product->productAttributeSets()->attach([$getTypeAttrSet]);
                                $product->productAttributes()->attach($getTypeAttrs);
                                $getSizeAttrSet = ProductAttributeSet::where('slug', 'size')->value('id');
                                if ($getSizeAttrSet) {
                                    $getCatSizes = Categorysizes::join('product_categories_sizes', 'categorysizes.id', 'product_categories_sizes.category_size_id')
                                        ->where('product_categories_sizes.product_category_id', $variation->product_category_id)
                                        ->pluck('categorysizes.name')
                                        ->all();
                                    $getSizeAttrs = [];
                                    foreach ($getCatSizes as $getCatSize) {
                                        $sizeExist = ProductAttribute::where('slug', strtolower($getCatSize))->where('attribute_set_id', $getSizeAttrSet)->value('id');
                                        if ($sizeExist) {
                                            $getSizeAttrs[] = $sizeExist;
                                        } else {
                                            $sizeAttrData = ['attribute_set_id' => $getSizeAttrSet, 'title' => $getCatSize, 'slug' => strtolower($getCatSize)];
                                            $sizeAttr = ProductAttribute::create($sizeAttrData);
                                            if ($sizeAttr) {
                                                $getSizeAttrs[] = $sizeAttr->id;
                                            }
                                        }
                                    }


                                    $addedAttributes = [];
                                    $getTypePackAttr = ProductAttribute::where('attribute_set_id', $getTypeAttrSet)->where('slug', 'pack')->value('id');
                                    $addedAttributes[$getTypeAttrSet] = $getTypePackAttr;
                                    $getSizeAllAttr = ProductAttribute::where('attribute_set_id', $getSizeAttrSet)->where('slug', 'all')->value('id');
                                    $addedAttributes[$getSizeAttrSet] = $getSizeAllAttr;
                                    $result = $this->productVariation->getVariationByAttributesOrCreate($product->id, $addedAttributes);

                                    if ($result['created']) {
                                        app('eComProdContr')->postSaveAllVersions([$result['variation']->id => ['attribute_sets' => $addedAttributes]], $this->productVariation, $product->id, $response);
                                        ProductVariation::where('id', $result['variation']->id)->update(['is_default' => 1]);

                                        $prodId = ProductVariation::where('id', $result['variation']->id)->value('product_id');
                                        $packAllProd = Product::where('id', $prodId)->first();

                                        $barcodePackAll = get_barcode();
                                        $packAllProd->upc = $barcodePackAll['upc'];
                                        $packAllProd->barcode = $barcodePackAll['barcode'];
                                        $packAllProd->private_label = $product->private_label;
                                        $packAllProd->save();

                                        $logParam = [
                                            'parent_product_id' => $product->id,
                                            'product_id' => $prodId,
                                            'sku' => $packAllProd->sku,
                                            'thread_order_id'   => $threadorder->id,
                                            'created_by' => auth()->user()->id,
                                            'reference'  => InventoryHistory::PROD_PUSH_ECOM
                                        ];
                                        log_product_history($logParam);
                                    }

                                    if (count($getSizeAttrs)) {
                                        $product->productAttributeSets()->attach([$getSizeAttrSet]);
                                        $product->productAttributes()->attach($getSizeAttrs);

                                        foreach ($getSizeAttrs as $getSizeAttr) {
                                            $addedAttributes = [];
                                            $getTypeSingleAttr = ProductAttribute::where('attribute_set_id', $getTypeAttrSet)->where('slug', 'single')->value('id');
                                            $addedAttributes[$getTypeAttrSet] = $getTypeSingleAttr;
                                            $addedAttributes[$getSizeAttrSet] = $getSizeAttr;
                                            $result = $this->productVariation->getVariationByAttributesOrCreate($product->id, $addedAttributes);
                                            if ($result['created']) {
                                                app('eComProdContr')->postSaveAllVersions([$result['variation']->id => ['attribute_sets' => $addedAttributes]], $this->productVariation, $product->id, $response);

                                                $prodId = ProductVariation::where('id', $result['variation']->id)->value('product_id');
                                                Product::where('id', $prodId)->update(['price' => $singlePrice]);
                                                $sizeProd = Product::where('id', $prodId)->first();

                                                $barcodeSize = get_barcode();
                                                $sizeProd->upc = $barcodeSize['upc'];
                                                $sizeProd->barcode = $barcodeSize['barcode'];
                                                $sizeProd->private_label = $product->private_label;
                                                $sizeProd->save();

                                                $logParam = [
                                                    'parent_product_id' => $product->id,
                                                    'product_id' => $prodId,
                                                    'sku' => $sizeProd->sku,
                                                    'thread_order_id'   => $threadorder->id,
                                                    'created_by' => auth()->user()->id,
                                                    'reference'  => InventoryHistory::PROD_PUSH_ECOM
                                                ];
                                                log_product_history($logParam);

                                            }
                                        }
                                    }
                                }
                            }
                        }

                    }
                } else {
                    $exist = true;
                }
            }
        } else {
            $success = false;
        }

        if ($exist) {
            return $response->setPreviousUrl(route('thread.index'))->setMessage('One or more variations already exists in E-commerce');
        }
        if ($success) {
            return $response->setPreviousUrl(route('thread.index'))->setMessage('Order pushed to E-commerce Successfully');
        }
    }

    public function showThreadOrderDetail($id, Request $request)
    {

        page_title()->setTitle('Thread Order Detail');

        $orderDetail = $this->threadordersRepository->findOrFail($id);

        return view('plugins/threadorders::threadOrderDetail', compact('orderDetail'));
    }

}
