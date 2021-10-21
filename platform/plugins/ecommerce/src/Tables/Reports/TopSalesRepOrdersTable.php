<?php

namespace Botble\Ecommerce\Tables\Reports;

use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Illuminate\Contracts\Routing\UrlGenerator;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Ecommerce\Models\Order;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;
use Botble\ACL\Models\Role;
use Throwable;
use BaseHelper;
use Html;


class TopSalesRepOrdersTable extends TableAbstract
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
    protected $customFilterTemplate = 'plugins/ecommerce::orders.topSalesRepOrderFilter';

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
            ->editColumn('salesperson_id', function ($item) {
                return $item->salesperson ? $item->salesperson->getFullName() : 'N/A';
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
            'ec_orders.salesperson_id',
            'ec_orders.created_at'
        ];

        $query = $model
            ->select($select)
            ->selectRaw('SUM(ec_orders.sub_total) AS sales_amount')
            ->selectRaw('COUNT(ec_orders.id) AS sales_order_count')
            ->where('ec_orders.is_finished', 1)
            ->where('ec_orders.salesperson_id', '>', 0)
            ->whereNotNull('ec_orders.salesperson_id')
            ->groupBy('ec_orders.salesperson_id');

        $search_items = $this->request()->all();
        if (!empty($search_items)) {
            if (isset($search_items['from_date'])) {
                $query->whereDate('ec_orders.created_at', '>=', Carbon::createFromFormat('m-d-Y', $search_items['from_date'])->format('Y-m-d'));
            }
            if (isset($search_items['to_date'])) {
                $query->whereDate('ec_orders.created_at', '<=', Carbon::createFromFormat('m-d-Y', $search_items['to_date'])->format('Y-m-d'));
            }
            if (isset($search_items['salesperson_id'])) {
                $query->where('ec_orders.salesperson_id', $search_items['salesperson_id']);
            }
        }

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        $columns = [
            'salesperson_id' => [
                'name'  => 'ec_orders.salesperson_id',
                'title' => 'Salesperson',
                'class' => 'text-left',
            ],
            'sales_order_count' => [
                'name'  => 'ec_orders.sales_order_count',
                'title' => 'Order Count',
                'class' => 'text-left',
            ],
            'sales_amount' => [
                'name'  => 'ec_orders.sales_amount',
                'title' => 'Sales Amount',
                'class' => 'text-left',
            ],
            'created_at'      => [
                'name'  => 'ec_orders.created_at',
                'title' => trans('core/base::tables.created_at'),
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
        $data['salesperson'] = Order::join('role_users', 'role_users.user_id', 'ec_orders.salesperson_id')
            ->join('roles', 'roles.id', 'role_users.role_id')
            ->join('users', 'users.id', 'role_users.user_id')
            ->whereIn('roles.slug', [Role::ONLINE_SALES,Role::IN_PERSON_SALES])
            ->pluck('users.username', 'users.id')
            ->all();

        $search_items = $this->request()->all();
        $data['search_items'] = $search_items;

        return view($this->customFilterTemplate, compact('data'))->render();
    }

}
