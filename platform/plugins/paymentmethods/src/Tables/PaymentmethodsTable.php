<?php

namespace Botble\Paymentmethods\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Paymentmethods\Repositories\Interfaces\PaymentmethodsInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Paymentmethods\Models\Paymentmethods;
use Html;

class PaymentmethodsTable extends TableAbstract
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
     * PaymentmethodsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param PaymentmethodsInterface $paymentmethodsRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, PaymentmethodsInterface $paymentmethodsRepository)
    {
        $this->repository = $paymentmethodsRepository;
        $this->setOption('id', 'plugins-paymentmethods-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['paymentmethods.edit', 'paymentmethods.destroy'])) {
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
                if (!Auth::user()->hasPermission('paymentmethods.edit')) {
                    return $item->name;
                }
                return Html::link(route('paymentmethods.edit', $item->id), $item->name);
            })*/
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
                return $this->getOperations('paymentmethods.edit', 'paymentmethods.destroy', $item);
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
            'paymentmethods.id',
            'paymentmethods.slug',
            'paymentmethods.name',
            'paymentmethods.created_at',
            'paymentmethods.status',
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
                'name'  => 'paymentmethods.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'slug' => [
                'name'  => 'paymentmethods.slug',
                'title' => 'Slug',
                'class' => 'text-left',
            ],
            'name' => [
                'name'  => 'paymentmethods.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'paymentmethods.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status' => [
                'name'  => 'paymentmethods.status',
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
        $buttons = $this->addCreateButton(route('paymentmethods.create'), 'paymentmethods.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Paymentmethods::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('paymentmethods.deletes'), 'paymentmethods.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            'paymentmethods.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'paymentmethods.status' => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'paymentmethods.created_at' => [
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
