<?php

namespace Botble\Producttimeline\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Producttimeline\Http\Requests\ProducttimelineRequest;
use Botble\Producttimeline\Models\Producttimeline;

class ProducttimelineForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $this
            ->setupModel(new Producttimeline)
            ->setValidatorClass(ProducttimelineRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])->add('product_desc', 'text', [
                'label'      => 'Description',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])->add('product_link', 'text', [
                'label'      => 'Product URL',
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
            ])->add('date', 'date', [
                'label'         => 'Date',
                'label_attr'    => ['class' => 'control-label'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'd M, yyyy',
                ],
                'default_value' => now(config('app.timezone'))->format('d M, Y'),
                'value'         => $this->model->pp_sample_date ? date('d M, Y', strtotime($this->model->pp_sample_date)) : now(config('app.timezone'))->format('d M, Y')
            ])->add('schedule_date', 'datetime-local', [
                'label'      => 'Schedule Date',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'            => 'form-control',
                    'data-date-format' => 'd M, yyyy',
                ]
            ])->add('product_image', 'mediaImage', [
                'label'      => 'Select File',
                'label_attr' => ['class' => 'control-label'],
            ])
            ->setBreakFieldPoint('status');
    }
}
