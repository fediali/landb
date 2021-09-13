<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\ACL\Models\Role;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Discount;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\UserSearch;
use Botble\Ecommerce\Models\UserSearchItem;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Orderstatuses\Models\Orderstatuses;
use Botble\Paymentmethods\Models\Paymentmethods;
use Botble\Table\Abstracts\TableAbstract;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Yajra\DataTables\DataTables;

class CustomerTable extends TableAbstract
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
     * @var string
     */
    protected $customFilterTemplate = 'plugins/ecommerce::customers.filter';


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

        if (!Auth::user()->hasAnyPermission(['customer.edit', 'customer.destroy'])) {
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
                if (!Auth::user()->hasPermission('customer.edit')) {
                    return $item->name;
                }
                return Html::link(route('customer.edit', $item->id), $item->name);
            })
            ->editColumn('salesperson_id', function ($item) {
                if ($item->salesperson_id) {
                    return isset($item->salesperson) ? $item->salesperson->username : 'N/A';
//                    return $item->salesperson->username;
                } else {
                    return 'N/A';
                }
            })
            ->editColumn('email', function ($item) {
                return $item->email;
            })
            ->editColumn('company', function ($item) {
                return $item->detail->company;
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('is_private', function ($item) {
                return $item->is_private ? 'Yes' : 'No';
            })
            ->editColumn('is_text', function ($item) {
                if ($item->is_text == 1) {
                    return $html = '<a href="javascript:void(0)" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-original-title="Verify Number">Verified Number</a>';
                } else if ($item->is_text == 2) {
                    return $html = '<a href="javascript:void(0)" onclick="confirm_start(' . '\'' . route('customers.verify-phone', $item->id) . '\'' . ')" class="btn btn-icon btn-sm btn-danger" data-toggle="tooltip" data-original-title="' . $item->phone_validation_error . '">UnVerified </a><script>function confirm_start(url){
                          swal({
                              title: \'Verify Customer Number?\',
                              text: "Do you want to verify customer phone number!",
                              icon: \'info\',
                              buttons:{
                                  cancel: {
                                    text: "Cancel",
                                    value: null,
                                    visible: true,
                                    className: "",
                                    closeModal: true,
                                  },
                                  confirm: {
                                    text: "Push",
                                    value: true,
                                    visible: true,
                                    className: "",
                                    closeModal: true
                                  }
                                }
                              }).then((result) => {
                                  if (result) {
                                      location.replace(url)
                                  }
                              });
                      }</script>';
                } else {
                    return $html = '<a href="javascript:void(0)" onclick="confirm_start(' . '\'' . route('customers.verify-phone', $item->id) . '\'' . ')" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="Verified Number">Verify Number</a><script>function confirm_start(url){
                          swal({
                              title: \'Verify Customer Number?\',
                              text: "Do you want to verify customer phone number!",
                              icon: \'info\',
                              buttons:{
                                  cancel: {
                                    text: "Cancel",
                                    value: null,
                                    visible: true,
                                    className: "",
                                    closeModal: true,
                                  },
                                  confirm: {
                                    text: "Push",
                                    value: true,
                                    visible: true,
                                    className: "",
                                    closeModal: true
                                  }
                                }
                              }).then((result) => {
                                  if (result) {
                                      location.replace(url)
                                  }
                              });
                      }</script>';
                }
            })
