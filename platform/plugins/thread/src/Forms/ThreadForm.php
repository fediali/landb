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
        $categories = get_product_categories_custom();
        $designs = get_designs();
        $fits = get_fits();
        $rises = get_rises();
        $fabrics = get_fabrics();

        $this->formHelper->addCustomField('addDenimFields', AddDenimFields::class);
        $this
            ->setupModel(new Thread)
            ->setValidatorClass(ThreadRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
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
            ->add('category_id', 'customSelect', [
                'label'      => 'Select Category',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select Category',
                    'class' => 'select-search-full',
                ],
                'choices'    => $categories,
            ])
            /*->add('design_id', 'customSelect', [
                'label'      => 'Select Design',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select Design',
                    'class' => 'select-search-full',
                ],
                'choices'    => $designs,
            ])
            ->add('pp_request', 'customSelect', [
                'label'      => 'Select PP Request',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select PP Request',
                    'class' => 'select-search-full',
                ],
                'choices'    => Thread::$statuses,
            ])*/
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
                    'data-date-format' => 'yyyy/mm/dd',
                ],
                'default_value' => now(config('app.timezone'))->format('Y/m/d'),
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
                'data' => ['fits'=>$fits,'rises'=>$rises,'fabrics'=>$fabrics]
            ])
            /*->add('inseam', 'text', [
                'label'      => 'Inseam',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Inseam',
                    'data-counter' => 120,
                ],
            ])
            ->add('fit_id', 'customSelect', [
                'label'      => 'Select Fit',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select Fit',
                    'class' => 'select-search-full',
                ],
                'choices'    => $fits,
            ])
            ->add('rise_id', 'customSelect', [
                'label'      => 'Select Rise',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select Rise',
                    'class' => 'select-search-full',
                ],
                'choices'    => $rises,
            ])
            ->add('fabric_id', 'customSelect', [
                'label'      => 'Select Fabric',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Select Fabric',
                    'class' => 'select-search-full',
                ],
                'choices'    => $fabrics,
            ])
            ->add('fabric_print_direction', 'text', [
                'label'      => 'Fabric Print Direction',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Fabric Print Direction',
                    'data-counter' => 120,
                ],
            ])
            ->add('wash', 'text', [
                'label'      => 'Wash',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder'  => 'Wash',
                    'data-counter' => 120,
                ],
            ])*/
            ->add('description', 'editor', [
                'label'      => 'Description',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'rows'            => 2,
                    'placeholder'     => 'Description',
                    'with-short-code' => true,
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
            /*->add('order_no', 'text', [
                'label'      => 'Order No.',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Order No.',
                    'data-counter' => 120,
                ],
            ])*/
            ->add('order_status', 'customSelect', [
                'label'      => 'Select Order Status',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select Order Status',
                    'class' => 'select-search-full',
                ],
                'choices'    => Thread::$order_statuses,
            ])
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
                    'data-date-format' => 'yyyy/mm/dd',
                ],
                'default_value' => now(config('app.timezone'))->format('Y/m/d'),
            ])
            ->add('ship_date', 'text', [
                'label'      => 'Ship Date',
                'label_attr' => ['class' => 'control-label required'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'yyyy/mm/dd',
                ],
                'default_value' => now(config('app.timezone'))->format('Y/m/d'),
            ])
            ->add('cancel_date', 'text', [
                'label'      => 'Cancel Date',
                'label_attr' => ['class' => 'control-label required'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'yyyy/mm/dd',
                ],
                'default_value' => now(config('app.timezone'))->format('Y/m/d'),
            ])
            ->add('spec_file', 'mediaImage', [
                'label'      => 'Tech Spec File',
                'label_attr' => ['class' => 'control-label'],
            ])
            ->setBreakFieldPoint('status');
    }
}
