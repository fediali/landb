<?php

namespace Botble\Orderstatuses\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Orderstatuses\Repositories\Interfaces\OrderstatusesInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Orderstatuses\Models\Orderstatuses;
use Html;

class OrderstatusesTable extends TableAbstract
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
     * OrderstatusesTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param OrderstatusesInterface $orderstatusesRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, OrderstatusesInterface $orderstatusesRepository)
    {
        $this->repository = $orderstatusesRepository;
        $this->setOption('id', 'plugins-orderstatuses-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['orderstatuses.edit', 'orderstatuses.destroy'])) {
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
                if (!Auth::user()->hasPermission('orderstatuses.edit')) {
                    return $item->name;
                }
                return Html::link(route('orderstatuses.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('orderstatuses.edit', 'orderstatuses.destroy', $item);
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
            'orderstatuses.id',
            'orderstatuses.name',
            'orderstatuses.qty_action',
            'orderstatuses.created_at',
            'orderstatuses.status',
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
                'name'  => 'orderstatuses.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name'  => 'orderstatuses.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'qty_action' => [
                'name'  => 'orderstatuses.qty_action',
                'title' => 'Qty Action',
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'orderstatuses.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name'  => 'orderstatuses.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('orderstatuses.create'), 'orderstatuses.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Orderstatuses::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('orderstatuses.deletes'), 'orderstatuses.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'orderstatuses.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'orderstatuses.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'orderstatuses.created_at' => [
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
