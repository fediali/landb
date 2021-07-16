<?php

namespace Botble\Thread\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\UserSearch;
use Botble\Ecommerce\Models\UserSearchItem;
use Botble\Thread\Repositories\Interfaces\ThreadInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Botble\Thread\Models\Thread;
use Html;

class ThreadTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    public $hasCustomFilter = true;

    protected $customFilterTemplate = 'plugins/thread::filter';


    /**
     * ThreadTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ThreadInterface $threadRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ThreadInterface $threadRepository)
    {
        $this->repository = $threadRepository;
        $this->setOption('id', 'plugins-thread-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['thread.edit', 'thread.destroy', 'thread.cloneItem', 'thread.details', 'threadorders.create'])) {
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
            /*  ->editColumn('name', function ($item) {
                     if (!Auth::user()->hasPermission('thread.edit')) {
                         return $item->name;
                     }
                     return Html::link(route('thread.edit', $item->id), $item->name);
                 })*/
            ->editColumn('designer_id', function ($item) {
                return $item->designer ? $item->designer->getFullName() : null;
            })->editColumn('vendor_id', function ($item) {
                return $item->vendor_id ? $item->vendor->getFullName() : null;
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return date('M d,Y', strtotime(BaseHelper::formatDate($item->created_at, '')));

            })->addColumn('create_thread_order', function ($item) {
                if ($item->vendor_id > 0 && $item->status == BaseStatusEnum::PUBLISHED && count($item->thread_variations)) {
                    if ($item->thread_has_order) {
                        return '<a href="' . route('threadorders.createThreadOrder', $item->id) . '" class="btn btn-icon btn-sm btn-warning" data-toggle="tooltip" data-original-title="Re-Order"><i class="fa fa-shopping-cart"></i> Re-Order</a>';
                    } else {
                        return '<a href="' . route('threadorders.createThreadOrder', $item->id) . '" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="Order"><i class="fa fa-shopping-cart"></i> Order</a>';
                    }
                } else {
                    return 'N/A';
                }
            })
            ->editColumn('is_denim', function ($item) {
                return $item->is_denim ? 'Yes' : 'No';
            })
            ->editColumn('status', function ($item) {
                //return $item->status->toHtml();
                return view('plugins/thread::threadStatus', ['item' => $item])->render();
            });

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                $html = '<a href="' . route('thread.cloneItem', $item->id) . '" class="btn btn-icon btn-sm btn-warning" data-toggle="tooltip" data-original-title="Clone"><i class="fa fa-copy"></i></a>';
                /*if ($item->vendor_id > 0 && $item->status == BaseStatusEnum::PUBLISHED) {
                    $html .= '<a href="'.route('threadorders.createThreadOrder', $item->id).'" class="btn btn-icon btn-sm btn-info" data-toggle="tooltip" data-original-title="Order"><i class="fa fa-shopping-cart"></i></a>';
                }*/
                $html .= '<a href="' . route('thread.details', $item->id) . '" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-original-title="Details"><i class="fa fa-eye"></i></a>';
                $html .= '<a href="' . route('threadsample.show', $item->id) . '" class="btn btn-icon btn-sm btn-success" data-toggle="tooltip" data-original-title="Thread Sample"><i class="fa fa-paper-plane"></i></a>';

                if (auth()->user()->hasPermission('thread.destroy')) {
                    if (!$item->thread_has_order && auth()->user()->hasPermission('thread.destroy')) {
                        $html .= '<a href="#" class="btn btn-icon btn-sm btn-danger deleteDialog" data-toggle="tooltip" data-section="' . route('thread.destroy', $item->id) . '" role="button" data-original-title="' . trans('core/base::tables.delete_entry') . '" >
                                <i class="fa fa-trash"></i>
                              </a>';
                    }
                }
                //thread.destroy
                return $this->getOperations('thread.edit', '', $item, $html);
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
            'threads.id',
            'threads.name',
            'categories_threads.sku AS reg_sku',
            'threads.designer_id',
            'threads.vendor_id',
            'threads.created_at',
            'threads.pp_sample',
            'threads.status',
            'threads.ready',
            'threads.is_denim',
            'threads.order_status',
            'threads.thread_status',
        ];

        $query = $model
            ->with([
                'designer' => function ($query) {
                    $query->select(['id', 'first_name', 'last_name']);
                },
            ])
            ->join('categories_threads', 'categories_threads.thread_id', '=', 'threads.id')
            ->where('categories_threads.category_type', Thread::REGULAR)
            ->select($select);

        if ($this->request()->has('search_id')) {
            $search_id = (int)$this->request()->input('search_id');
            if ($search_id) {
                $search_items = UserSearchItem::where('user_search_id', $search_id)->pluck('value', 'key')->all();
            }
        }

        if (empty($search_items)) {
            $search_items = $this->request()->all();
        }
        if (!empty($search_items)) {
            $query->when(isset($search_items['status']), function ($q) use ($search_items) {
                $q->where('threads.status', $search_items['status']);
            });
            $query->when(isset($search_items['thread_status']), function ($q) use ($search_items) {
                $q->where('threads.thread_status', $search_items['thread_status']);
            });
            $query->when(isset($search_items['vendor']), function ($q) use ($search_items) {
                $q->where('threads.vendor_id', $search_items['vendor']);
            });
            $query->when(isset($search_items['designer']), function ($q) use ($search_items) {
                $q->where('threads.designer_id', $search_items['designer']);
            });
            $query->when(isset($search_items['ready']), function ($q) use ($search_items) {
                $q->where('threads.ready', $search_items['ready']);
            });
            $query->when(isset($search_items['pp_sample']), function ($q) use ($search_items) {
                $q->where('threads.pp_sample', $search_items['pp_sample']);
            });

        }
        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'                  => [
                'name'  => 'threads.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name'                => [
                'name'  => 'threads.name',
                'title' => 'Description',
                'class' => 'text-left',
            ],
            'reg_sku'             => [
                'name'  => 'categories_threads.sku',
                'title' => 'Reg SKU',
                'class' => 'text-left'
            ],
            'vendor_id'           => [
                'name'    => 'threads.vendor_id',
                'title'   => 'Vendor',
                'width'   => '50px',
                'visible' => (Auth::user()->hasPermission(['threadorders.create'])) ? true : false,
            ],
            'designer_id'         => [
                'name'  => 'threads.designer_id',
                'title' => 'Designer',
                'class' => 'no-sort text-left',
                //'orderable' => false,
            ],
            'is_denim'            => [
                'name'  => 'threads.is_denim',
                'title' => 'Denim',
                'class' => 'no-sort text-left',
                //'orderable' => false,
            ], 'order_status'     => [
                'name'  => 'Order Status',
                'title' => 'Order Status',
                'class' => 'no-sort text-left',
            ],
            'pp_sample'           => [
                'name'  => 'threads.pp_sample',
                'title' => 'PP Sample',
                'width' => '100px',
                'class' => 'no-sort text-left',
            ], 'thread_status'    => [
                'name'  => 'threads.thread_status',
                'title' => 'Thread Status',
                'class' => 'no-sort text-left',
                //'orderable' => false,
            ],
            'create_thread_order' => [
                'name'       => 'thread_order',
                'title'      => 'Create Order',
                'width'      => '100px',
                'searchable' => false,
                'visible'    => (Auth::user()->hasPermission(['threadorders.order'])) ? true : false,
            ],
            'created_at'          => [
                'name'  => 'threads.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status'              => [
                'name'  => 'threads.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
//                'visible' => (Auth::user()->hasPermission(['thread.create'])) ? true : false,
            ],
            'ready'               => [
                'name'    => 'threads.ready',
                'title'   => 'Ready To Order',
                'width'   => '50px',
                'visible' => (Auth::user()->hasPermission(['threadorders.create'])) ? true : false,
            ]

        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('thread.create'), 'thread.create');

        $buttons['download'] = [
            'link' => route('thread.download.tech.pack', ['offset' => 0, 'limit' => 5]),
            'text' => '<i class="fa fa-download"></i> Download Tech Pack(s)'
        ];

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Thread::class);
    }

    /**
     * {@inheritDoc}
     */
    public function htmlDrawCallbackFunction(): ?string
    {
        $return = parent::htmlDrawCallbackFunction();
        if (Thread::all()->count()) {
            $return .= '$(".editable").editable();';
        }
        return $return;
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
//        return $this->addDeleteAction(route('thread.deletes'), 'thread.destroy', parent::bulkActions());
        return parent::bulkActions();
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            /*'threads.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],*/
            'threads.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            /*'threads.created_at' => [
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

    public function renderCustomFilter(): string
    {
        $user = \Illuminate\Support\Facades\Auth::id();
        $searches = UserSearch::where(['search_type' => 'threads', 'status' => 1])->where('user_id', $user)->pluck('name', 'id')->all();
        $vendor = get_vendors();
        $designer = get_designers_for_thread();
        return view($this->customFilterTemplate, compact('searches', 'vendor', 'designer'))->render();
    }

}
