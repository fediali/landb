<?php

namespace Botble\Threadorders\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Thread\Models\Thread;
use Botble\Threadorders\Forms\Fields\AddThreadOrderVariationFields;
use Botble\Threadorders\Http\Requests\ThreadordersRequest;
use Botble\Threadorders\Models\Threadorders;

class ThreadordersForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $vendors = get_vendors();
        $regular_categories = get_reg_product_categories_custom();
        $plus_categories = get_plu_product_categories_custom();

        $selectedRegCat = [];
        $selectedPluCat = [];
        if ($this->getModel()) {
            $selectedRegCat = $this->getModel()->regular_product_categories()->pluck('product_category_id')->all();
            $selectedPluCat = $this->getModel()->plus_product_categories()->pluck('product_category_id')->all();
        }

        $this->formHelper->addCustomField('addThreadOrderVariationFields', AddThreadOrderVariationFields::class);
        $this
            ->setupModel(new Threadorders)
            ->setValidatorClass(ThreadordersRequest::class)
            ->withCustomFields()
            ->add('thread_id', 'hidden', [
                'value' => $this->model->id
            ])
            ->add('vendor_id', 'customSelect', [
                'label'      => 'Select Vendor',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select Vendor',
                    'class' => 'select-search-full',
                    'disabled'
                ],
                'choices'    => $vendors,
            ])
            ->add('regular_category_id', 'customSelect', [
                'label'      => 'Select Regular Category',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select Regular Category',
                    'class' => 'select-search-full',
                    'disabled'
                ],
                'choices'    => $regular_categories,
                'default_value'      => old('regular_category_id', $selectedRegCat),
            ])
            ->add('plus_category_id', 'customSelect', [
                'label'      => 'Select Plus Category',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select Plus Category',
                    'class' => 'select-search-full',
                    'disabled'
                ],
                'choices'    => $plus_categories,
                'default_value'      => old('plus_category_id', $selectedPluCat),
            ])

            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('pp_sample_date', 'text', [
                'label'         => 'PP Sample Date',
                'label_attr'    => ['class' => 'control-label'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'd M, yyyy',
                ],
                'default_value' => now(config('app.timezone'))->format('d M, Y'),
                'value' => old('pp_sample_date', date('d M, Y', strtotime($this->model->pp_sample_date)))
            ])
            ->add('pp_sample', 'customSelect', [
                'label'      => 'Select PP Sample',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select PP Sample',
                    'class' => 'select-search-full',
                ],
                'choices'    => Thread::$statuses,
            ])
            ->add('material', 'text', [
                'label'      => 'Fabric',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Fabric',
                    'data-counter' => 120,
                ],
            ])
            ->add('shipping_method', 'customSelect', [
                'label'      => 'Select Shipping Method',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select Shipping Method',
                    'class' => 'select-search-full',
                ],
                'choices'    => Thread::$shipping_methods,
            ])

            ->add('addThreadOrderVariationFields', 'addThreadOrderVariationFields', [
                'data' => $this->model
            ])

            ->add('order_date', 'text', [
                'label'      => 'Order Date',
                'label_attr' => ['class' => 'control-label required'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'd M, yyyy',
                ],
                'default_value' => now(config('app.timezone'))->format('d M, Y'),
                'value' => old('order_date', date('d M, Y', strtotime($this->model->order_date)))
            ])
            ->add('ship_date', 'text', [
                'label'      => 'Ship Date',
                'label_attr' => ['class' => 'control-label required'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'd M, yyyy',
                ],
                'default_value' => now(config('app.timezone'))->format('d M, Y'),
                'value' => old('ship_date', date('d M, Y', strtotime($this->model->ship_date)))
            ])
            ->add('cancel_date', 'text', [
                'label'      => 'No later than',
                'label_attr' => ['class' => 'control-label required'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'd M, yyyy',
                ],
                'default_value' => now(config('app.timezone'))->format('d M, Y'),
                'value' => old('cancel_date', date('d M, Y', strtotime($this->model->cancel_date)))
            ])
            ->setBreakFieldPoint('order_date');
    }
}
