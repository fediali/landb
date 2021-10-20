<?php

namespace Botble\Ecommerce\Tables\Reports;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Orderstatuses\Models\Orderstatuses;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Carbon;
use Throwable;
use Yajra\DataTables\DataTables;
use BaseHelper;

class MostOrdersStatesTable extends TableAbstract
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
    protected $customFilterTemplate = 'plugins/ecommerce::orders.mostOrderStateFilter';

    /**
     * OrderTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param OrderInterface $orderRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, OrderInterface $orderRepository)
    {
        $this->repository = $orderRepository;
        $this->setOption('id', 'table-orders');
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
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
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
            'ec_orders.id',
            'ec_order_addresses.state',
            'ec_order_addresses.country',
            'ec_orders.created_at',
        ];

        $query = $model
            ->select($select)
            ->selectRaw('SUM(ec_orders.sub_total) AS sales_amount')
            ->join('ec_order_addresses', 'ec_order_addresses.order_id', 'ec_orders.id')
            ->where('ec_orders.is_finished', 1)
            ->where('ec_order_addresses.type', 'shipping')
            ->groupBy('ec_order_addresses.state');

        $search_items = $this->request()->all();
        if (!empty($search_items)) {
            if (isset($search_items['from_date'])) {
                $query->whereDate('ec_orders.created_at', '>=', Carbon::createFromFormat('m-d-Y', $search_items['from_date'])->format('Y-m-d'));
            }
            if (isset($search_items['to_date'])) {
                $query->whereDate('ec_orders.created_at', '<=', Carbon::createFromFormat('m-d-Y', $search_items['to_date'])->format('Y-m-d'));
            }
            /*if (isset($search_items['order_status'])) {
                $query->where('ec_orders.status', $search_items['order_status']);
            }*/
        }

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        $columns = [
            'id'             => [
                'name'  => 'ec_orders.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-left',
            ],
            'state'      => [
                'name'  => 'ec_order_addresses.state',
                'title' => 'State',
                'class' => 'text-left',
            ],
            'country'      => [
                'name'  => 'ec_order_addresses.country',
                'title' => 'Country',
                'class' => 'text-left',
            ],
            'sales_amount'      => [
                'name'  => 'ec_orders.sales_amount',
                'title' => 'Sales Amount',
                'class' => 'text-left',
            ],
            'created_at'      => [
                'name'  => 'ec_orders.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-left',
            ],
        ];
        return $columns;
    }

    /**
     * {@inheritDoc}
     */
    public function renderTable($data = [], $mergeData = [])
    {
        if ($this->query()->count() === 0 && !$this->request()->wantsJson() && $this->request()->input('filter_table_id') !== $this->getOption('id')) {
            return view('plugins/ecommerce::orders.intro');
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
        //$data['order_statuses'] = Orderstatuses::where('status', BaseStatusEnum::PUBLISHED)->pluck('name')->all();

        $search_items = $this->request()->all();
        $data['search_items'] = $search_items;

        return view($this->customFilterTemplate, compact('data'))->render();
    }

}
