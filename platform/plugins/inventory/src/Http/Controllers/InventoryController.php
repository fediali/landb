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
        $data = $request->all();
        $data['created_by'] = Auth::user()->id;
        $data['date'] = Carbon::parse($data['date']);
        $inventory = $this->inventoryRepository->createOrUpdate($data);

        if($inventory){
          for ($i=0; $i<=count($data) ; $i++ ) {
            if(isset($data['sku_'.$i])){
              $product = array();
              $product['inventory_id'] = $inventory->id;
              $product['sku'] = $data['sku_'.$i];
              $product['barcode'] = $data['barcode_'.$i];
              $product['quantity'] = $data['quantity_'.$i];

              InventoryProducts::create($product);
            }else{
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
        $inventory = Inventory::with(['products' => function($query){
          $query->leftJoin('ec_products as p', 'p.sku','inventory_products_pivot.sku')->select(
              'inventory_products_pivot.*',
              'p.id as pid',
              'p.name as pname',
              'p.images as pimages',
              'p.quantity as pquantity'
          );
        }])->findOrFail($id);
//dd($inventory);
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
        $data = $request->all();
        $data['updated_by'] = Auth::user()->id;
        $data['date'] = Carbon::parse($data['date']);
        $inventory = $this->inventoryRepository->findOrFail($id);

        $inventory->fill($request->input());

        $update = $this->inventoryRepository->createOrUpdate($inventory);

        InventoryProducts::where('inventory_id' , $inventory->id)->delete();

        if($update){
          for ($i=0; $i<=count($data) ; $i++ ) {
            if(isset($data['sku_'.$i])){
              $product = array();
              $product['inventory_id'] = $update->id;
              $product['sku'] = $data['sku_'.$i];
              $product['barcode'] = $data['barcode_'.$i];
              $product['quantity'] = $data['quantity_'.$i];

              InventoryProducts::create($product);
            }else{
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


    public function getProductByBarcode(Request $request){
      $product = Product::where('barcode',$request->get('barcode'))->orWhere('sku', $request->get('barcode'))->where('status', 'published')->first();
      if($product){
        return response()->json(['product' => $product, 'status' => 'success'], 200);
      }else{
        return response()->json(['product' => [], 'status' => 'error'], 404);
      }
    }

    public function pushToEcommerce($id, BaseHttpResponse $response){
      $error = null;
      $inventory = Inventory::with('products')->where('id',$id)->first();
      if($inventory && $inventory->status == 'published'){
        if(count($inventory->products)){
          foreach ($inventory->products as $inv_product){
            $product = Product::where('sku', $inv_product->sku)->first();
            if($product){
              $old_stock = $product->quantity;
              $product->quantity =  $product->quantity + $inv_product->quantity;
              if($product->save()){

                InventoryHistory::create([
                    'product_id' => $product->id,
                    'quantity' => $inv_product->quantity,
                    'new_stock' => $product->quantity,
                    'old_stock' => $old_stock,
                    'created_by' => Auth::user()->id,
                    'inventory_id' => $inventory->id,
                    'reference' => 'inventory.push_to_ecommerce'
                ]);

              }
            }else{
              $error = 'Some of the inventory products does not exists';
            }

          }
        }else{
          $error = 'Inventory have no products';
        }
      }else{
        $error = 'Invalid inventory or Inventory is not published';
      }

      if (!is_null($error)) {
        return $response->setPreviousUrl(route('inventory.index'))
            ->setError($error);
      }else{
        return $response->setPreviousUrl(route('inventory.index'))
            ->setMessage('Inventory has been pushed into ecommerce successfully');
      }
    }
}
