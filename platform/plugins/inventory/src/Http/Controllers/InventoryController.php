<?php

namespace Botble\Inventory\Http\Controllers;

use App\Models\InventoryHistory;
use App\Models\InventoryProducts;
use App\Models\QtyAllotmentHistory;
use Botble\ACL\Models\Role;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Inventory\Http\Requests\InventoryRequest;
use Botble\Inventory\Models\Inventory;
use Botble\Inventory\Repositories\Interfaces\InventoryInterface;
use Botble\Base\Http\Controllers\BaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Botble\Inventory\Tables\InventoryTable;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Inventory\Forms\InventoryForm;
use Botble\Base\Forms\FormBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends BaseController
{
    /**
     * @var InventoryInterface
     */
    protected $inventoryRepository;

    /**
     * @param InventoryInterface $inventoryRepository
     */
    public function __construct(InventoryInterface $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * @param InventoryTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(InventoryTable $table)
    {
        page_title()->setTitle(trans('plugins/inventory::inventory.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/inventory::inventory.create'));

        /*return view('plugins/inventory::create');*/
        return $formBuilder->create(InventoryForm::class)->renderForm();
    }

    /**
     * @param InventoryRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(InventoryRequest $request, BaseHttpResponse $response)
    {
        $data = $request->input();
        $data['date'] = date('Y-m-d', strtotime($data['date']));
        $data['created_by'] = Auth::user()->id;
        $data['warehouse_sec'] = @$request->warehouse_sec;
        $inventory = $this->inventoryRepository->createOrUpdate($data);

        if ($inventory) {
            for ($i = 0; $i <= count($data); $i++) {
                if (isset($data['sku_' . $i])) {
                    $product = array();
                    $product['inventory_id'] = $inventory->id;
                    $product['product_id'] = @$data['product_id_' . $i];
                    $product['sku'] = @$data['sku_' . $i];
                    $product['barcode'] = @$data['barcode_' . $i];
                    //$product['is_variation'] = @$data['is_variation_' . $i];
                    $product['ecom_qty'] = @$data['quantity_' . $i];
                    $product['ordered_qty'] = @$data['ordered_qty_' . $i];
                    $product['received_qty'] = @$data['received_qty_' . $i];
                    if (isset($data['received_qty_' . $i])) {
                        $product['is_variation'] = 1;
                    }
                    InventoryProducts::create($product);
                } else {
                    continue;
                }
            }
        }

        event(new CreatedContentEvent(INVENTORY_MODULE_SCREEN_NAME, $request, $inventory));

        return $response
            ->setPreviousUrl(route('inventory.index'))
            ->setNextUrl(route('inventory.edit', $inventory->id))
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
        //TODO::Need Refactoring
        $inventory = Inventory::with(['products' => function ($query) {
            $query
                ->leftJoin('ec_products as p', 'p.id', 'inventory_products.product_id')
                //->leftJoin('thread_order_variations as tov', 'tov.sku', 'inventory_products.sku')
                ->select('inventory_products.*', 'p.barcode', 'p.upc', 'p.id as pid', 'p.name as pname', 'p.images as pimages', 'p.quantity as pquantity', 'p.price', 'p.sale_price');
        }])->findOrFail($id);

        event(new BeforeEditContentEvent($request, $inventory));

        page_title()->setTitle(trans('plugins/inventory::inventory.edit') . ' "' . $inventory->name . '"');

        return $formBuilder->create(InventoryForm::class, ['model' => $inventory])->renderForm();
    }

    /**
     * @param int $id
     * @param InventoryRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, InventoryRequest $request, BaseHttpResponse $response)
    {
        $data = $request->input();
        $data['date'] = date('Y-m-d', strtotime($data['date']));
        $data['updated_by'] = Auth::user()->id;
        $inventory = $this->inventoryRepository->findOrFail($id);

        $inventory->fill($data);

        $update = $this->inventoryRepository->createOrUpdate($inventory);

        InventoryProducts::where('inventory_id', $inventory->id)->delete();

        if ($update) {
            for ($i = 0; $i <= count($data); $i++) {
                if (isset($data['sku_' . $i])) {
                    $product = array();
                    $product['inventory_id'] = $inventory->id;
                    $product['product_id'] = @$data['product_id_' . $i];
                    $product['sku'] = @$data['sku_' . $i];
                    $product['barcode'] = @$data['barcode_' . $i];
                    $product['ecom_qty'] = @$data['quantity_' . $i];
                    $product['ordered_qty'] = @$data['ordered_qty_' . $i];
                    $product['received_qty'] = @$data['received_qty_' . $i];
                    if (isset($data['received_qty_' . $i])) {
                        $product['is_variation'] = 1;
                    }
                    InventoryProducts::create($product);
                } else {
                    continue;
                }
            }
        }

        event(new UpdatedContentEvent(INVENTORY_MODULE_SCREEN_NAME, $request, $inventory));

        return $response
            ->setPreviousUrl(route('inventory.index'))
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
            $inventory = $this->inventoryRepository->findOrFail($id);

            $this->inventoryRepository->delete($inventory);

            event(new DeletedContentEvent(INVENTORY_MODULE_SCREEN_NAME, $request, $inventory));

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
            $inventory = $this->inventoryRepository->findOrFail($id);
            $this->inventoryRepository->delete($inventory);
            event(new DeletedContentEvent(INVENTORY_MODULE_SCREEN_NAME, $request, $inventory));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    public function getProductByBarcode(Request $request)
    {
        $products = DB::table('ec_products')
            ->select('ec_products.id', 'ec_products.warehouse_sec', 'ec_products.images', 'ec_products.sku', 'ec_products.barcode', 'ec_products.upc', 'ec_products.name',
            'ec_products.quantity', 'thread_order_variations.quantity AS ordered_qty', 'ec_products.price', 'ec_products.sale_price', 'ec_products.is_variation', 'ec_products.private_label')
            ->leftJoin('thread_order_variations', 'thread_order_variations.sku', 'ec_products.sku')
            //->leftJoin('inventory_history', 'inventory_history.parent_product_id', 'ec_products.id')
            //->whereNull('inventory_history.inventory_id')
            //->where('ec_products.barcode', $request->get('barcode'))
            ->where('ec_products.sku', 'LIKE', $request->get('barcode') . '%')
            //->orWhere('ec_products.upc', $request->get('barcode'))
            //->orWhere('ec_products.barcode', $request->get('barcode'))
            //->orWhere('parent_sku', $request->get('barcode'))
            //->where('status', 'published')
            ->where(['ec_products.ptype' => 'R'])
            ->where('ec_products.status', '!=', BaseStatusEnum::HIDE)
            //->orderBy('ec_products.sku', 'ASC')
            ->orderBy('ec_products.name', 'ASC')
            ->orderBy('thread_order_variations.thread_order_id', 'DESC')
            ->get();
        if ($products && count($products) > 1) {
            return response()->json(['products' => $products, 'status' => 'success'], 200);
        } else {
            $getProdIdByUPC = Product::where('upc',"LIKE", "%" . $request->get('barcode') . "%")->value('id');
            $getChildIds = ProductVariation::where('configurable_product_id', $getProdIdByUPC)->pluck('product_id')->all();
            $getChildIds[] = $getProdIdByUPC;

            $products = DB::table('ec_products')
                ->select('ec_products.id', 'ec_products.warehouse_sec', 'ec_products.images', 'ec_products.sku', 'ec_products.barcode', 'ec_products.upc', 'ec_products.name',
                'ec_products.quantity', 'thread_order_variations.quantity AS ordered_qty', 'ec_products.price', 'ec_products.sale_price', 'ec_products.is_variation', 'ec_products.private_label')
                ->leftJoin('thread_order_variations', 'thread_order_variations.sku', 'ec_products.sku')
                ->whereIn('ec_products.id', $getChildIds)
                ->where(['ec_products.ptype' => 'R'])
                ->where('ec_products.status', '!=', BaseStatusEnum::HIDE)
                ->orderBy('thread_order_variations.thread_order_id', 'DESC')
                ->get();

            if ($products && count($products) > 1) {
                return response()->json(['products' => $products, 'status' => 'success'], 200);
            }

            return response()->json(['product' => [], 'status' => 'error'], 404);
        }
    }

    public function pushToEcommerce($id, BaseHttpResponse $response)
    {
        $error = null;
        $inventory = Inventory::with('products')->where('id', $id)->first();
        if ($inventory && $inventory->status == 'published' && !$inventory->is_full_released) {
            if (count($inventory->products)) {
                foreach ($inventory->products as $inv_product) {
                    $product = Product::where('id', $inv_product->product_id)->where('is_variation', 1)->first();
                    if ($product) {
                        if ($inv_product->is_released) {
                            $error = 'some products already released in this inventory!';
                        } else {

                            $old_stock = $product->quantity;
                            $product->quantity += $inv_product->received_qty;
                            $product->with_storehouse_management = 1;

                            $qtyOS = 0;
                            $getOSPercentage = Role::where('slug', Role::ONLINE_SALES)->value('qty_allotment_percentage');
                            if ($getOSPercentage) {
                                $getOSPercentage = $getOSPercentage / 100;
                                $qtyOS = round($getOSPercentage * $inv_product->received_qty);
                                $product->online_sales_qty = $product->online_sales_qty + $qtyOS;
                            }

                            $qtyIS = 0;
                            $getISPercentage = Role::where('slug', Role::IN_PERSON_SALES)->value('qty_allotment_percentage');
                            if ($getISPercentage) {
                                $getISPercentage = $getISPercentage / 100;
                                $qtyIS = round($getISPercentage * $inv_product->received_qty);
                                $product->in_person_sales_qty = $product->in_person_sales_qty + $qtyIS;
                            }

                            if ($product->save()) {

                                $getParentProdId = ProductVariation::where('product_id', $product->id)->value('configurable_product_id');
                                $logParam = [
                                    'parent_product_id' => $getParentProdId,
                                    'product_id'        => $product->id,
                                    'sku'               => $product->sku,
                                    'quantity'          => $inv_product->received_qty,
                                    'new_stock'         => $product->quantity,
                                    'old_stock'         => $old_stock,
                                    'created_by'        => Auth::user()->id,
                                    'inventory_id'      => $inventory->id,
                                    'reference'         => InventoryHistory::PROD_STOCK_ADD
                                ];
                                log_product_history($logParam);

                                QtyAllotmentHistory::create([
                                    'product_id'          => $product->id,
                                    'online_sales_qty'    => $qtyOS,
                                    'in_person_sales_qty' => $qtyIS,
                                    'reference'           => InventoryHistory::PROD_STOCK_ADD
                                ]);

                            }
                        }
                    } else {
                        $error = 'Some of the inventory products does not exists';
                    }
                }
            } else {
                $error = 'Inventory have no products';
            }
        } else {
            $error = 'Invalid inventory or Inventory is not published or Already Released';
        }

        if (!is_null($error)) {
            return $response->setPreviousUrl(route('inventory.index'))
                ->setError($error);
        } else {
            return $response->setPreviousUrl(route('inventory.index'))
                ->setMessage('Inventory has been pushed into ecommerce successfully');
        }
    }

    public function releaseProduct($inv_id, $prod_id, BaseHttpResponse $response)
    {
        $error = null;

        $getProdIds = ProductVariation::where('configurable_product_id', $prod_id)->pluck('product_id')->all();
        $getProdIds[] = (int)$prod_id;

        $inventory = Inventory::with([
            'products' => function ($q) use ($getProdIds) {
                $q->whereIn('product_id', $getProdIds);
            }
        ])
            ->where('id', $inv_id)
            ->first();

        if ($inventory && $inventory->status == 'published' && !$inventory->is_full_released) {
            if (count($inventory->products)) {
                foreach ($inventory->products as $inv_product) {
                    $product = Product::where('id', $inv_product->product_id)/*->where('is_variation', 1)*/ ->first();
                    if ($product) {
                        if ($inv_product->is_released) {
                            $error = 'some products already released in this inventory!';
                        } else {

                            $old_stock = $product->quantity;
                            $product->quantity = $product->quantity + $inv_product->received_qty;
                            $product->with_storehouse_management = 1;

                            $qtyOS = 0;
                            $getOSPercentage = Role::where('slug', Role::ONLINE_SALES)->value('qty_allotment_percentage');
                            if ($getOSPercentage) {
                                $getOSPercentage = $getOSPercentage / 100;
                                $qtyOS = round($getOSPercentage * $inv_product->received_qty);
                                $product->online_sales_qty = $product->online_sales_qty + $qtyOS;
                            }

                            $qtyIS = 0;
                            $getISPercentage = Role::where('slug', Role::IN_PERSON_SALES)->value('qty_allotment_percentage');
                            if ($getISPercentage) {
                                $getISPercentage = $getISPercentage / 100;
                                $qtyIS = round($getISPercentage * $inv_product->received_qty);
                                $product->in_person_sales_qty = $product->in_person_sales_qty + $qtyIS;
                            }

                            if ($product->save()) {

                                $getParentProdId = ProductVariation::where('product_id', $product->id)->value('configurable_product_id');
                                $logParam = [
                                    'parent_product_id' => $getParentProdId,
                                    'product_id'        => $product->id,
                                    'sku'               => $product->sku,
                                    'quantity'          => $inv_product->received_qty,
                                    'new_stock'         => $product->quantity,
                                    'old_stock'         => $old_stock,
                                    'created_by'        => Auth::user()->id,
                                    'inventory_id'      => $inventory->id,
                                    'reference'         => InventoryHistory::PROD_STOCK_ADD
                                ];
                                log_product_history($logParam);

                                QtyAllotmentHistory::create([
                                    'product_id'          => $product->id,
                                    'online_sales_qty'    => $qtyOS,
                                    'in_person_sales_qty' => $qtyIS,
                                    'reference'           => InventoryHistory::PROD_STOCK_ADD
                                ]);

                            }
                        }
                    } else {
                        $error = 'product does not exists';
                    }
                }
            } else {
                $error = 'Inventory have no products';
            }
        } else {
            $error = 'Invalid inventory or Inventory is not published or Already Released';
        }

        if (!is_null($error)) {
            return $response->setPreviousUrl(back())->setError($error);
        } else {
            return $response->setPreviousUrl(back())->setMessage('Inventory has been pushed into e-commerce successfully');
        }
    }

    public function showInventoryDetail($id, Request $request)
    {
        page_title()->setTitle('Inventory Detail');

        $inventoryDetail = $this->inventoryRepository->findOrFail($id);

        return view('plugins/inventory::details', compact('inventoryDetail'));
    }
}
