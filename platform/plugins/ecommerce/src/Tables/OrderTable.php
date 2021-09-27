<?php

namespace Botble\Ecommerce\Tables;

use App\Models\MergeAccount;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Models\Discount;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\UserSearch;
use Botble\Ecommerce\Models\UserSearchItem;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Orderstatuses\Models\Orderstatuses;
use Botble\Paymentmethods\Models\Paymentmethods;
use Botble\Table\Abstracts\TableAbstract;
use EcommerceHelper;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Yajra\DataTables\DataTables;
use Html;

class OrderTable extends TableAbstract
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
     * @var bool
     */
    public $hasCustomFilter = true;

    /**
     * @var bool
     */
    public $hasCustomBottom = true;

    /**
     * @var string
     */
    protected $customFilterTemplate = 'plugins/ecommerce::orders.filter';

    /**
     * @var string
     */
    protected $customBottomTemplate = 'plugins/ecommerce::orders.bottom';

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
        if (!Auth::user()->hasPermission('orders.edit')) {
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
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })->editColumn('id', function ($item) {
                $html = '<a href="' . route('orders.edit', $item->id) . '" data-toggle="tooltip">' . $item->id . '</a>' . (($item->platform == "online") ? ' <i class="badge bg-success ml-1">online</i>' : '');
                if ($item->salesperson) {
                    $html .= ' <span class="text-success">(' . $item->salesperson->first_name . ')</span>';
                }

                return $html;

            })->editColumn('user_id', function ($item) {
                // return $item->user->name ?? $item->address->name;
//                return Html::link(route('customer.edit', $item->user_id), $item->user->name);

//                return $html = '<div class="d-flex"><a href="' . route('customer.edit', $item->user_id) . '" data-toggle="tooltip">' . $item->user->name . '</a>' . (($item->salesperson) ? ' <i class="badge bg-success ml-1">'.$item->salesperson->getFullName().'</i>' : '</div>');


                    $customer = '<a href="' . route('customer.edit', $item->user_id) . '" data-toggle="tooltip">' . $item->user->detail->company . '</a>';

                    return $customer;


            })
            ->editColumn('order_type', function ($item) {
                return $item->order_type_html;
            })
            ->editColumn('status', function ($item) {
                // return $item->status->toHtml();
                return view('plugins/ecommerce::orders/orderStatus', ['item' => $item])->render();
            })
            ->editColumn('payment_status', function ($item) {
                return $item->payment->status->label() ? $item->payment->status->toHtml() : '&mdash;';
            })
            ->editColumn('payment_method', function ($item) {
                return $item->payment->payment_channel->label() ? $item->payment->payment_channel->label() : '&mdash;';
            })
            ->editColumn('amount', function ($item) {
                return format_price($item->amount, $item->currency_id);
            })
            ->editColumn('shipping_amount', function ($item) {
                return format_price($item->shipping_amount, $item->currency_id);
            })

            ->editColumn('salesperson_id', function ($item) {
                return $item->salesperson ? $item->salesperson->getFullName() : 'N/A';
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            });

        if (EcommerceHelper::isTaxEnabled()) {
            $data = $data->editColumn('tax_amount', function ($item) {
                return format_price($item->tax_amount, $item->currency_id);
            });
        }

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                $html = '';
                if (Auth::user()->hasPermission('orders.edit')) {
                    if (!in_array($item->status, [\Botble\Ecommerce\Enums\OrderStatusEnum::CANCELED, \Botble\Ecommerce\Enums\OrderStatusEnum::COMPLETED, 'shipping complete' ])) {
                        $html .= '<a href="' . route('orders.editOrder', $item->id) . '" class="btn btn-icon btn-sm btn-warning" data-toggle="tooltip" data-original-title="Edit Order"><i class="fa fa-edit"></i></a>';
                    }
                    $html .= '<a href="' . route('orders.edit', $item->id) . '" class="btn btn-icon btn-sm btn-primary" data-toggle="tooltip" data-original-title="View Order"><i class="fa fa-eye"></i></a>';
                }
                //orders.edit
                return $this->getOperations('', '', $item, $html);
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
            'ec_orders.id',
            'ec_orders.status',
            'ec_orders.order_type',
            'ec_orders.user_id',
            'ec_orders.created_at',
            'ec_orders.amount',
            'ec_orders.platform',
            'ec_orders.tax_amount',
            'ec_orders.currency_id',
            'ec_orders.shipping_amount',
            'ec_orders.payment_id',
            'ec_orders.salesperson_id',
            'ec_orders.order_type',
        ];

        $query = $model
            ->select($select)
            ->join('ec_customers', 'ec_customers.id', 'ec_orders.user_id')
            ->with(['user', 'payment'])
            ->where('ec_orders.is_finished', 1);

        $order_type = $this->request()->input('order_type', false);
        $product_id = $this->request()->input('product_id', false);
        $order_id = $this->request()->input('order_id', false);
        $user_id = $this->request()->input('user_id', false);

        if ($user_id) {
            $merge = MergeAccount::where('user_id_one', $user_id)->pluck('user_id_two');
            if (!$merge->isEmpty()) {
                $query->whereIn('ec_orders.user_id', $merge)->orWhere('ec_orders.user_id', $user_id);
            } else {
                $query->where('ec_orders.user_id', $user_id);
            }
        }
        if ($order_type && in_array($order_type, [Order::NORMAL, Order::PRE_ORDER])) {
            $query->where('ec_orders.order_type', $order_type);
        }
        if ($product_id) {
            $getProdIds = ProductVariation::where('configurable_product_id', $product_id)->pluck('product_id')->all();
            $getProdIds[] = $product_id;
            $query->join('ec_order_product', 'ec_order_product.order_id', 'ec_orders.id')
                ->whereIn('ec_order_product.product_id', $getProdIds)
                ->whereNotIn('ec_orders.status', [OrderStatusEnum::CANCELED, OrderStatusEnum::PENDING]);
        }
        if($order_id){
          $query->where('ec_orders.id', $order_id);
        }
        if ($this->request()->has('search_id')) {
            $search_id = (int)$this->request()->input('search_id');
            if ($search_id) {
                $search_items = UserSearchItem::where('user_search_id', $search_id)->pluck('value', 'key')->all();
            }
        }

        if (empty($search_items)) {
            $search_items = $this->request()->all();
        }

        if (!empty($search_items)) {
            $query->when(isset($search_items['company']), function ($q) use ($search_items) {
                $q->leftJoin('ec_customer_detail', 'ec_customer_detail.customer_id', 'ec_customers.id');
                $q->where('ec_customer_detail.company', 'LIKE', '%' . $search_items['company'] . '%');
            });
            $query->when(isset($search_items['customer_name']), function ($q) use ($search_items) {
                $q->where('ec_customers.name', 'LIKE', '%' . $search_items['customer_name'] . '%');
            });
            $query->when(isset($search_items['customer_email']), function ($q) use ($search_items) {
                $q->where('ec_customers.email', 'LIKE', '%' . $search_items['customer_email'] . '%');
            });
            $query->when(isset($search_items['order_min_total']), function ($q) use ($search_items) {
                $q->where('ec_orders.sub_total', '>=', $search_items['order_min_total']);
            });
            $query->when(isset($search_items['order_max_total']), function ($q) use ($search_items) {
                $q->where('ec_orders.sub_total', '<=', $search_items['order_max_total']);
            });
            if (isset($search_items['order_from_date'])) {
                // $from_date = Carbon::createFromDate(strtotime($search_items['order_from_date']))->format('Y-m-d');
                $query->whereDate('ec_orders.created_at', '>=', Carbon::createFromFormat('m-d-Y', $search_items['order_from_date'])->format('Y-m-d'));
            }
            if (isset($search_items['order_to_date'])) {
                // $to_date = Carbon::createFromDate(strtotime($search_items['order_to_date']))->format('Y-m-d');
                $query->whereDate('ec_orders.created_at', '<=', Carbon::createFromFormat('m-d-Y', $search_items['order_to_date'])->format('Y-m-d'));
            }
            $query->when(isset($search_items['order_status']), function ($q) use ($search_items) {
                if(is_array($search_items['order_status'])){
                    $q->whereIn('ec_orders.status',  $search_items['order_status']);
                }
                else{
                    $q->whereIn('ec_orders.status', explode(',', $search_items['order_status']));
                }
//                $q->whereIn('ec_orders.status', $search_items['order_status']);
            });
            $query->when(isset($search_items['order_type']), function ($q) use ($search_items) {
                $q->where('ec_orders.order_type', $search_items['order_type']);
            });
            $query->when(isset($search_items['payment_method']), function ($q) use ($search_items) {
                $q->leftJoin('payments', 'payments.id', 'ec_orders.payment_id');
                if(is_array($search_items['payment_method'])){
                    $q->whereIn('payments.payment_channel',  $search_items['payment_method']);
                }
                else{
                    $q->whereIn('payments.payment_channel', explode(',', $search_items['payment_method']));
                }
                //$q->where('payments.payment_channel', $search_items['payment_method']);
            });
            $query->when(isset($search_items['online_order']), function ($q) use ($search_items) {
                $q->where('ec_orders.platform', $search_items['online_order']);
            });
            $query->when(isset($search_items['mobile_order']), function ($q) use ($search_items) {
                $q->where('ec_orders.platform', $search_items['mobile_order']);
            });
            $query->when(isset($search_items['coupon_code']), function ($q) use ($search_items) {
                $q->where('ec_orders.coupon_code', $search_items['coupon_code']);
            });
            $query->when(isset($search_items['manager']), function ($q) use ($search_items) {
                $q->where('ec_orders.salesperson_id', $search_items['manager']);
            });
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

//            'salesperson_id' => [
//                'name'  => 'ec_orders.salesperson_id',
//                'title' => 'Salesperson',
//                'class' => 'text-left',
//            ],

        ];

