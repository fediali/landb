<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\ACL\Models\Role;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Exports\ProductExport;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Thread\Models\Thread;
use Botble\Threadorders\Models\Threadorders;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use RvMedia;
use Yajra\DataTables\DataTables;

class ProductTable extends TableAbstract
{
    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = false;

    /**
     * @var string
     */
    protected $exportClass = ProductExport::class;

    /**
     * ProductTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ProductInterface $productRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ProductInterface $productRepository)
    {
        $this->repository = $productRepository;
        $this->setOption('id', 'table-products');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['products.edit', 'products.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            /*->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('products.edit')) {
                    return $item->name;
                }
                return Html::link(route('products.edit', $item->id), $item->name);
            })*/
            ->editColumn('image', function ($item) {
                if ($this->request()->input('action') == 'csv') {
                    return RvMedia::getImageUrl($item->image, null, false, RvMedia::getDefaultImage());
                }

                if ($this->request()->input('action') == 'excel') {
                    return RvMedia::getImageUrl($item->image, 'thumb', false, RvMedia::getDefaultImage());
                }

                return view('plugins/ecommerce::products.partials.thumbnail', compact('item'))->render();
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('price', function ($item) {
                $price = format_price($item->front_sale_price);

                if ($item->front_sale_price != $item->price) {
                    $price .= ' <del class="text-danger">' . format_price($item->price) . '</del>';
                }

                return $price;
            })
            ->editColumn('sku', function ($item) {
                return $item->sku ? $item->sku : '&mdash;';
            })
            ->editColumn('warehouse_sec', function ($item) {
                $html = '<form class="d-flex" action="' . route('products.update-wh-sec', $item->id) . '" method="POST">
                            <input type="hidden" name="_token" value="' . @csrf_token() . '">
                            <input style="width: 70px; height: 35px; margin-right:5px;" class="ui-text-area textarea-auto-height" name="warehouse_sec" value="' . $item->warehouse_sec . '" required>
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-check"></i></button>
                        </form>';
                return $html;
            })
            ->editColumn('order', function ($item) {
                return view('plugins/ecommerce::products.partials.sort-order', compact('item'))->render();
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('oos_date', function ($item) {
                return BaseHelper::formatDate($item->oos_date);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            })
            ->editColumn('quantity', function ($item) {
                $getPackId = ProductVariation::where('configurable_product_id', $item->id)->where('is_default', 1)->value('product_id');
                if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES) {
                    $packQty = Product::where('id', $getPackId)->value('online_sales_qty');
                } elseif (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES) {
                    $packQty = Product::where('id', $getPackId)->value('in_person_sales_qty');
                } else {
                    $packQty = Product::where('id', $getPackId)->value('quantity');
                }
                return $packQty;
            })
            ->editColumn('single_qty', function ($item) {
                $getSingleIds = ProductVariation::where('configurable_product_id', $item->id)->where('is_default', 0)->pluck('product_id')->all();
                if (@auth()->user()->roles[0]->slug == Role::ONLINE_SALES) {
                    $singleQty = Product::whereIn('id', $getSingleIds)->sum('online_sales_qty');
                } elseif (@auth()->user()->roles[0]->slug == Role::IN_PERSON_SALES) {
                    $singleQty = Product::whereIn('id', $getSingleIds)->sum('in_person_sales_qty');
                } else {
                    $singleQty = Product::whereIn('id', $getSingleIds)->sum('quantity');
                }
                return $singleQty;
            })
            ->editColumn('order_qty', function ($item) {
                $getProdIds = ProductVariation::where('configurable_product_id', $item->id)->pluck('product_id')->all();
                $getProdIds[] = $item->id;
                $preOrderQty = OrderProduct::join('ec_orders', 'ec_orders.id', 'ec_order_product.order_id')
                    ->whereIn('product_id', $getProdIds)
                    ->where('order_type', Order::PRE_ORDER)
                    ->whereNotIn('status', [OrderStatusEnum::CANCELED, OrderStatusEnum::PENDING])
                    ->sum('qty');
                $orderQty = OrderProduct::join('ec_orders', 'ec_orders.id', 'ec_order_product.order_id')
                    ->whereIn('product_id', $getProdIds)
                    ->where('order_type', Order::PRE_ORDER)
                    ->whereNotIn('status', [OrderStatusEnum::CANCELED, OrderStatusEnum::PENDING])
                    ->groupBy('ec_orders.id')
                    ->get();
                $html = '&mdash;';
                if ($orderQty && $preOrderQty) {
                    $html = '<a href="' . route('orders.index', ['order_type' => 'pre_order', 'product_id' => $item->id]) . '"><span>' . $preOrderQty . '</span><br><span><em>Order : ' . count($orderQty) . '</em></span></a>';
                }
                return $html;
            })
            ->editColumn('sold_qty', function ($item) {
                $getProdIds = ProductVariation::where('configurable_product_id', $item->id)->pluck('product_id')->all();
                $getProdIds[] = $item->id;
                $soldQty = OrderProduct::join('ec_orders', 'ec_orders.id', 'ec_order_product.order_id')
                    ->whereIn('product_id', $getProdIds)
                    ->whereNotIn('status', [OrderStatusEnum::CANCELED, OrderStatusEnum::PENDING])
                    ->sum('qty');
                $html = '&mdash;';
                if ($soldQty) {
                    $html = '<a href="' . route('orders.index', ['product_id' => $item->id]) . '"><span>' . $soldQty . '</span></a>';
                }
                return $html;
            })
            ->editColumn('reorder_qty', function ($item) {
                $getProdSKU = Product::where('id', $item->id)->value('sku');
                $reOrderQty = Threadorders::join('thread_order_variations', 'thread_order_variations.thread_order_id', 'threadorders.id')
                    ->leftJoin('inventory_history', 'inventory_history.order_id', 'threadorders.id')
                    ->where('thread_order_variations.sku', $getProdSKU)
                    ->where('threadorders.order_status', Thread::REORDER)
                    ->whereNull('inventory_history.order_id')
                    ->value('thread_order_variations.quantity');
                $html = '<span>' . $reOrderQty . '</span>';
                return $html;
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                $html = '<a href="' . route('products.inventory_history', $item->id) . '" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="Inventory History"><i class="fa fa-list-alt"></i></a>';
                $html .= '<a href="' . route('products.product_timeline', $item->id) . '" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="Product Timeline"><i class="fa fa-list-alt"></i></a>';
                $html .= '<a href="#" data-toggle="modal" data-target="#allotment-modal" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="Quantity Allotment"><i class="fa fa-check-circle"></i></a>';

                return $this->getOperations('products.edit', 'products.destroy', $item, $html);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'ec_products.id',
            'ec_products.name',
            'ec_products.order',
            'ec_products.created_at',
            'ec_products.status',
            'ec_products.product_type',
            'ec_products.sku',
            'ec_products.warehouse_sec',
            'ec_products.quantity',
            'ec_products.quantity AS single_qty',
            'ec_products.quantity AS order_qty',
            'ec_products.quantity AS sold_qty',
            'ec_products.quantity AS reorder_qty',
            'ec_products.images',
            'ec_products.price',
            'ec_products.sale_price',
            'ec_products.sale_type',
            'ec_products.start_date',
            'ec_products.end_date',
            'ec_products.oos_date',
        ];

        $query = $model
            ->select($select)
            ->where('is_variation', 0);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function htmlDrawCallbackFunction(): ?string
    {
        return parent::htmlDrawCallbackFunction() . '$(".editable").editable();';
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'            => [
                'name'  => 'ec_products.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'image'         => [
                'name'  => 'ec_products.images',
                'title' => trans('plugins/ecommerce::products.image'),
                'width' => '100px',
                'class' => 'text-center',
            ],
            'name'          => [
                'name'  => 'ec_products.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'price'         => [
                'name'  => 'ec_products.price',
                'title' => trans('plugins/ecommerce::products.price'),
                'class' => 'text-left',
            ],
            'sku'           => [
                'name'  => 'ec_products.sku',
                'title' => trans('plugins/ecommerce::products.sku'),
                'class' => 'text-left',
            ],
            'warehouse_sec' => [
                'name'  => 'ec_products.warehouse_sec',
                'title' => 'SEC',
                'width' => '100px',
                'class' => 'text-left',
            ],
            'quantity'      => [
                'name'  => 'ec_products.quantity',
                'title' => 'Pack Qty',
                'class' => 'text-left',
            ],
            'single_qty'    => [
                'name'  => 'ec_products.single_qty',
                'title' => 'Single Qty',
                'class' => 'text-left',
            ],
            'product_type'  => [
                'name'  => 'ec_products.product_type',
                'title' => 'Type',
                'class' => 'text-left',
            ],
            /*'order'      => [
                'name'  => 'ec_products.order',
                'title' => trans('core/base::tables.order'),
                'width' => '50px',
                'class' => 'text-center',
            ],*/
            'order_qty'     => [
                'name'  => 'ec_products.order_qty',
                'title' => 'Pre-order Qty',
                'width' => '100px',
                'class' => 'text-center',
            ],
            'reorder_qty'   => [
                'name'  => 'ec_products.reorder_qty',
                'title' => 'Re-order Qty',
                'class' => 'text-center',
            ],
            'sold_qty'      => [
                'name'  => 'ec_products.sold_qty',
                'title' => 'Sold Qty',
                'class' => 'text-center',
            ],
            'created_at'    => [
                'name'  => 'ec_products.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-center',
            ],
            'oos_date'      => [
                'name'  => 'ec_products.oos_date',
                'title' => 'OOS Date',
                'width' => '100px',
                'class' => 'text-center',
            ],
            'status'        => [
                'name'  => 'ec_products.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
                'class' => 'text-center',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('products.create'), 'products.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Product::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('products.deletes'), 'products.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            /*'ec_products.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'ec_products.oos_date' => [
                'title' => 'OOS Date',
                'type'  => 'date',
            ],*/
            /*'ec_products.order'      => [
                'title'    => trans('core/base::tables.order'),
                'type'     => 'number',
                'validate' => 'required|min:0',
            ],
            'ec_products.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'ec_products.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],*/
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function renderTable($data = [], $mergeData = [])
    {
        if ($this->query()->count() === 0 &&
            !$this->request()->wantsJson() &&
            $this->request()->input('filter_table_id') !== $this->getOption('id')
        ) {
            return view('plugins/ecommerce::products.intro');
        }

        return parent::renderTable($data, $mergeData);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultButtons(): array
    {
        return [
            'export',
            'reload',
        ];
    }
}
