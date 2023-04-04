<?php

namespace Botble\Ecommerce\Tables\Reports;

use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Carbon;
use Throwable;
use Yajra\DataTables\DataTables;
use BaseHelper;


class PreOrdersTable extends TableAbstract
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
    protected $customFilterTemplate = 'plugins/ecommerce::orders.preOrderFilter';

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
            ->editColumn('id', function ($item) {
                $html = '<span data-toggle="tooltip">' . $item->id . '</span>' . (($item->platform == "online") ? ' <i class="badge bg-success ml-1">online</i>' : '');
                return $html;
            })
            ->editColumn('company', function ($item) {
                return $item->company;
            })
            ->editColumn('salesperson_id', function ($item) {
                return $item->salesperson ? $item->salesperson->getFullName() : 'N/A';
            })
            ->editColumn('payment_status', function ($item) {
                return $item->payment->status->label() ? $item->payment->status->toHtml() : '&mdash;';
            })
            ->editColumn('payment_method', function ($item) {
                return $item->payment->payment_channel->label() ? $item->payment->payment_channel->label() : '&mdash;';
            })
            /*->editColumn('order_type', function ($item) {
                return $item->order_type_html;
            })*/
            ->editColumn('amount', function ($item) {
                return format_price($item->amount, $item->currency_id);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
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
            'ec_orders.status',
            //'ec_orders.order_type',
            'ec_orders.user_id',
            'ec_customer_detail.company',
            'ec_orders.created_at',
            'ec_orders.amount',
            'ec_orders.payment_id',
            'ec_orders.salesperson_id',
        ];

        $query = $model
            ->select($select)
            //->join('ec_order_product', 'ec_orders.id', 'ec_order_product.order_id')
            ->join('ec_customers', 'ec_customers.id', 'ec_orders.user_id')
            ->leftJoin('ec_customer_detail', 'ec_customer_detail.customer_id', 'ec_customers.id')
            ->with(['user', 'payment'])
            ->where('ec_orders.is_finished', 1)
            ->where('ec_orders.order_type', Order::PRE_ORDER)
            ->whereNotIn('ec_orders.status', [OrderStatusEnum::CANCELED, OrderStatusEnum::PENDING])
            ->groupBy('ec_orders.id');

        $query = $query->selectRaw('(SELECT SUM(`ec_order_product`.`qty`) FROM `ec_order_product` WHERE `ec_order_product`.`order_id` = ec_orders.id) AS pre_order_qty');

        $search_items = $this->request()->all();
        if (!empty($search_items)) {
            if (isset($search_items['from_date'])) {
                $query->whereDate('ec_orders.created_at', '>=', Carbon::createFromFormat('m-d-Y', $search_items['from_date'])->format('Y-m-d'));
            }
            if (isset($search_items['to_date'])) {
                $query->whereDate('ec_orders.created_at', '<=', Carbon::createFromFormat('m-d-Y', $search_items['to_date'])->format('Y-m-d'));
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
            'id'             => [
                'name'  => 'ec_orders.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-left',
            ],
            'company'        => [
                'name'  => 'ec_customer_detail.company',
                'title' => 'Company',
                'class' => 'text-left',
            ],
            'salesperson_id' => [
                'name'  => 'ec_orders.salesperson_id',
                'title' => 'Salesperson',
                'class' => 'text-left',
            ],
            'pre_order_qty' => [
                'name'  => 'ec_orders.pre_order_qty',
                'title' => 'Qty',
                'class' => 'text-left',
            ],
            'payment_method'  => [
                'name'  => 'ec_orders.id',
                'title' => trans('plugins/ecommerce::order.payment_method'),
                'class' => 'text-center',
            ],
            'payment_status'  => [
                'name'  => 'ec_orders.id',
                'title' => trans('plugins/ecommerce::order.payment_status_label'),
                'class' => 'text-center',
            ],
            /*'order_type'      => [
                'name'  => 'ec_orders.order_type',
                'title' => 'Order Type',
                'class' => 'text-center',
            ],*/
            'amount'         => [
                'name'  => 'ec_orders.amount',
                'title' => trans('plugins/ecommerce::order.amount'),
                'class' => 'text-center',
            ],
            'status'          => [
                'name'  => 'ec_orders.status',
                'title' => trans('core/base::tables.status'),
                'class' => 'text-center',
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
        $search_items = $this->request()->all();
        $data['search_items'] = $search_items;

        return view($this->customFilterTemplate, compact('data'))->render();
    }

}
