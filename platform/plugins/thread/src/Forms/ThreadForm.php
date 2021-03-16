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
        $designers = get_designers();
        $vendors = get_vendors();
        $seasons = get_seasons();
        $regular_categories = get_reg_product_categories_custom();
        $plus_categories = get_plu_product_categories_custom();
        $fits = get_fits();
        $rises = get_rises();
        $fabrics = get_fabrics();

        $selectedRegCat = [];
        $selectedPluCat = [];
        if ($this->getModel()) {
            $selectedRegCat = $this->getModel()->regular_product_categories()->pluck('product_category_id')->all();
            $selectedPluCat = $this->getModel()->plus_product_categories()->pluck('product_category_id')->all();
        }

        $this->formHelper->addCustomField('addDenimFields', AddDenimFields::class);
        $this
            ->setupModel(new Thread)
            ->setValidatorClass(ThreadRequest::class)
            ->withCustomFields()
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
                'data' => ['fits'=>$fits,'rises'=>$rises,'fabrics'=>$fabrics,'model'=>$this->model]
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
            ->add('material', 'text', [
                'label'      => 'Material',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Material',
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
                'label_attr' => ['class' => 'control-label'],
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
            ])
            ->add('ship_date', 'text', [
                'label'      => 'Ship Date',
                'label_attr' => ['class' => 'control-label required'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'd M, yyyy',
                ],
                'default_value' => now(config('app.timezone'))->format('d M, Y'),
            ])
            ->add('cancel_date', 'text', [
                'label'      => 'Cancel Date',
                'label_attr' => ['class' => 'control-label required'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'd M, yyyy',
                ],
                'default_value' => now(config('app.timezone'))->format('d M, Y'),
            ])
            ->add('spec_file', 'mediaImage', [
                'label'      => 'Tech Spec File',
                'label_attr' => ['class' => 'control-label'],
            ])
            ->setBreakFieldPoint('material');
    }
}
