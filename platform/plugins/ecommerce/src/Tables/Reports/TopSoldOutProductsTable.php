<?php

namespace Botble\Ecommerce\Tables\Reports;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Exports\ProductExport;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\UserSearch;
use Botble\Ecommerce\Models\UserSearchItem;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Orderstatuses\Models\Orderstatuses;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Yajra\DataTables\DataTables;

class TopSoldOutProductsTable extends TableAbstract
{
    protected $hasOperations = false;
    protected $hasActions = false;

    /**
     * @var bool
     */
    public $hasCustomFilter = true;

    /**
     * @var string
     */
    protected $customFilterTemplate = 'plugins/ecommerce::products.topSoldOutProductFilter';

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
                return $item->category_id ? @$item->category->name : '&mdash;';
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
            'ec_products.oos_date',
        ];

        $query = $model->select($select)
            ->join('ec_product_variations', 'ec_product_variations.configurable_product_id', 'ec_products.id')
            //->where(['is_variation' => 1, 'ptype' => 'R'])
            ->where('ec_product_variations.is_default', 1)
            ->where('ec_products.quantity', 0)
            ->where('status', '!=', BaseStatusEnum::HIDE)
            ->orderBy('ec_products.oos_date', 'DESC');

        $search_items = $this->request()->all();
        if (!empty($search_items)) {
            if (isset($search_items['from_date'])) {
                $query->whereDate('ec_products.oos_date', '>=', Carbon::createFromFormat('m-d-Y', $search_items['from_date'])->format('Y-m-d'));
            }
            if (isset($search_items['to_date'])) {
                $query->whereDate('ec_products.oos_date', '<=', Carbon::createFromFormat('m-d-Y', $search_items['to_date'])->format('Y-m-d'));
            }
        }

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
                'searchable' => 'true',
            ],
            'sku'        => [
                'name'  => 'ec_products.sku',
                'title' => trans('plugins/ecommerce::products.sku'),
                'class' => 'text-left',
            ],
            'name'       => [
                'name'      => 'ec_products.name',
                'title'     => trans('core/base::tables.name'),
                'class'     => 'text-left',
                'font-size' => '15px',
            ],
            'category_id'       => [
                'name'      => 'ec_products.category_id',
                'title'     => 'Category',
                'class'     => 'text-left',
                'font-size' => '15px',
            ],
            'oos_date'      => [
                'name'  => 'ec_orders.oos_date',
                'title' => 'OOS Date',
                'class' => 'text-left',
            ],
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

    /**
     * @return bool
     */
    public function isHasCustomFilter(): bool
    {
        return $this->hasCustomFilter;
    }

    /**
     * @return string
     * @throws Throwable
     */
    public function renderCustomFilter(): string
    {
        $search_items = $this->request()->all();
        $data['search_items'] = $search_items;

        return view($this->customFilterTemplate, compact('data'))->render();
    }
}