//            ->editColumn('order_count', function ($item) {
//                return $html = '<a  target="_blank" href="' . route('orders.index', ['user_id' => $item->id]) . '">' . $item->order_count . '</a>';
//            })
//            ->editColumn('order_spend', function ($item) {
//                return $item->order_spend;
//            })
//            ->editColumn('abandon_products', function ($item) {
//                return '<a href="' . route('orders.incomplete-list', ['order_id' => $item->abandon_order_id]) . '">' . $item->abandon_products . '</a>';
//            })
            ->editColumn('status', function ($item) {
//                $html = '<span class="badge badge-default">' . $item->status . '</span>';
//                if ($item->status == BaseStatusEnum::$CUSTOMERS['Active']) {
//                    $html = '<span class="badge badge-success">' . BaseStatusEnum::$CUSTOMERS['Active'] . '</span>';
//                } elseif ($item->status == BaseStatusEnum::$CUSTOMERS['Disabled']) {
//                    $html = '<span class="badge badge-warning">' . BaseStatusEnum::$CUSTOMERS['Disabled'] . '</span>';
//                } elseif ($item->status == BaseStatusEnum::$CUSTOMERS['declined']) {
//                    $html = '<span class="badge badge-danger">' . BaseStatusEnum::$CUSTOMERS['declined'] . '</span>';
//                }
//                return $html;

                return view('plugins/ecommerce::customers/customerStatus', ['item' => $item])->render();
            })
//            ->editColumn('last_order_date', function ($item) {
//                return !is_null($item->last_order_date) ? date('m/d/y', strtotime($item->last_order_date)) : '-';
//            })
            ->editColumn('last_visit', function ($item) {
                return !is_null($item->last_visit) ? date('m/d/y', strtotime($item->last_visit)) : '-';
            });
        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                $html = '<button data-id="' . $item->id . '" class="merge-customer btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-original-title="Merge"><i class="fa fa-align-center"></i></button>';
                return $this->getOperations('customer.edit', '', $item, $html);
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
            'ec_customers.id',
            'ec_customers.name',
            'ec_customers.salesperson_id',
            'ec_customers.email',
            'ec_customers.avatar',
            'ec_customers.is_private',
            'ec_customers.is_text',
            'ec_customers.created_at',
            'ec_customers.status',
            'ec_customers.last_visit',
            'ec_customers.phone_validation_error',
            'ec_customers.phone',
            'ec_customer_detail.business_phone AS business_phone',
            'ec_customer_detail.company AS company'
        ];

        $query = $model->join('ec_customer_detail', 'ec_customer_detail.customer_id', '=', 'ec_customers.id')->select($select);
        $from_date = Carbon::now()->format('Y-m-d');
        $to_date = Carbon::now()->format('Y-m-d');
        $request = request();
        if ($request->has('report_type')) {
            $report_type = (int)$request->input('report_type');
            if ($report_type) {
                $from_date = Carbon::now()->subDays($report_type)->format('Y-m-d');
                $to_date = Carbon::now()->format('Y-m-d');
            }
        }

        //$query = $query->selectRaw('(SELECT COUNT(`ec_orders`.`id`) FROM `ec_orders` WHERE `ec_orders`.`user_id` = ec_customers.id AND DATE(ec_orders.created_at) >= "' . $from_date . '" AND DATE(ec_orders.created_at) <= "' . $to_date . '") AS order_count');

//
        //$query = $query->selectRaw('(SELECT SUM(`ec_orders`.`amount`) FROM `ec_orders` WHERE `ec_orders`.`user_id` = ec_customers.id) AS order_spend');
//
       // $query = $query->selectRaw('(SELECT COUNT(`ec_order_product`.`product_id`) FROM `ec_orders` JOIN `ec_order_product` ON ec_orders.`id` = ec_order_product.`order_id` WHERE ec_orders.`is_finished` = 0 AND `ec_orders`.`user_id` = ec_customers.id) AS abandon_products');
       // $query = $query->selectRaw('(SELECT `ec_orders`.`id` FROM `ec_orders` WHERE ec_orders.`is_finished` = 0 AND `ec_orders`.`user_id` = ec_customers.id ORDER BY ec_orders.`id` DESC LIMIT 1) AS abandon_order_id');
