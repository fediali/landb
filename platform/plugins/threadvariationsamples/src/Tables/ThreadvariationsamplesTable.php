<?php

namespace Botble\Threadvariationsamples\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Threadvariationsamples\Repositories\Interfaces\ThreadvariationsamplesInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Threadvariationsamples\Models\Threadvariationsamples;
use Html;

class ThreadvariationsamplesTable extends TableAbstract
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
     * ThreadvariationsamplesTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ThreadvariationsamplesInterface $threadvariationsamplesRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ThreadvariationsamplesInterface $threadvariationsamplesRepository)
    {
        $this->repository = $threadvariationsamplesRepository;
        $this->setOption('id', 'plugins-threadvariationsamples-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['threadvariationsamples.edit', 'threadvariationsamples.destroy'])) {
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
                if (!Auth::user()->hasPermission('threadvariationsamples.edit')) {
                    return $item->name;
                }
                return Html::link(route('threadvariationsamples.edit', $item->id), $item->name);
            })*/
            ->editColumn('photographer_id', function ($item) {
                return $item->photographer->getFullName();
            })
            ->editColumn('thread_id', function ($item) {
                return $item->thread->name;
            })
            ->editColumn('thread_variation_id', function ($item) {
                return $item->thread_variation->name;
            })
            ->editColumn('assign_date', function ($item) {
                return BaseHelper::formatDate($item->assign_date);
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
                $html = '<a href="' . route('threadvariationsamples.sampleMediaList', $item->id) . '" class="btn btn-icon btn-sm btn-warning" data-toggle="tooltip" data-original-title="Upload Media"><i class="fa fa-images"></i></a>';
                return $html; //$this->getOperations('threadvariationsamples.edit', 'threadvariationsamples.destroy', $item);
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
            'threadvariationsamples.id',
            'threadvariationsamples.photographer_id',
            'threadvariationsamples.thread_id',
            'threadvariationsamples.thread_variation_id',
            'threadvariationsamples.assign_date',
            'threadvariationsamples.created_at',
            'threadvariationsamples.status',
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
                'name'  => 'threadvariationsamples.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'photographer_id' => [
                'name'  => 'threadvariationsamples.photographer_id',
                'title' => 'Photographer',
                'class' => 'text-left',
            ],
            'thread_id' => [
                'name'  => 'threadvariationsamples.thread_id',
                'title' => 'Thread',
                'class' => 'text-left',
            ],
            'thread_variation_id' => [
                'name'  => 'threadvariationsamples.thread_variation_id',
                'title' => 'Thread Variation',
                'class' => 'text-left',
            ],
            'assign_date' => [
                'name'  => 'threadvariationsamples.assign_date',
                'title' => 'Assign Date',
                'width' => '100px',
            ],
            'created_at' => [
                'name'  => 'threadvariationsamples.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name'  => 'threadvariationsamples.status',
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
        // $buttons = $this->addCreateButton(route('threadvariationsamples.create'), 'threadvariationsamples.create');
        $buttons = [];
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Threadvariationsamples::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return []; //$this->addDeleteAction(route('threadvariationsamples.deletes'), 'threadvariationsamples.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            /*'threadvariationsamples.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'threadvariationsamples.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'threadvariationsamples.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
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
