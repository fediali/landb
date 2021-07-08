<?php

namespace Botble\Threadorders\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Threadorders\Repositories\Interfaces\ThreadordersInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Threadorders\Models\Threadorders;
use Html;

class ThreadordersTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    /**
     * ThreadordersTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ThreadordersInterface $threadordersRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ThreadordersInterface $threadordersRepository)
    {
        $this->repository = $threadordersRepository;
        $this->setOption('id', 'plugins-threadorders-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['threadorders.edit', 'threadorders.destroy', 'threadorders.status', 'threadorders.pushEcommerce', 'threadorders.details', 'threadorders.order'])) {
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
                return $item->name;
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                // return $item->status->toHtml();
                return view('plugins/threadorders::threadOrderStatus', ['item' => $item])->render();
            })
            ->editColumn('sku', function ($item) {
                return @$item->thread->regular_product_categories[0]->pivot->sku;
            })
            ->editColumn('ecommerce', function ($item) {
                if ($item->status == 'completed') {
                    if ($item->thread_order_has_pushed) {
                        $html = '<a href="javascript:void(0)" class="btn btn-sm btn-warning" disabled>Pushed</a>';
                    } else {
                        $html = '<a href="javascript:void(0)" onclick="confirm_start(' . '\'' . route('threadorders.orderItem', $item->id) . '\'' . ')" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="Order">Push</a><script>function confirm_start(url){
                          swal({
                              title: \'Are you sure?\',
                              text: "Do you want to push this Order to Ecommerce!",
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
                } else {
                    $html = 'N/A';
                }
                return $html;
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                $html = '<a href="' . route('threadorders.threadOrderDetail', $item->id) . '" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="View"><i class="fa fa-eye"></i></a>';
                return $this->getOperations('', 'threadorders.destroy', $item, $html);
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
            'threadorders.id',
            'threadorders.thread_id',
            'threadorders.name',
            'threadorders.order_status',
            'threadorders.created_at',
            'threadorders.status',
            'categories_threads.sku',
        ];

        $query = $model->select($select)
            ->join('threads', 'threadorders.thread_id', 'threads.id')->join('categories_threads', 'threads.id', 'categories_threads.thread_id')
            ->groupBy('threadorders.id');

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'thread_id'    => [
                'name'  => 'threadorders.thread_id',
                'title' => 'Thread ID',
                'width' => '20px',
            ],
            'sku'          => [
                'name'  => 'categories_threads.sku',
                'title' => 'SKU',
                'class' => 'text-left',
            ],
            'name'         => [
                'name'  => 'threadorders.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'order_status' => [
                'name'  => 'threadorders.order_status',
                'title' => 'Order Status',
                'class' => 'text-left',
            ],
            'created_at'   => [
                'name'  => 'threadorders.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status'       => [
                'name'  => 'threadorders.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
            'ecommerce'    => [
                'name'    => 'threadorders.status',
                'title'   => 'Ecommerce',
                'width'   => '100px',
                'visible' => (Auth::user()->hasPermission('threadorders.pushEcommerce')) ? true : false,
            ]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = []; //$this->addCreateButton(route('threadorders.create'), 'threadorders.create');
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Threadorders::class);
    }

    /**
     * {@inheritDoc}
     */
    public function htmlDrawCallbackFunction(): ?string
    {
        $return = parent::htmlDrawCallbackFunction();
        if (Threadorders::all()->count()) {
            $return .= '$(".editable").editable();';
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return [];
        // return $this->addDeleteAction(route('threadorders.deletes'), 'threadorders.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'threadorders.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'threadorders.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'threadorders.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->getBulkChanges();
    }
}
