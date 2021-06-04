<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Ecommerce\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderIncompleteTable extends OrderTable
{

    /**
     * @var bool
     */
    protected $hasCheckbox = true;

    /**
     * @var bool
     */
    protected $hasActions = true;

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
            ->editColumn('status', function ($item) {
                // return $item->status->toHtml();
                return view('plugins/ecommerce::orders/orderStatus', ['item' => $item])->render();
            })
            ->editColumn('amount', function ($item) {
                return format_price($item->amount, $item->currency_id);
            })
            ->editColumn('user_id', function ($item) {
                return $item->user->name ?? $item->address->name;
            })
            ->editColumn('phone', function ($item) {
                return $item->user->phone ?? $item->user->phone;
            })
            ->editColumn('salesperson_id', function ($item) {
                return $item->salesperson? $item->salesperson->getFullName() : 'N/A';
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                $html = '';
                if (Auth::user()->hasPermission('orders.edit')) {
                    if (!in_array($item->status, [\Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED, \Botble\Ecommerce\Enums\OrderStatusEnum::COMPLETED])) {
                        $html .= '<a href="' . route('orders.editOrder', $item->id) . '" class="btn btn-icon btn-sm btn-warning" data-toggle="tooltip" data-original-title="Edit Order"><i class="fa fa-edit"></i></a>';
                    }
                    $html .= '<a href="'.route('orders.view-incomplete-order', $item->id).'" class="btn btn-icon btn-sm btn-primary" data-toggle="tooltip" data-original-title="View Order"><i class="fa fa-eye"></i></a>';
                }
                //orders.view-incomplete-order
                return $this->getOperations('', 'orders.destroy', $item, $html);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * {@inheritDoc}
     */
    protected function tableActions($item)
    {
        return $this->getOperations('orders.view-incomplete-order', null, $item);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'ec_orders.id',
            'ec_orders.user_id',
            'ec_orders.created_at',
            'ec_orders.amount',
            'ec_orders.currency_id',
            'ec_customers.phone',
            'ec_orders.salesperson_id',
            'ec_orders.status',
        ];

        $query = $model
            ->select($select)
            ->join('ec_customers', 'ec_customers.id', 'ec_orders.user_id')
            ->with(['user'])
            ->where('ec_orders.is_finished', 0);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
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
            return view('plugins/ecommerce::orders.incomplete-intro');
        }

        return parent::renderTable($data, $mergeData);
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'         => [
                'name'  => 'ec_orders.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-left',
            ],
            'user_id'    => [
                'name'  => 'ec_orders.user_id',
                'title' => trans('plugins/ecommerce::order.customer_label'),
                'class' => 'text-left',
            ],
            'phone'    => [
                'name'  => 'ec_customers.phone',
                'title' => 'Phone',
                'class' => 'text-left',
            ],
            'salesperson_id' => [
                'name'  => 'ec_orders.salesperson_id',
                'title' => 'Salesperson',
                'class' => 'text-left',
            ],
            'amount'     => [
                'name'  => 'ec_orders.amount',
                'title' => trans('plugins/ecommerce::order.amount'),
                'class' => 'text-center',
            ],
            'status'          => [
                'name'  => 'ec_orders.status',
                'title' => trans('core/base::tables.status'),
                'class' => 'text-center',
            ],
            'created_at' => [
                'name'  => 'ec_orders.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-left',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function htmlDrawCallbackFunction(): ?string
    {
        $return = parent::htmlDrawCallbackFunction();
        if (Order::where('ec_orders.is_finished', 0)->count()) {
            $return .= '$(".editable").editable();';
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('orders.deletes'), 'orders.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'ec_orders.salesperson_id'     => [
                'title'    => 'Salesperson',
                'type'     => 'select',
                'choices'  => get_salesperson(),
                'validate' => 'required',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        return [];
    }
}
