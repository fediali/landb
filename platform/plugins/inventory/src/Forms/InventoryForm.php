<?php

namespace Botble\Inventory\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Blog\Forms\Fields\AddProductsField;
use Botble\Inventory\Http\Requests\InventoryRequest;
use Botble\Inventory\Models\Inventory;

class InventoryForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
      $this->formHelper->addCustomField('addProducts', \Botble\Inventory\Forms\Fields\AddProductsField::class);
        $this
            ->setupModel(new Inventory)
            ->setValidatorClass(InventoryRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('description', 'text', [
                'label'      => 'Description',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Inventory Description',
                    'data-counter' => 200,
                ],
            ])
            ->add('date', 'date', [
                'label'         => 'Select Date',
                'label_attr'    => ['class' => 'control-label'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'd M, yyyy',
                ],
                'default_value' => now(config('app.timezone'))->format('d M, Y'),
                'value' => $this->model->date ? date('d M, Y', strtotime($this->model->date)) : now(config('app.timezone'))->format('d M, Y')
            ])
            ->add('comments', 'textarea', [
                'label'      => 'Comments',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Comments',
                ],
            ])
            ->add('Products', 'addProducts', [
                'label'      => 'Comments',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Comments',
                ],
                'data' => $this->model
            ])
            ->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => BaseStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status');
    }
}