//
       // $query = $query->selectRaw('(SELECT `ec_orders`.`created_at` FROM `ec_orders` WHERE ec_orders.`is_finished` = 1 AND `ec_orders`.`user_id` = ec_customers.id ORDER BY ec_orders.`id` DESC LIMIT 1) AS last_order_date');


        // $query->selectRaw('SELECT COUNT(`ec_orders`.`id`) AS order_type FROM `ec_orders` WHERE `ec_orders`.`user_id` = ec_customers.id');


        if ($this->request()->has('search_id')) {
            $search_id = (int)$this->request()->input('search_id');
            if ($search_id) {
                $search_items = UserSearchItem::where('user_search_id', $search_id)->pluck('value', 'key')->all();
            }
        }

        if (empty($search_items)) {
            $search_items = $this->request()->all();
        }

        if (!isset($search_items['report_type'])) {
            $query = $query->selectRaw('(SELECT COUNT(`ec_orders`.`id`) FROM `ec_orders` WHERE `ec_orders`.`user_id` = ec_customers.id) AS order_count');
        }

        if (!empty($search_items)) {
            $query->when(isset($search_items['company']), function ($q) use ($search_items) {
//                $q->join('ec_customer_detail', 'ec_customer_detail.customer_id', 'ec_customers.id');
                $q->where('ec_customer_detail.company', 'LIKE', '%' . $search_items['company'] . '%');
            });
            $query->when(isset($search_items['customer_name']), function ($q) use ($search_items) {
                $q->where('ec_customers.name', 'LIKE', '%' . $search_items['customer_name'] . '%');
            });
            $query->when(isset($search_items['customer_email']), function ($q) use ($search_items) {
                $q->where('ec_customers.email', 'LIKE', '%' . $search_items['customer_email'] . '%');
            });
            $query->when(isset($search_items['manager']), function ($q) use ($search_items) {
                $q->where('ec_customers.salesperson_id', $search_items['manager']);
            });
            $query->when(isset($search_items['status']), function ($q) use ($search_items) {
                $q->where('ec_customers.status', $search_items['status']);
            });
            $query->when(isset($search_items['last_order']), function ($q) use ($search_items) {
                $q->join('ec_orders', 'ec_orders.user_id', 'ec_customers.id');
                $q->whereDate('ec_orders.created_at', '>=', date('Y-m-d', strtotime($search_items['last_order'])));
            });
            $query->when(isset($search_items['last_visit']), function ($q) use ($search_items) {
                $q->whereDate('ec_customers.last_visit', '>=', date('Y-m-d', strtotime($search_items['last_visit'])));
            });
            $query->when(isset($search_items['spend']), function ($q) use ($search_items) {
                $q->join('ec_orders', 'ec_orders.user_id', 'ec_customers.id');
                $q->where('ec_orders.amount', '>=', $search_items['spend']);
            });
            $query->when(isset($search_items['no_sales_rep']), function ($q) use ($search_items) {
                $q->where('ec_customers.salesperson_id', 0);
            });
            $query->when(isset($search_items['merged_account']), function ($q) use ($search_items) {
                $q->join('ec_customers_merge', 'ec_customers_merge.user_id_one', 'ec_customers.id');
            });
            $query->when(isset($search_items['report_type']), function ($q) use ($search_items) {
                $from_date = Carbon::now()->subDays($search_items['report_type'])->format('Y-m-d');
                $to_date = Carbon::now()->format('Y-m-d');
                $q->selectRaw('(SELECT COUNT(`ec_orders`.`id`) FROM `ec_orders` WHERE `ec_orders`.`user_id` = ec_customers.id AND DATE(ec_orders.created_at) >= "' . $from_date . '" AND DATE(ec_orders.created_at) <= "' . $to_date . '") AS order_count');
            });

        }
        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'               => [
                'name'  => 'ec_customers.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-left',
            ],
            'business_phone'   => [
                'name'    => 'ec_customer_detail.business_phone',
                'title'   => 'Business Phone',
                'width'   => '20px',
                'visible' => false,
            ],
            'salesperson_id'   => [
                'name'  => 'ec_customers.salesperson_id',
                'title' => 'Rep',
                'width' => '20px',
                'class' => 'text-left',
            ],
            'name'             => [
                'name'  => 'ec_customers.name',
                'title' => trans('core/base::forms.name'),
                'class' => 'text-left',
            ],
            'company'            => [
                'name'  => 'ec_customer_detail.company',
                'title' => 'Company',
                'class' => 'text-left',
            ], 'email'            => [
                'name'  => 'ec_customers.email',
                'title' => trans('plugins/ecommerce::customer.name'),
                'class' => 'text-left',
                'visible' => false,
            ],
            'is_private'       => [
                'name'  => 'ec_customers.is_private',
                'title' => 'Is Private',
                'class' => 'text-left',
                'visible' => false,
            ],
            'is_text'          => [
                'name'  => 'ec_customers.is_text',
                'title' => 'Text',
                'class' => 'text-left',
                'width' => '100px',
            ],
