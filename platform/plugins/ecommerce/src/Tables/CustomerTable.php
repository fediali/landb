<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Discount;
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
            ->editColumn('email', function ($item) {
                return $item->email;
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('is_private', function ($item) {
                return $item->is_private ? 'Yes' : 'No';
            })->editColumn('is_text', function ($item) {
                if ($item->is_text == 1) {
                    return $html = '<a href="javascript:void(0)" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-original-title="Verify Number">Verified</a>';
                } else if ($item->is_text == 2) {
                    return $html = '<a href="javascript:void(0)" onclick="confirm_start(' . '\'' . route('customers.verify-phone', $item->id) . '\'' . ')" class="btn btn-icon btn-sm btn-danger" data-toggle="tooltip" data-original-title="' . $item->phone_validation_error . '">UnVerified</a><script>function confirm_start(url){
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
                    return $html = '<a href="javascript:void(0)" onclick="confirm_start(' . '\'' . route('customers.verify-phone', $item->id) . '\'' . ')" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="Verified Number">Verify</a><script>function confirm_start(url){
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
            })->editColumn('order_count', function ($item) {
                return $html = '<a  target="_blank" href="' . route('orders.index', ['user_id' => $item->id]) . '">' . $item->order_count . '</a>';

            });
        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                $html = '<button data-id="' . $item->id . '" class="merge-customer btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-original-title="Merge"><i class="fa fa-align-center"></i></button>';
                return $this->getOperations('customer.edit', 'customer.destroy', $item, $html);
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
            'ec_customers.email',
            'ec_customers.avatar',
            'ec_customers.is_private',
            'ec_customers.is_text',
            'ec_customers.created_at',
            'ec_customers.phone_validation_error',
        ];

        $query = $model->select($select);

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
        $query = $query->selectRaw('(SELECT COUNT(`ec_orders`.`id`) FROM `ec_orders` WHERE `ec_orders`.`user_id` = ec_customers.id AND DATE(ec_orders.created_at) >= "' . $from_date . '" AND DATE(ec_orders.created_at) <= "' . $to_date . '") AS order_count');

        //$query->selectRaw('SELECT COUNT(`ec_orders`.`id`) AS order_type FROM `ec_orders` WHERE `ec_orders`.`user_id` = ec_customers.id');


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
            $query->when(isset($search_items['company']), function($q) use($search_items) {
                $q->where('ec_customer_detail.company', 'LIKE', '%'.$search_items['company'].'%');
            });
            $query->when(isset($search_items['customer_name']), function($q) use($search_items) {
                $q->where('ec_customers.name', 'LIKE', '%'.$search_items['customer_name'].'%');
            });
            $query->when(isset($search_items['customer_email']), function($q) use($search_items) {
                $q->where('ec_customers.email', 'LIKE', '%'.$search_items['customer_email'].'%');
            });
            $query->when(isset($search_items['order_min_total']), function($q) use($search_items) {
                $q->where('ec_orders.sub_total', '>=', $search_items['order_min_total']);
            });
            $query->when(isset($search_items['order_max_total']), function($q) use($search_items) {
                $q->where('ec_orders.sub_total', '<=', $search_items['order_max_total']);
            });
            if (isset($search_items['order_from_date'])) {
                // $from_date = Carbon::createFromDate(strtotime($search_items['order_from_date']))->format('Y-m-d');
                $query->whereDate('ec_orders.created_at', '>=', $search_items['order_from_date']);
            }
            if (isset($search_items['order_to_date'])) {
                // $to_date = Carbon::createFromDate(strtotime($search_items['order_to_date']))->format('Y-m-d');
                $query->whereDate('ec_orders.created_at', '<=', $search_items['order_to_date']);
            }
            $query->when(isset($search_items['order_status']), function($q) use($search_items) {
                $q->where('ec_orders.status', $search_items['order_status']);
            });
            $query->when(isset($search_items['payment_method']), function($q) use($search_items) {
                $q->leftJoin('payments', 'payments.id', 'ec_orders.payment_id');
                $q->where('payments.payment_channel', $search_items['payment_method']);
            });
            $query->when(isset($search_items['online_order']), function($q) use($search_items) {
                $q->where('ec_orders.platform', $search_items['online_order']);
            });
            $query->when(isset($search_items['mobile_order']), function($q) use($search_items) {
                $q->where('ec_orders.platform', $search_items['mobile_order']);
            });
            $query->when(isset($search_items['coupon_code']), function($q) use($search_items) {
                $q->where('ec_orders.coupon_code', $search_items['coupon_code']);
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
            'id'          => [
                'name'  => 'ec_customers.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-left',
            ],
            'name'        => [
                'name'  => 'ec_customers.name',
                'title' => trans('core/base::forms.name'),
                'class' => 'text-left',
            ],
            'email'       => [
                'name'  => 'ec_customers.email',
                'title' => trans('plugins/ecommerce::customer.name'),
                'class' => 'text-left',
            ],
            'is_private'  => [
                'name'  => 'ec_customers.is_private',
                'title' => 'Is Private',
                'class' => 'text-left',
            ],
            'is_text'     => [
                'name'  => 'ec_customers.is_text',
                'title' => 'Text',
                'class' => 'text-left',
            ],
            'order_count' => [
                'name'       => 'order_count',
                'title'      => 'Order Count',
                'class'      => 'text-left',
                'searchable' => false
            ],
            'created_at'  => [
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

//        dd($buttons, $verify);
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Customer::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('customer.deletes'), 'customer.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            /*'ec_customers.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'ec_customers.email'      => [
                'title'    => trans('core/base::tables.email'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'ec_customers.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],*/
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
        $searches = UserSearch::where(['search_type' => 'orders', 'status' => 1])->pluck('name', 'id')->all();
        $data['order_statuses'] = Orderstatuses::where('status', BaseStatusEnum::PUBLISHED)->pluck('name')->all();
        $data['payment_methods'] = Paymentmethods::where('status', BaseStatusEnum::PUBLISHED)->pluck('name', 'slug')->all();
        $data['coupon_codes'] = Discount::pluck('title', 'code')->all();

        $report_types = [
            7   => 'Weekly',
            15  => 'Bi-Weekly',
            30  => 'Monthly',
            120 => 'Quarterly',
            180 => 'Six Month',
        ];
        return view($this->customFilterTemplate, compact('report_types','searches', 'data'))->render();
    }

}
