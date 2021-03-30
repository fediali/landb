<?php

namespace Botble\Thread\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Thread\Forms\Fields\AddDenimFields;
use Botble\Thread\Http\Requests\ThreadRequest;
use Botble\Thread\Models\Thread;

class ThreadForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $vendor_products = get_vendor_products();
        $product_units = get_product_units();
        $designers = get_designers();
        $vendors = get_vendors();
        $seasons = get_seasons();
        $regular_categories = get_reg_product_categories_custom();
        $plus_categories = get_plu_product_categories_custom();
        $fits = get_fits();
        $rises = get_rises();
        $fabrics = get_fabrics();
        $wash = get_washes();

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

        $this->formHelper->addCustomField('addDenimFields', AddDenimFields::class);
        $this
            ->setupModel(new Thread)
            ->setValidatorClass(ThreadRequest::class)
            ->withCustomFields()
            ->setFormOption('enctype','multipart/form-data')
            ->add('name', 'text', [
                'label'      => 'Description',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Description',
                    'data-counter' => 120,
                ],
            ])
            ->add('designer_id', 'customSelect', [
                'label'      => 'Select Designer',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select Designer',
                    'class' => 'select-search-full',
                ],
                'choices'    => $designers,
            ])
            ->add('vendor_id', 'customSelect', [
                'label'      => 'Select Vendor',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select Vendor',
                    'class' => 'select-search-full',
                ],
                'choices'    => $vendors,
            ])
            ->add('season_id', 'customSelect', [
                'label'      => 'Select Season',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select Season',
                    'class' => 'select-search-full',
                ],
                'choices'    => $seasons,
            ])
            ->add('regular_category_id', 'customSelect', [
                'label'      => 'Select Regular Category',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select Regular Category',
                    'class' => 'select-search-full',
                    //'multiple' => 'multiple'
                ],
                'choices'    => $regular_categories,
                'default_value'      => old('regular_category_id', $selectedRegCat),
            ])
            ->add('regular_product_unit_id', 'customSelect', [
                'label'      => 'Select Product Unit (Reg)',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select Product Unit (Reg)',
                    'class' => 'select-search-full',
                ],
                'choices'    => $product_units,
                'default_value' => old('regular_product_unit_id', $selRegProdUnit)
            ])
            ->add('regular_per_piece_qty', 'number', [
                'label'      => 'Per Piece Qty (Reg)',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Per Piece Qty (Reg)',
                    'steps' => 0.1,
                ],
                'default_value' => old('regular_per_piece_qty', $selRegPPQty)
            ])
            ->add('plus_category_id', 'customSelect', [
                'label'      => 'Select Plus Category',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select Plus Category',
                    'class' => 'select-search-full',
                ],
                'choices'    => $plus_categories,
                'default_value'      => old('plus_category_id', $selectedPluCat),
            ])
            ->add('plus_product_unit_id', 'customSelect', [
                'label'      => 'Select Product Unit (Plus)',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select Product Unit (Plus)',
                    'class' => 'select-search-full',
                ],
                'choices'    => $product_units,
                'default_value' => old('regular_product_unit_id', $selPluProdUnit)
            ])
            ->add('plus_per_piece_qty', 'number', [
                'label'      => 'Per Piece Qty (Plus)',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Per Piece Qty (Plus)',
                    'steps' => 0.1,
                ],
                'default_value' => old('regular_per_piece_qty', $selPluPPQty)
            ])
            ->add('pp_sample', 'customSelect', [
                'label'      => 'Select PP Sample',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select PP Sample',
                    'class' => 'select-search-full',
                ],
                'choices'    => Thread::$statuses,
            ])
            ->add('pp_sample_size', 'text', [
                'label'      => 'PP Sample Size',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'PP Sample Size',
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
                'value' => $this->model->pp_sample_date ? date('d M, Y', strtotime($this->model->pp_sample_date)) : now(config('app.timezone'))->format('d M, Y')
            ])
            ->add('vendor_product_id', 'customSelect', [
                'label'      => 'Select Vendor Product',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select Vendor Product',
                    'class' => 'select-search-full',
                ],
                'choices'    => $vendor_products,
            ])
            ->add('is_denim', 'onOff', [
                'label'         => 'Denim',
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => false,
            ])
            ->add('DenimFields', 'addDenimFields', [
                'label'      => 'Denim Fields',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Denim Fields',
                ],
                'data' => ['fits'=>$fits,'rises'=>$rises,'fabrics'=>$fabrics,'model'=>$this->model, 'wash' => $wash]
            ])

            /*->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => BaseStatusEnum::labels(),
            ])
            ->add('order_status', 'customSelect', [
                'label'      => 'Select Order Status',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select Order Status',
                    'class' => 'form-control select-full',
                ],
                'choices'    => Thread::$order_statuses,
            ])*/
            ->add('thread_status', 'customSelect', [
                'label'      => 'Select Thread Status',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select Thread Status',
                    'class' => 'select-search-full',
                ],
                'choices'    => Thread::$thread_statuses,
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
            ->add('elastic_waste_pant', 'onOff', [
                'label'         => 'Elastic Waste Pant',
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => false,
            ])
            ->add('material', 'text', [
                'label'      => 'Fabric',
                'label_attr' => ['class' => 'control-label material-ip'],
                'attr'       => [
                    'placeholder'  => 'Fabric',
                    'data-counter' => 120,
                ],
            ])
            ->add('sleeve', 'text', [
                'label'      => 'Sleeve',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Sleeve',
                    'data-counter' => 120,
                ],
            ])
            ->add('label', 'text', [
                'label'      => 'Label',
                'label_attr' => ['class' => 'control-label label-ip'],
                'attr'       => [
                    'placeholder'  => 'Label',
                    'data-counter' => 120,
                ],
            ])
            ->add('order_date', 'text', [
                'label'      => 'Order Date',
                'label_attr' => ['class' => 'control-label required'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'd M, yyyy',
                ],
                'default_value' => now(config('app.timezone'))->format('d M, Y'),
                'value' => $this->model->order_date ? date('d M, Y', strtotime($this->model->order_date)) : now(config('app.timezone'))->format('d M, Y')
            ])
            ->add('ship_date', 'text', [
                'label'      => 'Ship Date',
                'label_attr' => ['class' => 'control-label required'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'd M, yyyy',
                ],
                'default_value' => now(config('app.timezone'))->format('d M, Y'),
                'value' => $this->model->ship_date ? date('d M, Y', strtotime($this->model->ship_date)) : now(config('app.timezone'))->format('d M, Y')
            ])
            ->add('cancel_date', 'text', [
                'label'      => 'No later than',
                'label_attr' => ['class' => 'control-label required'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'd M, yyyy',
                ],
                'default_value' => now(config('app.timezone'))->format('d M, Y'),
                'value' => $this->model->cancel_date ? date('d M, Y', strtotime($this->model->cancel_date)) : now(config('app.timezone'))->format('d M, Y')
            ])
            /*->add('spec_file', 'mediaImage', [
                'label'      => 'Tech Spec File',
                'label_attr' => ['class' => 'control-label'],
            ])*/
            ->setBreakFieldPoint('thread_status');
    }
}
