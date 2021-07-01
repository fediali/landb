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
        $vendor_products = get_vendor_products();
        $product_units = get_product_units();

        $vendors = get_vendors();
        $regular_categories = get_reg_product_categories_custom();
        $plus_categories = get_plu_product_categories_custom();

        $selectedRegCat = [];
        $selectedPluCat = [];
        $selRegProdUnit = '';
        $selPluProdUnit = '';
        $selRegPPQty = 0;
        $selPluPPQty = 0;
        if ($this->getModel()) {
            $regCat = $this->getModel()->regular_product_categories();
            $pluCat = $this->getModel()->plus_product_categories();
            $selectedRegCat = $regCat->pluck('product_category_id')->all();
            $selectedPluCat = $pluCat->pluck('product_category_id')->all();
            $selRegProdUnit = $regCat->value('categories_threads.product_unit_id');
            $selPluProdUnit = $pluCat->value('categories_threads.product_unit_id');
            $selRegPPQty = $regCat->value('categories_threads.per_piece_qty');
            $selPluPPQty = $pluCat->value('categories_threads.per_piece_qty');
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
                ],
                'choices'    => $vendors,
            ])

            ->add('regular_category_id', 'customSelect', [
                'label'      => 'Select Regular Pack Category',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select Regular Category',
                    'class' => 'select-search-full',
                    'disabled'
                ],
                'choices'    => $regular_categories,
                'default_value'      => old('regular_category_id', $selectedRegCat),
            ])
            ->add('regular_product_unit_id', 'customSelect', [
                'label'      => 'Select Per Piece Making Product Unit (Reg)',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select Product Unit (Reg)',
                    'class' => 'select-search-full',
                ],
                'choices'    => $product_units,
                'default_value' => old('regular_product_unit_id', $selRegProdUnit)
            ])
            ->add('regular_per_piece_qty', 'number', [
                'label'      => 'Per Piece Making Qty (Reg)',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Per Piece Qty (Reg)',
                    'steps' => 0.1,
                ],
                'default_value' => old('regular_per_piece_qty', $selRegPPQty)
            ])

            ->add('plus_category_id', 'customSelect', [
                'label'      => 'Select Plus Pack Category',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select Plus Category',
                    'class' => 'select-search-full',
                    'disabled'
                ],
                'choices'    => $plus_categories,
                'default_value'      => old('plus_category_id', $selectedPluCat),
            ])
            ->add('plus_product_unit_id', 'customSelect', [
                'label'      => 'Select Per Piece Making Product Unit (Plus)',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select Product Unit (Plus)',
                    'class' => 'select-search-full',
                ],
                'choices'    => $product_units,
                'default_value' => old('regular_product_unit_id', $selPluProdUnit)
            ])
            ->add('plus_per_piece_qty', 'number', [
                'label'      => 'Per Piece Making Qty (Plus)',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Per Piece Qty (Plus)',
                    'steps' => 0.1,
                ],
                'default_value' => old('regular_per_piece_qty', $selPluPPQty)
            ])

            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            /*->add('pp_sample_date', 'text', [
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
            ])*/
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

            ->add('thread_status', 'customSelect', [
                'label'      => 'Select Thread Status',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder' => 'Select Thread Status',
                    'class'       => 'select-search-full',
                    'disabled'
                ],
                'choices'    => Thread::$thread_statuses,
            ])
            ->add('vendor_product_id', 'customSelect', [
                'label'      => 'Select Vendor Making Product',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select Vendor Product',
                    'class' => 'select-search-full',
                ],
                'choices'    => $vendor_products,
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
            ->setBreakFieldPoint('thread_status');
    }
}