//            'order_count'      => [
//                'name'       => 'order_count',
//                'title'      => 'Order Count',
//                'class'      => 'text-left',
//                'searchable' => false
//            ],
//            'order_spend'      => [
//                'name'       => 'order_spend',
//                'title'      => 'Spend',
//                'class'      => 'text-left',
//                'searchable' => false
//            ],
//            'abandon_products' => [
//                'name'       => 'abandon_products',
//                'title'      => 'Abandoned',
//                'class'      => 'text-left',
//                'searchable' => false
//            ],
            'status'           => [
                'name'       => 'status',
                'title'      => 'Validation',
                'class'      => 'text-left',
                'searchable' => false
            ],
//            'last_order_date'  => [
//                'name'       => 'last_order_date',
//                'title'      => 'Last order',
//                'class'      => 'text-left',
//                'searchable' => false
//            ],
            'last_visit'       => [
                'name'       => 'last_visit',
                'title'      => 'Last visit',
                'class'      => 'text-left',
                'searchable' => false
            ],
            'created_at'       => [
                'name'  => 'ec_customers.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-left',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('customer.create'), 'customer.create');

        $buttons['verify'] = [
            'link' => route('customers.verify-phone-bulk', [Auth::id()]),
            'text' => '<i class="fa fa-check"></i> Verify All Customer'
        ];

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Customer::class);
    }

    public function htmlDrawCallbackFunction(): ?string
    {
        $return = parent::htmlDrawCallbackFunction();
        if (Customer::all()->count()) {
            $return .= '$(".editable").editable();';
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
//        return $this->addDeleteAction(route('customer.deletes'), 'customer.destroy', parent::bulkActions());
        return parent::bulkActions();
    }


    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
//            'ec_customers.name'       => [
//                'title'    => trans('core/base::tables.name'),
//                'type'     => 'text',
//                'validate' => 'required|max:120',
//            ],
//            'ec_customers.email'      => [
//                'title'    => trans('core/base::tables.email'),
//                'type'     => 'text',
//                'validate' => 'required|max:120',
//            ],
//            'ec_customers.created_at' => [
//                'title' => trans('core/base::tables.created_at'),
//                'type'  => 'date',
//            ],
            'ec_customers.salesperson_id' => [
                'title'   => 'Sales Rep',
                'type'    => 'select',
                'choices' => get_salesperson(),
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function renderTable($data = [], $mergeData = [])
    {
        if ($this->query()->count() === 0 &&
            $this->request()->input('filter_table_id') !== $this->getOption('id')
        ) {
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
        $searches = UserSearch::where(['search_type' => 'customers', 'status' => 1])->where('user_id', $user)->pluck('name', 'id')->all();

        $report_types = [
            7   => 'Weekly',
            15  => 'Bi-Weekly',
            30  => 'Monthly',
            120 => 'Quarterly',
            180 => 'Six Month',
        ];

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

        return view($this->customFilterTemplate, compact('report_types', 'searches', 'data'))->render();
    }

}
