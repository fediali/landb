<?php

namespace Botble\Textmessages\Tables;

use Auth;
use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Textmessages\Repositories\Interfaces\TextmessagesInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Botble\Textmessages\Models\Textmessages;
use Html;

class TextmessagesTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = false;

    /**
     * @var bool
     */
    protected $hasFilter = false;

    /**
     * @var bool
     */
    protected $hasCheckbox = false;

    /**
     * @var bool
     */
    protected $hasOperations = false;

    /**
     * TextmessagesTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param TextmessagesInterface $textmessagesRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, TextmessagesInterface $textmessagesRepository)
    {
        $this->repository = $textmessagesRepository;
        $this->setOption('id', 'plugins-textmessages-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['textmessages.edit', 'textmessages.destroy'])) {
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
                if (!Auth::user()->hasPermission('textmessages.edit')) {
                    return $item->name;
                }
                return Html::link(route('textmessages.edit', $item->id), $item->name);
            })*/
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return ucwords($item->status);
            })
            /*->addColumn('resend_sms', function ($item) {
                $html = '-';
                if ($item->status == BaseStatusEnum::PUBLISHED) {
                    $html = '<a href="javascript:void(0)" onclick="confirm_start(' . '\'' . route('chating.smsCampaign', $item->id) . '\'' . ')" class="btn btn-icon btn-sm btn-info">Resend SMS</a><script>function confirm_start(url){
                          swal({
                              title: \'Are you sure?\',
                              text: "Do you want to resend this message to Customers!",
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
            })*/;

        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('textmessages.edit', 'textmessages.destroy', $item);
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
            'textmessages.id',
            'textmessages.name',
            'textmessages.text',
            'textmessages.created_at',
            'textmessages.status',
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
            'id'         => [
                'name'  => 'textmessages.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name'       => [
                'name'  => 'textmessages.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'text'       => [
                'name'  => 'textmessages.text',
                'title' => 'Text',
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'textmessages.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status'     => [
                'name'  => 'textmessages.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
            /*'resend_sms'    => [
                'name'    => 'resend_sms',
                'title'   => 'Resend SMS',
                'width'   => '100px',
                //'visible' => (Auth::user()->hasPermission('chating.smsCampaign')) ? true : false,
            ]*/
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('textmessages.create'), 'textmessages.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Textmessages::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return parent::bulkActions();
        //return $this->addDeleteAction(route('textmessages.deletes'), 'textmessages.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            /*'textmessages.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'textmessages.status'     => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => BaseStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
            ],
            'textmessages.created_at' => [
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




