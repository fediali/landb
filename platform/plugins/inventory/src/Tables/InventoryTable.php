<?php

namespace Botble\Inventory\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Inventory\Repositories\Interfaces\InventoryInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Inventory\Models\Inventory;
use Html;

class InventoryTable extends TableAbstract
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
     * InventoryTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param InventoryInterface $inventoryRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, InventoryInterface $inventoryRepository)
    {
        $this->repository = $inventoryRepository;
        $this->setOption('id', 'plugins-inventory-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['inventory.edit', 'inventory.destroy'])) {
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
            /*->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('inventory.edit')) {
                    return $item->name;
                }
                return Html::link(route('inventory.edit', $item->id), $item->name);
            })*/
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->addColumn('ecommerce', function ($item) {
                if ($item->is_full_released) {
                    $html = '<a href="javascript:void(0)" class="btn btn-sm btn-warning" disabled>Released</a>';
                } else {
                    $html = '<a href="javascript:void(0)" onclick="confirm_start(' . '\'' . route('inventory.pushToEcommerce', $item->id) . '\'' . ')" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="Release inventory to Ecommerce">Release</a><script>function confirm_start(url){
                      swal({
                          title: \'Are you sure?\',
                          text: "Do you want to release this Inventory to Ecommerce!",
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
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                if ($item->is_full_released) {
                    return '<a href="' . route('inventory.details', $item->id) . '" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="View"><i class="fa fa-eye"></i></a>';
                } else {
                    return $this->getOperations('inventory.edit', 'inventory.destroy', $item);
                }
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
            'inventories.id',
            'inventories.name',
            'inventories.id AS ecommerce',
            'inventories.created_at',
            'inventories.status',
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
                'name' => 'inventories.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name' => 'inventories.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'ecommerce' => [
                'name' => 'ecommerce',
                'title' => 'Ecommerce',
                'width' => '100px',
                //'visible' => false
            ],
            'created_at' => [
                'name' => 'inventories.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name' => 'inventories.status',
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
        $buttons = $this->addCreateButton(route('inventory.create'), 'inventory.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Inventory::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return parent::bulkActions();
        // return $this->addDeleteAction(route('inventory.deletes'), 'inventory.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            /*'inventories.name' => [
                'title' => trans('core/base::tables.name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'inventories.status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'inventories.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'date',
            ],*/
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
