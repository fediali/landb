<?php

namespace Botble\Ecommerce\Tables\Reports;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Exports\ProductExport;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class TopInventoriesTable extends TableAbstract
{
    protected $hasOperations = false;
    protected $hasActions = false;

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
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('sku', function ($item) {
                return $item->sku ? $item->sku : '&mdash;';
            })
            ->editColumn('category_id', function ($item) {
                return $item->category_id ? $item->category->name : '&mdash;';
            })
            ->editColumn('quantity', function ($item) {
                $getPackId = ProductVariation::where('configurable_product_id', $item->id)->where('is_default', 1)->value('product_id');
                $packQty = Product::where('id', $getPackId)->value('quantity');
                Product::where('id', $item->id)->update(['quantity' => $packQty]);
                return $packQty;
            })
            ->editColumn('single_qty', function ($item) {
                $getSingleIds = ProductVariation::where('configurable_product_id', $item->id)->where('is_default', 0)->pluck('product_id')->all();
                $singleQty = 0;
                $skuQty = '';
                foreach ($getSingleIds as $getSingleId) {
                    $singleSkuQty = Product::where('id', $getSingleId)->select('sku', 'quantity')->first();
                    if ($singleSkuQty) {
                        $singleQty += $singleSkuQty->quantity;
                        $skuQty .= explode('-single-', $singleSkuQty->sku)[1].':'.$singleSkuQty->quantity.' | ';
                    }
                }
                Product::where('id', $item->id)->update(['single_qty' => $singleQty]);
                return '<span title="'.$skuQty.'" style="cursor:pointer">'.$singleQty.'</span>';
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
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
            'ec_products.sku',
            'ec_products.category_id',
            'ec_products.quantity',
            'ec_products.extra_qty',
            'ec_products.single_qty',
        ];

        $query = $model->select($select)
            ->where(['is_variation' => 0, 'ptype' => 'R'])
            ->where('status', '!=', BaseStatusEnum::HIDE)
            ->orderBy('ec_products.quantity', 'DESC')
            ->orderBy('ec_products.single_qty', 'DESC')
            ->orderBy('ec_products.extra_qty', 'DESC');

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        $arr = [
            'id'            => [
                'name'  => 'ec_products.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'searchable' => 'true',
                //'visible' => false,
            ],
            'sku'        => [
                'name'  => 'ec_products.sku',
                'title' => trans('plugins/ecommerce::products.sku'),
                'class' => 'text-left',
                'width' => '100px',
            ],
            'name'       => [
                'name'      => 'ec_products.name',
                'title'     => trans('core/base::tables.name'),
                'class'     => 'text-left',
                'width'     => '200px',
                'font-size' => '15px',
            ],
            'category_id'       => [
                'name'      => 'ec_products.category_id',
                'title'     => 'Category',
                'class'     => 'text-left',
                'width'     => '100px',
                'font-size' => '15px',
            ],
            'quantity'      => [
                'name'  => 'ec_products.quantity',
                'title' => 'Pack Qty',
                'class' => 'text-left',
            ],
            'extra_qty'      => [
                'name'  => 'ec_products.extra_qty',
                'title' => 'Extra Qty',
                'class' => 'text-left red_font',
            ],
            'single_qty'    => [
                'name'  => 'ec_products.single_qty',
                'title' => 'Single Qty',
                'class' => 'text-left',
            ]
        ];

        return $arr;
    }

    /**
     * {@inheritDoc}
     */
    public function renderTable($data = [], $mergeData = [])
    {
        if ($this->query()->count() === 0 && !$this->request()->wantsJson() && $this->request()->input('filter_table_id') !== $this->getOption('id')) {
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
            //'export',
            'reload',
        ];
    }

}
