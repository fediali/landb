<?php

namespace Botble\Vendororderstatuses\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Vendororderstatuses\Repositories\Interfaces\VendororderstatusesInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Vendororderstatuses\Models\Vendororderstatuses;
use Html;

class VendororderstatusesTable extends TableAbstract
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
     * VendororderstatusesTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param VendororderstatusesInterface $vendororderstatusesRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, VendororderstatusesInterface $vendororderstatusesRepository)
    {
        $this->repository = $vendororderstatusesRepository;
        $this->setOption('id', 'plugins-vendororderstatuses-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['vendororderstatuses.edit', 'vendororderstatuses.destroy'])) {
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
                if (!Auth::user()->hasPermission('vendororderstatuses.edit')) {
                    return $item->name;
                }
                return Html::link(route('vendororderstatuses.edit', $item->id), $item->name);
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
                return $this->getOperations('vendororderstatuses.edit', 'vendororderstatuses.destroy', $item);
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
            'vendororderstatuses.id',
            'vendororderstatuses.name',
            'vendororderstatuses.created_at',
            'vendororderstatuses.status',
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
                'name'  => 'vendororderstatuses.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name'  => 'vendororderstatuses.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'vendororderstatuses.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name'  => 'vendororderstatuses.status',
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
        $buttons = $this->addCreateButton(route('vendororderstatuses.create'), 'vendororderstatuses.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Vendororderstatuses::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('vendororderstatuses.deletes'), 'vendororderstatuses.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'vendororderstatuses.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'vendororderstatuses.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'vendororderstatuses.created_at' => [
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
