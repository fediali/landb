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

        if (!Auth::user()->hasAnyPermission(['threadorders.edit', 'threadorders.destroy'])) {
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
            ->editColumn('ecommerce', function ($item) {
                if ($item->thread_order_has_pushed) {
                    $html = '<a href="javascript:void(0)" class="btn btn-sm btn-warning" disabled>Pushed</a>';
                } else {
                    $html = '<a href="javascript:void(0)" onclick="confirm_start('. '\''.route('threadorders.orderItem', $item->id). '\''.')" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="Order">Push</a><script>function confirm_start(url){
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

              return $html;
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return '<a href="'.route('threadorders.threadOrderDetail', $item->id).'" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="View"><i class="fa fa-eye"></i></a>';
                //return $this->getOperations('threadorders.edit', 'threadorders.destroy', $item);
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
            'threadorders.name',
            'threadorders.order_status',
            'threadorders.created_at',
            'threadorders.status',
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id' => [
                'name'  => 'threadorders.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name'  => 'threadorders.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'order_status' => [
                'name'  => 'threadorders.order_status',
                'title' => 'Order Status',
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'threadorders.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name'  => 'threadorders.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
            'ecommerce' => [
                'name'  => 'Ecommerce',
                'title' => 'Ecommerce',
                'width' => '100px',
            ],
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
        return []; // $this->addDeleteAction(route('threadorders.deletes'), 'threadorders.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'threadorders.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'threadorders.status' => [
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
