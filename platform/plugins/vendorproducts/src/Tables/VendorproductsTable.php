<?php

namespace Botble\Vendorproducts\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Vendorproducts\Repositories\Interfaces\VendorproductsInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Vendorproducts\Models\Vendorproducts;
use Html;

class VendorproductsTable extends TableAbstract
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
     * VendorproductsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param VendorproductsInterface $vendorproductsRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, VendorproductsInterface $vendorproductsRepository)
    {
        $this->repository = $vendorproductsRepository;
        $this->setOption('id', 'plugins-vendorproducts-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['vendorproducts.edit', 'vendorproducts.destroy'])) {
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
            })
            ->editColumn('product_unit_id', function ($item) {
                return $item->product_unit->name;
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('vendorproducts.edit', 'vendorproducts.destroy', $item);
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
            'vendorproducts.id',
            'vendorproducts.name',
            'vendorproducts.quantity',
            'vendorproducts.product_unit_id',
            'vendorproducts.created_at',
            'vendorproducts.status',
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
                'name'  => 'vendorproducts.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name'  => 'vendorproducts.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'quantity' => [
                'name'  => 'vendorproducts.quantity',
                'title' => 'Product Qty',
                'class' => 'text-left',
            ],
            'product_unit_id' => [
                'name'  => 'vendorproducts.product_unit_id',
                'title' => 'Product Unit',
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'vendorproducts.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name'  => 'vendorproducts.status',
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
        $buttons = $this->addCreateButton(route('vendorproducts.create'), 'vendorproducts.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Vendorproducts::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('vendorproducts.deletes'), 'vendorproducts.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'vendorproducts.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'vendorproducts.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'vendorproducts.created_at' => [
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
