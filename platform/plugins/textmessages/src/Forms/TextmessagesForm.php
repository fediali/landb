<?php

namespace Botble\Textmessages\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Textmessages\Http\Requests\TextmessagesRequest;
use Botble\Textmessages\Models\Textmessages;
use Botble\Textmessages\Forms\Fields\AddCustomerFields;

class TextmessagesForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $customers = get_customers_by_sales_rep();

        $selectedCustomers = array_map('intval', explode(',', @$this->model->customer_ids));

        $this->formHelper->addCustomField('addCustomerFields', AddCustomerFields::class);

        $this
            ->setupModel(new Textmessages)
            ->setValidatorClass(TextmessagesRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('text', 'textarea', [
                'label'      => 'Text',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                ]
            ])
            /*->add('schedule_date', 'datetime-local', [
                'label'      => 'Schedule Date',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'            => 'form-control',
                    // 'data-date-format' => 'd M, Y',
                ],
                'value'      => date('m/d/YTH:i:s', strtotime($this->model->schedule_date))
            ])*/
            /*->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => BaseStatusEnum::$SCHEDULE,
            ])*/
            /*->add('customer_ids', 'customSelect', [
                'label'      => 'Select Customers',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder' => 'Select Customers',
                    'class'       => 'select-search-full',
                    'multiple' => 'multiple'
                ],
                'choices' => $customers,
                'default_value' => old('customer_ids', $selectedCustomers),
            ])*/
            ->add('customer_type', 'customSelect', [
                'label'      => 'Select Customer Type',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => [
                    'auto' => 'Auto',
                    'manual' => 'Manual',
                ],
            ])
            ->add('customerFields', 'addCustomerFields', [
                'label'      => 'Customer Fields',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder' => 'Customer Fields',
                ],
                'data'       => []
            ])
            ->setBreakFieldPoint('customer_type');
    }
}