//        if (EcommerceHelper::isTaxEnabled()) {
//            $columns['tax_amount'] = [
//                'name'  => 'ec_orders.amount',
//                'title' => trans('plugins/ecommerce::order.tax_amount'),
//                'class' => 'text-center',
//            ];
//        }

        $columns += [
//            'shipping_amount' => [
//                'name'  => 'ec_orders.shipping_amount',
//                'title' => trans('plugins/ecommerce::order.shipping_amount'),
//                'class' => 'text-center',
//            ],

            'user_id'        => [
                'name'  => 'ec_orders.user_id',
                'title' => 'Company',
                'class' => 'text-left',
            ],

            'payment_method'  => [
                'name'  => 'ec_orders.id',
                'title' => trans('plugins/ecommerce::order.payment_method'),
                'class' => 'text-center',
            ],



//            'payment_status'  => [
//                'name'  => 'ec_orders.id',
//                'title' => trans('plugins/ecommerce::order.payment_status_label'),
//                'class' => 'text-center',
//            ],

            'order_type'      => [
                'name'  => 'ec_orders.order_type',
                'title' => 'Order Type',
                'class' => 'text-center',
            ],

            'amount'         => [
                'name'  => 'ec_orders.amount',
                'title' => trans('plugins/ecommerce::order.amount'),
                'class' => 'text-center',
            ],
        ];

        return $columns;
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('orders.create'), 'orders.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Order::class);
    }

    /**
     * {@inheritDoc}
     */
    public function htmlDrawCallbackFunction(): ?string
    {
        $return = parent::htmlDrawCallbackFunction();
        if (Order::all()->count()) {
            $return .= '$(".editable").editable();';
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
//        return $this->addDeleteAction(route('orders.deletes'), 'orders.destroy', parent::bulkActions());
        return parent::bulkActions();
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'ec_orders.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => get_order_statuses(1),
                //'validate' => 'required|in:' . json_encode(get_order_statuses()),
            ],
//            'ec_orders.created_at' => [
//                'title' => trans('core/base::tables.created_at'),
//                'type'  => 'date',
//            ],
            'ec_orders.id' => [
                'title' => 'Print',
                'type' => 'select',
                'choices'  => ['print_now' => 'Print Now!'],
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
            'export',
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
        $user = Auth::id();
        $searches = UserSearch::where(['search_type' => 'orders', 'status' => 1])->where('user_id', $user)->pluck('name', 'id')->all();
        $data['order_statuses'] = Orderstatuses::where('status', BaseStatusEnum::PUBLISHED)->pluck('name')->all();
        $data['payment_methods'] = Paymentmethods::where('status', BaseStatusEnum::PUBLISHED)->pluck('name', 'slug')->all();
        $data['coupon_codes'] = Discount::pluck('title', 'code')->all();

        if ($this->request()->has('search_id')) {
            $search_id = (int)$this->request()->input('search_id');
            if ($search_id) {
                $data['search_name'] = UserSearch::where('id', $search_id)->value('name');
                $search_items = UserSearchItem::where('user_search_id', $search_id)->pluck('value', 'key')->all();
            }
        }

        if (empty($search_items)) {
            $search_items = $this->request()->all();
        }

        $data['search_items'] = $search_items;

        return view($this->customFilterTemplate, compact('searches', 'data'))->render();
    }

    /**
     * @return bool
     */
    public function isHasCustomBottom(): bool
    {
        return $this->hasCustomBottom;
    }

    /**
     * @return string
     * @throws Throwable
     */
    public function renderCustomBottom(): string
    {
        $data = ['gross_total' => 0, 'total_paid' => 0, 'total_shipping_cost' => 0];

        $search_items = $this->request()->all();
        $from_date = false;
        if (isset($search_items['order_from_date'])) {
            $from_date = Carbon::createFromFormat('m-d-Y', $search_items['order_from_date'])->format('Y-m-d');
        }
        $to_date = false;
        if (isset($search_items['order_to_date'])) {
            $to_date = Carbon::createFromFormat('m-d-Y', $search_items['order_to_date'])->format('Y-m-d');
        }

        $paid_statuses[] = 'paid in full'; //Paid in Full
        $paid_statuses[] = 'shipping complete'; //Shipping Complete
        $paid_statuses[] = 'exchange'; //Exchange
        $paid_statuses[] = 'store credit'; //Store Credit
        $paid_statuses[] = 'in store complete'; //In Store Complete
        $paid_statuses[] = 'refund'; //REFUND
        $paid_statuses[] = 'fashiongo complete'; //FashionGo Complete
        $paid_statuses[] = 'lashowroom complete'; //LAShowroom Complete
        $paid_statuses[] = 'orangeshine complete'; //ORANGESHINE COMPLETE
        $paid_statuses[] = 'model purchase'; //Model Purchase
        $paid_statuses[] = 'employee uniform'; //Employee Uniform
        $paid_statuses[] = 'fashiondomino complete'; //FashionDomino Complete

        $data['gross_total'] = Order::when($from_date && $to_date, function($q) use($from_date, $to_date) {
            $q->whereDate('ec_orders.created_at', '>=', $from_date);
            $q->whereDate('ec_orders.created_at', '<=', $to_date);
        })->where('ec_orders.is_finished', 1)->sum('amount');
        $data['total_paid'] = Order::whereNotNull('order_completion_date')->whereIn('status', $paid_statuses)
            ->when($from_date && $to_date, function($q) use($from_date, $to_date) {
                $q->whereDate('ec_orders.created_at', '>=', $from_date);
                $q->whereDate('ec_orders.created_at', '<=', $to_date);
            })
            ->where('ec_orders.is_finished', 1)->sum('sub_total');
        $data['total_shipping_cost'] = Order::when($from_date && $to_date, function($q) use($from_date, $to_date) {
            $q->whereDate('ec_orders.created_at', '>=', $from_date);
            $q->whereDate('ec_orders.created_at', '<=', $to_date);
        })->where('ec_orders.is_finished', 1)->sum('shipping_amount');

        return view($this->customBottomTemplate, compact('data'))->render();
    }
}
