<?php

namespace Botble\Printdesigns\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Printdesigns\Repositories\Interfaces\PrintdesignsInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Printdesigns\Models\Printdesigns;
use Html;
use RvMedia;

class PrintdesignsTable extends TableAbstract
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
     * PrintdesignsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param PrintdesignsInterface $printdesignsRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, PrintdesignsInterface $printdesignsRepository)
    {
        $this->repository = $printdesignsRepository;
        $this->setOption('id', 'plugins-printdesigns-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['printdesigns.edit', 'printdesigns.destroy'])) {
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
            ->editColumn('designer_id', function ($item) {
                return $item->designer ? $item->designer->getFullName() : null;
            })
            /*->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('printdesigns.edit')) {
                    return $item->name;
                }
                return Html::link(route('printdesigns.edit', $item->id), $item->name);
            })*/
            ->editColumn('file', function ($item) {
                return Html::image(RvMedia::getImageUrl($item->file, 'thumb', false, RvMedia::getDefaultImage()),
                    $item->name, ['width' => 50]);
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
                return $this->getOperations('printdesigns.edit', 'printdesigns.destroy', $item);
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
            'printdesigns.id',
            'printdesigns.designer_id',
            'printdesigns.name',
            'printdesigns.sku',
            'printdesigns.file',
            'printdesigns.created_at',
            'printdesigns.status',
        ];

        $query = $model
            ->with([
                'designer'     => function ($query) {
                    $query->select(['id', 'first_name', 'last_name']);
                },
            ])
            ->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id' => [
                'name'  => 'printdesigns.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'designer_id'  => [
                'name'      => 'printdesigns.designer_id',
                'title'     => 'Designer',
                'class'     => 'no-sort text-left',
                'orderable' => false,
            ],
            'name' => [
                'name'  => 'printdesigns.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'sku' => [
                'name'  => 'printdesigns.sku',
                'title' => 'SKU',
                'class' => 'text-left',
            ],
            'file'      => [
                'name'  => 'printdesigns.file',
                'title' => 'File',
                'width' => '70px',
            ],
            'created_at' => [
                'name'  => 'printdesigns.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name'  => 'printdesigns.status',
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
        $buttons = $this->addCreateButton(route('printdesigns.create'), 'printdesigns.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Printdesigns::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('printdesigns.deletes'), 'printdesigns.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'printdesigns.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'printdesigns.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'printdesigns.created_at' => [
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
