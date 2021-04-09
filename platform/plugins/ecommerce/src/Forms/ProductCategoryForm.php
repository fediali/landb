<?php

namespace Botble\Ecommerce\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Http\Requests\ProductCategoryRequest;
use Botble\Ecommerce\Models\ProductCategory;
use Illuminate\Support\Arr;

class ProductCategoryForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $list = get_product_categories();

        $categories = [];
        foreach ($list as $row) {
            $categories[$row->id] = $row->indent_text . ' ' . $row->name;
        }
        $categories[0] = trans('plugins/ecommerce::product-categories.none');
        $categories = Arr::sortRecursive($categories);
        $product_units = get_product_units();
        $category_sizes = get_category_sizes();
        $selectedCatSizes = [];
        if ($this->getModel()) {
            $selectedCatSizes = $this->getModel()->category_sizes()->pluck('category_size_id')->all();
        }
        $this
            ->setupModel(new ProductCategory)
            ->setValidatorClass(ProductCategoryRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('parent_id', 'select', [
                'label'      => trans('core/base::forms.parent'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'select-search-full',
                ],
                'choices'    => $categories,
            ])
            ->add('description', 'editor', [
                'label'      => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'rows'         => 4,
                    'placeholder'  => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 500,
                ],
            ])
            ->add('order', 'number', [
                'label'         => trans('core/base::forms.order'),
                'label_attr'    => ['class' => 'control-label'],
                'attr'          => [
                    'placeholder' => trans('core/base::forms.order_by_placeholder'),
                ],
                'default_value' => 0,
            ])
            ->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'choices'    => BaseStatusEnum::labels(),
            ])
            ->add('image', 'mediaImage', [
                'label'      => trans('core/base::forms.image'),
                'label_attr' => ['class' => 'control-label'],
            ])
            ->add('is_featured', 'onOff', [
                'label'         => trans('core/base::forms.is_featured'),
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => false,
            ])
            ->add('is_plus_cat', 'onOff', [
                'label'         => 'Is Plus Category?',
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => false,
            ])
            ->add('category_size_id', 'customSelect', [
                'label'         => 'Select Category Sizes',
                'label_attr'    => ['class' => 'control-label'],
                'attr'          => [
                    'class'    => 'select-search-full',
                    'multiple' => 'multiple'
                ],
                'choices'       => $category_sizes,
                'default_value' => old('category_size_id', $selectedCatSizes),
            ])
            ->add('impact_price', 'number', [
                'label'         => 'Impact Price',
                'label_attr'    => ['class' => 'control-label'],
                'attr'          => [
                    'placeholder' => 'Product Quantity',
                    'steps'       => 0.1,
                ],
                'default_value' => 0,
            ])
            ->add('product_unit_id', 'customSelect', [
                'label'      => 'Select Product Unit',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder' => 'Select Product Unit',
                    'class'       => 'select-search-full',
                ],
                'choices'    => $product_units,
            ])
            ->add('per_piece_qty', 'number', [
                'label'         => 'Per Piece Qty',
                'label_attr'    => ['class' => 'control-label required'],
                'attr'          => [
                    'placeholder' => 'Per Piece Qty',
                    'steps'       => 0.1,
                ],
                'default_value' => 0,
            ])->add('sku_initial', 'text', [
                'label'      => 'SKU Initial',
                'label_attr' => ['class' => 'control-label required', 'readonly'],
                'attr'       => [
                    'placeholder' => 'SKU Initial',
                    //'readonly'    => 'true',
                ],
            ])
            ->setBreakFieldPoint('status');
    }
}
