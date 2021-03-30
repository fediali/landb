<?php

namespace Botble\Vendorproducts\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Vendorproducts\Http\Requests\VendorproductsRequest;
use Botble\Vendorproducts\Models\Vendorproducts;

class VendorproductsForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $product_units = get_product_units();
        $this
            ->setupModel(new Vendorproducts)
            ->setValidatorClass(VendorproductsRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('product_unit_id', 'customSelect', [
                'label'      => 'Select Product Unit',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Select Product Unit',
                    'class' => 'select-search-full',
                ],
                'choices'    => $product_units,
            ])
            ->add('quantity', 'number', [
                'label'      => 'Product Quantity',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'Product Quantity',
                    'steps' => 0.1,
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
            ->setBreakFieldPoint('status');
    }
}
