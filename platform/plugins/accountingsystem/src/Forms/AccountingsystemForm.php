<?php

namespace Botble\Accountingsystem\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Accountingsystem\Http\Requests\AccountingsystemRequest;
use Botble\Accountingsystem\Models\Accountingsystem;

class AccountingsystemForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $this
            ->setupModel(new Accountingsystem)
            ->setValidatorClass(AccountingsystemRequest::class)
            ->withCustomFields()
            ->add('money', 'customRadio', [
                'label' => 'Money',
                'label_attr' => ['class' => 'control-label required'],
                'choices' => [
                    ['in', 'In'],
                    ['out', 'Out'],
                ],
            ])
            ->add('description', 'text', [
                'label'      => 'Description',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Description',
                    'data-counter' => 200,
                ],
            ])
            ->add('amount', 'number', [
                'label'      => 'Amount ($)',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder' => 'Amount ($)',
                    'step' => 0.1
                ],
            ]);
    }
}
