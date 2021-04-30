<?php

namespace Botble\Orderstatuses\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Orderstatuses\Http\Requests\OrderstatusesRequest;
use Botble\Orderstatuses\Models\Orderstatuses;

class OrderstatusesForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $this
            ->setupModel(new Orderstatuses)
            ->setValidatorClass(OrderstatusesRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => BaseStatusEnum::labels(),
            ])
            ->add('qty_action', 'customSelect', [
                'label'      => 'Select Qty Action',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder' => 'Select Qty Action',
                    'class'       => 'select-search-full',
                ],
                'choices'    => Orderstatuses::$QTY_ACTIONS,
            ])
            ->setBreakFieldPoint('status');
    }
}
