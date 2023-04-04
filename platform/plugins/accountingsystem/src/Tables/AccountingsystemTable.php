<?php

namespace Botble\Accountingsystem\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Accountingsystem\Repositories\Interfaces\AccountingsystemInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Accountingsystem\Models\Accountingsystem;
use Html;

class AccountingsystemTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = false;
    protected $hasOperations = false;

    /**
     * @var bool
     */
    protected $hasFilter = false;

    /**
     * @var bool
     */
    public $hasCustomFilter = true;

    /**
     * @var string
     */
    protected $customFilterTemplate = 'plugins/accountingsystem::filter';

    /**
     * AccountingsystemTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param AccountingsystemInterface $accountingsystemRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AccountingsystemInterface $accountingsystemRepository)
    {
        $this->repository = $accountingsystemRepository;
        $this->setOption('id', 'plugins-accountingsystem-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['accountingsystem.edit', 'accountingsystem.destroy'])) {
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
                if (!Auth::user()->hasPermission('accountingsystem.edit')) {
                    return $item->name;
                }
                return Html::link(route('accountingsystem.edit', $item->id), $item->name);
            })*/
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('money', function ($item) {
                $color = $item->money == 'in' ? 'green' : 'red';
                return '<span style="color: '.$color.'">'.strtoupper($item->money).'</span>';
            })
            ->editColumn('amount', function ($item) {
                return '$ '.$item->amount;
            })
            ->editColumn('created_by', function ($item) {
                return $item->user->getFullName();
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at, config('core.base.general.date_format.date_time'));
            });
            /*->editColumn('status', function ($item) {
                return $item->status->toHtml();
            });*/

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('accountingsystem.edit', 'accountingsystem.destroy', $item);
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
            'accountingsystems.id',
            'accountingsystems.money',
            'accountingsystems.description',
            'accountingsystems.amount',
            'accountingsystems.created_by',
            'accountingsystems.created_at',
        ];

        $query = $model->select($select)->orderBy('accountingsystems.id', 'DESC');

        $query = $query->whereDate('accountingsystems.created_at', request('sel_date', date('Y-m-d')));

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            /*'id' => [
                'name'  => 'accountingsystems.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],*/
            'money' => [
                'name'  => 'accountingsystems.money',
                'title' => 'Money',
                'class' => 'text-left',
            ],
            'description' => [
                'name'  => 'accountingsystems.description',
                'title' => 'Description',
                'class' => 'text-left',
            ],
            'amount' => [
                'name'  => 'accountingsystems.amount',
                'title' => 'Amount',
                'class' => 'text-left',
            ],
            'created_by' => [
                'name'  => 'accountingsystems.created_by',
                'title' => 'User',
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'accountingsystems.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '200px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('accountingsystem.create'), 'accountingsystem.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Accountingsystem::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('accountingsystem.deletes'), 'accountingsystem.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            /*'accountingsystems.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'accountingsystems.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'accountingsystems.created_at' => [
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

    /**
     * @return string
     * @throws Throwable
     */
    public function renderCustomFilter(): string
    {
        $data['totalCash'] = Accountingsystem::where(['money' => 'in'])->whereDate('created_at', request('sel_date', date('Y-m-d')))->value('amount');

        $data['cashIn'] = Accountingsystem::where(['money' => 'in'])->whereDate('created_at', request('sel_date', date('Y-m-d')))->sum('amount');
        $data['cashOut'] = Accountingsystem::where(['money' => 'out'])->whereDate('created_at', request('sel_date', date('Y-m-d')))->sum('amount');

        $data['leftover'] = $data['cashIn'] - $data['cashOut'];

        $data['diff'] = $data['totalCash'] - $data['leftover'];
        return view($this->customFilterTemplate, compact('data'))->render();
    }

}
