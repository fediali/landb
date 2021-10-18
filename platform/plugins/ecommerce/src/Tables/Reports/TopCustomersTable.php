<?php

namespace Botble\Ecommerce\Tables\Reports;

use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use BaseHelper;
use Throwable;
use Html;

class TopCustomersTable extends TableAbstract
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
    protected $customFilterTemplate = 'plugins/ecommerce::customers.topCustomerFilter';


    /**
     * CustomerTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param CustomerInterface $customerRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, CustomerInterface $customerRepository)
    {
        $this->repository = $customerRepository;
        $this->setOption('id', 'table-customers');
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
                if ($item->salesperson_id) {
                    return isset($item->salesperson) ? $item->salesperson->username : 'N/A';
                } else {
                    return 'N/A';
                }
            })
            ->editColumn('company', function ($item) {
                return $item->detail->company;
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status;
            })
            ->editColumn('last_order_date', function ($item) {
                return !is_null($item->last_order_date) ? date('m/d/y', strtotime($item->last_order_date)) : '-';
            })
            ->editColumn('last_visit', function ($item) {
                return !is_null($item->last_visit) ? date('m/d/y', strtotime($item->last_visit)) : '-';
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
            'ec_customers.id',
            'ec_customers.name',
            'ec_customers.salesperson_id',
            'ec_customers.created_at',
            'ec_customers.status',
            'ec_customers.last_visit',
            'ec_customer_detail.company AS company'
        ];

        $query = $model->join('ec_customer_detail', 'ec_customer_detail.customer_id', '=', 'ec_customers.id')->select($select);

        $query = $query->selectRaw('(SELECT `ec_orders`.`created_at` FROM `ec_orders` WHERE ec_orders.`is_finished` = 1 AND `ec_orders`.`user_id` = ec_customers.id ORDER BY ec_orders.`id` DESC LIMIT 1) AS last_order_date');

        $query = $query->orderBy('ec_customers.last_visit')->orderBy('last_order_date');

        $search_items = $this->request()->all();
        if (!empty($search_items)) {
            if (isset($search_items['from_date'])) {
                $query->whereDate('ec_customers.created_at', '>=', Carbon::createFromFormat('m-d-Y', $search_items['from_date'])->format('Y-m-d'));
            }
            if (isset($search_items['to_date'])) {
                $query->whereDate('ec_customers.created_at', '<=', Carbon::createFromFormat('m-d-Y', $search_items['to_date'])->format('Y-m-d'));
            }
        }

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id' => [
                'name' => 'ec_customers.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-left',
            ],
            'salesperson_id' => [
                'name' => 'ec_customers.salesperson_id',
                'title' => 'Rep',
                'width' => '20px',
                'class' => 'text-left',
            ],
            'name' => [
                'name' => 'ec_customers.name',
                'title' => trans('core/base::forms.name'),
                'class' => 'text-left',
            ],
            'company' => [
                'name' => 'ec_customer_detail.company',
                'title' => 'Company',
                'class' => 'text-left',
            ],
            'status' => [
                'name' => 'status',
                'title' => 'Status',
                'class' => 'text-left',
                'searchable' => false
            ],
            'last_order_date' => [
                'name' => 'last_order_date',
                'title' => 'Last order',
                'class' => 'text-left',
                'searchable' => false
            ],
            'last_visit' => [
                'name' => 'last_visit',
                'title' => 'Last visit',
                'class' => 'text-left',
                'searchable' => false
            ],
            'created_at' => [
                'name' => 'ec_customers.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-left',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function renderTable($data = [], $mergeData = [])
    {
        if ($this->query()->count() === 0 && $this->request()->input('filter_table_id') !== $this->getOption('id')) {
            return view('plugins/ecommerce::customers.intro');
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
