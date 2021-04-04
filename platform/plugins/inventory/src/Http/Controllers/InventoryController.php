<?php

namespace Botble\Inventory\Http\Controllers;

use App\Models\InventoryHistory;
use App\Models\InventoryProducts;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Ecommerce\Models\Product;
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
        $inventory = $this->inventoryRepository->createOrUpdate($data);

        if ($inventory) {
            for ($i = 0; $i <= count($data); $i++) {
                if (isset($data['sku_' . $i])) {
                    $product = array();
                    $product['inventory_id'] = $inventory->id;
                    $product['product_id'] = $data['product_id_' . $i];
                    $product['sku'] = $data['sku_' . $i];
                    $product['barcode'] = $data['barcode_' . $i];
                    $product['ecom_pack_qty'] = $data['quantity_' . $i];
                    $product['ordered_pack_qty'] = $data['ordered_qty_' . $i];
                    $product['received_pack_qty'] = $data['received_qty_' . $i];
                    InventoryProducts::create($product);
                } else {
                    break;
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
                    $product['product_id'] = $data['product_id_' . $i];
                    $product['sku'] = $data['sku_' . $i];
                    $product['barcode'] = $data['barcode_' . $i];
                    $product['ecom_pack_qty'] = $data['quantity_' . $i];
                    $product['ordered_pack_qty'] = $data['ordered_qty_' . $i];
                    $product['received_pack_qty'] = $data['received_qty_' . $i];
                    InventoryProducts::create($product);
                } else {
                    break;
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
        $product = Product::select('ec_products.id', 'ec_products.images', 'ec_products.sku', 'ec_products.barcode', 'ec_products.upc', 'ec_products.name',
            'ec_products.quantity', 'thread_order_variations.quantity AS ordered_qty', 'ec_products.price', 'ec_products.sale_price')
            ->leftJoin('thread_order_variations', 'thread_order_variations.sku', 'ec_products.sku')
            ->where('ec_products.barcode', $request->get('barcode'))
            ->orWhere('ec_products.sku', $request->get('barcode'))
            ->orWhere('parent_sku', $request->get('barcode'))
            /*->where('status', 'published')*/
            ->first();
        if ($product) {
            return response()->json(['product' => $product, 'status' => 'success'], 200);
        } else {
            return response()->json(['product' => [], 'status' => 'error'], 404);
        }
    }

    public function pushToEcommerce($id, BaseHttpResponse $response)
    {
        $error = null;
        $inventory = Inventory::with('products')->where('id', $id)->first();
        if ($inventory && $inventory->status == 'published') {
            if (count($inventory->products)) {
                foreach ($inventory->products as $inv_product) {
                    $product = Product::where('sku', $inv_product->sku)->first();
                    if ($product) {
                        $old_stock = $product->quantity;
                        $product->quantity = $product->quantity + $inv_product->received_pack_qty;
                        if ($product->save()) {

                            InventoryHistory::create([
                                'product_id' => $product->id,
                                'quantity' => $inv_product->received_pack_qty,
                                'new_stock' => $product->quantity,
                                'old_stock' => $old_stock,
                                'created_by' => Auth::user()->id,
                                'inventory_id' => $inventory->id,
                                'reference' => 'inventory.push_to_ecommerce'
                            ]);

                        }
                    } else {
                        $error = 'Some of the inventory products does not exists';
                    }

                }
            } else {
                $error = 'Inventory have no products';
            }
        } else {
            $error = 'Invalid inventory or Inventory is not published';
        }

        if (!is_null($error)) {
            return $response->setPreviousUrl(route('inventory.index'))
                ->setError($error);
        } else {
            return $response->setPreviousUrl(route('inventory.index'))
                ->setMessage('Inventory has been pushed into ecommerce successfully');
        }
    }
}
