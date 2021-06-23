<?php

namespace Botble\Timeline\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Timeline\Repositories\Interfaces\TimelineInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Timeline\Models\Timeline;
use Html;

class TimelineTable extends TableAbstract
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
     * TimelineTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param TimelineInterface $timelineRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, TimelineInterface $timelineRepository)
    {
        $this->repository = $timelineRepository;
        $this->setOption('id', 'plugins-timeline-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['timeline.edit', 'timeline.destroy'])) {
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
                if (!Auth::user()->hasPermission('timeline.edit')) {
                    return $item->name;
                }
                return Html::link(route('timeline.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('date', function ($item) {
                return BaseHelper::formatDate($item->date);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('timeline.edit', 'timeline.destroy', $item);
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
            'timelines.id',
            'timelines.name',
            'timelines.created_at',
            'timelines.date',
            'timelines.status',
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
                'name'  => 'timelines.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name' => [
                'name'  => 'timelines.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'date' => [
                'name'  => 'timelines.date',
                'title' => 'date',
                'width' => '100px',
            ],
            'status' => [
                'name'  => 'timelines.status',
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
        $buttons = $this->addCreateButton(route('timeline.create'), 'timeline.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Timeline::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('timeline.deletes'), 'timeline.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'timelines.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'timelines.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'timelines.created_at' => [
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
