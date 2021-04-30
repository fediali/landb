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
    protected $hasFilter = true;

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
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('products.edit')) {
                    return $item->name;
                }

                return Html::link(route('products.edit', $item->id), $item->name);
            })
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
            ->editColumn('order', function ($item) {
                return view('plugins/ecommerce::products.partials.sort-order', compact('item'))->render();
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
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
                $preOrderQty = OrderProduct::join('ec_orders', 'ec_orders.id', 'ec_order_product.order_id')
                    ->where('order_type', Order::PRE_ORDER)
                    ->whereNotIn('status', [OrderStatusEnum::CANCELED, OrderStatusEnum::PENDING])
                    ->sum('qty');
                $orderQty = OrderProduct::join('ec_orders', 'ec_orders.id', 'ec_order_product.order_id')
                    ->where('order_type', Order::PRE_ORDER)
                    ->whereNotIn('status', [OrderStatusEnum::CANCELED, OrderStatusEnum::PENDING])
                    ->groupBy('ec_orders.id')
                    ->count('ec_orders.id');
                $html = '<span>'.$preOrderQty.'</span><br><span><em>Order : '.$orderQty.'</em></span>';
                return $html;
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                $html = '<a href="' . route('products.inventory_history', $item->id) . '" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="Inventory History"><i class="fa fa-list-alt"></i></a>';
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
            'ec_products.quantity',
            'ec_products.quantity AS single_qty',
            'ec_products.quantity AS order_qty',
            'ec_products.images',
            'ec_products.price',
            'ec_products.sale_price',
            'ec_products.sale_type',
            'ec_products.start_date',
            'ec_products.end_date',
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
            'id'           => [
                'name'  => 'ec_products.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'image'        => [
                'name'  => 'ec_products.images',
                'title' => trans('plugins/ecommerce::products.image'),
                'width' => '100px',
                'class' => 'text-center',
            ],
            'name'         => [
                'name'  => 'ec_products.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'price'        => [
                'name'  => 'ec_products.price',
                'title' => trans('plugins/ecommerce::products.price'),
                'class' => 'text-left',
            ],
            'sku'          => [
                'name'  => 'ec_products.sku',
                'title' => trans('plugins/ecommerce::products.sku'),
                'class' => 'text-left',
            ],
            'quantity'     => [
                'name'  => 'ec_products.quantity',
                'title' => 'Pack Qty',
                'class' => 'text-left',
            ],
            'single_qty'   => [
                'name'  => 'ec_products.single_qty',
                'title' => 'Single Qty',
                'class' => 'text-left',
            ],
            'product_type' => [
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
            'order_qty'      => [
                'name'  => 'ec_products.order_qty',
                'title' => 'Pre-order Qty',
                'width' => '100px',
                'class' => 'text-center',
            ],
            'created_at'   => [
                'name'  => 'ec_products.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-center',
            ],
            'status'       => [
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
            'ec_products.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'ec_products.order'      => [
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
            ],
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
