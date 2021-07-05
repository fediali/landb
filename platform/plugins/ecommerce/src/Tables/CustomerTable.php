<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
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
        //$query = $query->orderBy('order_count', 'DESC');
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
        $report_types = [
            7   => 'Weekly',
            15  => 'Bi-Weekly',
            30  => 'Monthly',
            120 => 'Quarterly',
            180 => 'Six Month',
        ];
        return view($this->customFilterTemplate, compact('report_types'))->render();
    }
}
