<?php

namespace Botble\Ecommerce\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\Fields\MultiCheckListField;
use Botble\Base\Forms\Fields\TagField;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Forms\Fields\CategoryMultiField;
use Botble\Ecommerce\Forms\Fields\InventoryHistoryDetail;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCollectionInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductLabelInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface;
use Botble\Ecommerce\Repositories\Interfaces\TaxInterface;
use EcommerceHelper;
use Illuminate\Support\Facades\Auth;

class ProductForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $this->formHelper->addCustomField('inventory_history', InventoryHistoryDetail::class);
        $selectedCategories = [];
        if ($this->getModel()) {
            $selectedCategories = $this->getModel()->categories()->pluck('category_id')->all();
        }

        $brands = app(BrandInterface::class)->pluck('name', 'id');

        $brands = [0 => trans('plugins/ecommerce::brands.no_brand')] + $brands;

        $productCollections = app(ProductCollectionInterface::class)->pluck('name', 'id');


        $selectedProductCollections = [];
        if ($this->getModel()) {
            $selectedProductCollections = $this->getModel()->productCollections()->pluck('product_collection_id')
                ->all();
        }
        $productLabels = app(ProductLabelInterface::class)->pluck('name', 'id');
        $selectedProductLabels = [];
        if ($this->getModel()) {
            $selectedProductLabels[] = $this->getModel()->product_label_id;
        }

        $productId = $this->getModel() ? $this->getModel()->id : null;
        $productAttributeSets = app(ProductAttributeSetInterface::class)->getAllWithSelected($productId);
        $productAttributes = app(ProductAttributeInterface::class)->getAllWithSelected($productId);


        $productVariations = [];
        $productVariationsInfo = [];
        $productsRelatedToVariation = [];

        if ($this->getModel()) {
            $products = get_products_data($this->getModel()->id);
            $productVariations = app(ProductVariationInterface::class)->allBy([
                'configurable_product_id' => $this->getModel()->id,
            ]);

            $productVariationsInfo = app(ProductVariationItemInterface::class)
                ->getVariationsInfo($productVariations->pluck('id')->toArray());

            $productsRelatedToVariation = app(ProductInterface::class)->getProductVariations($productId);
        } else {
            $products = get_products_data();
        }

        $tags = null;

        if ($this->getModel()) {
            $tags = $this->getModel()->tags()->pluck('name')->all();
            $tags = implode(',', $tags);
        }

        $this
            ->setupModel(new Product)
            ->setValidatorClass(ProductRequest::class)
            ->withCustomFields()
            ->addCustomField('categoryMulti', CategoryMultiField::class)
            ->addCustomField('multiCheckList', MultiCheckListField::class)
            ->addCustomField('tags', TagField::class)
            ->add('name', 'text', [
                'label'      => trans('plugins/ecommerce::products.form.name'),
                'label_attr' => ['class' => 'text-title-field required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('description', 'editor', [
                'label'      => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'rows'         => 2,
                    'placeholder'  => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 1000,
                ],
            ])
            ->add('content', 'editor', [
                'label'      => trans('plugins/ecommerce::products.form.content'),
                'label_attr' => ['class' => 'text-title-field'],
                'attr'       => [
                    'rows'            => 4,
                    'with-short-code' => true,
                ],
            ])
            ->addMetaBoxes([
                'with_related' => [
                    'title'    => null,
                    'content'  => '<div class="wrap-relation-product" data-target="' . route('products.get-relations-boxes',
                            $productId ? $productId : 0) . '"></div>',
                    'wrap'     => false,
                    'priority' => 9999,
                ],
            ])
            ->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'choices'    => BaseStatusEnum::$PRODUCT,
            ])->add('oos_date', 'text', [
                'label'      => 'Out of Stock Date',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-controlco',
                    'readonly']
            ])
            ->add('color_print', 'mediaImages', [
                'label'      => 'Color Print',
                'label_attr' => ['class' => 'control-label'],
                'values'     => $productId ? $this->getModel()->color_print : '',
            ])
            ->add('color_products[]', 'multiCheckList', [
                'label'      => 'Color Products',
                'label_attr' => ['class' => 'control-label'],
                'choices'    => $products,
                'value'      => old('color_products', (!is_null($this->getModel()->color_products) ? json_decode($this->getModel()->color_products) : [])),
            ])
            ->add('inventory_history', 'inventory_history', [
                'label'         => 'Inventory History',
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => false,
                'id'            => $productId ? $productId : 0
            ])
            ->add('creation_date', 'text', [
                'label'         => 'Creation date',
                'label_attr'    => ['class' => 'control-label'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'd M, yyyy',
                ],
                'default_value' => now(config('app.timezone'))->format('d M, Y'),
                'value'         => $this->model->creation_date ? date('d M, Y', strtotime($this->model->creation_date)) : now(config('app.timezone'))->format('d M, Y')
            ])
            ->add('is_featured', 'onOff', [
                'label'         => trans('core/base::forms.is_featured'),
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => false,
            ])
            ->add('categories[]', 'categoryMulti', [
                'label'      => trans('plugins/ecommerce::products.form.categories'),
                'label_attr' => ['class' => 'control-label'],
                'choices'    => get_product_categories_with_children(),
                'value'      => old('categories', $selectedCategories),
            ])
//            ->add('brand_id', 'customSelect', [
//                'label'      => trans('plugins/ecommerce::products.form.brand'),
//                'label_attr' => ['class' => 'control-label'],
//                'choices'    => $brands,
//            ])
            ->add('product_collections[]', 'multiCheckList', [
                'label'      => trans('plugins/ecommerce::products.form.label'),
                'label_attr' => ['class' => 'control-label'],
                'choices'    => $productCollections,
                'value'      => old('product_collections', $selectedProductCollections),
            ])
            ->add('product_label_id', 'multiCheckList', [
                'label'      => 'Product Label',
                'label_attr' => ['class' => 'control-label'],
                'choices'    => $productLabels,
                'value'      => old('product_labels', $selectedProductLabels),

            ]);
//        if (EcommerceHelper::isTaxEnabled()) {
//            $taxes = app(TaxInterface::class)->pluck('title', 'id');
//
//            $taxes = [0 => trans('plugins/ecommerce::tax.select_tax')] + $taxes;
//
//            $this->add('tax_id', 'customSelect', [
//                'label'      => trans('plugins/ecommerce::products.form.tax'),
//                'label_attr' => ['class' => 'control-label'],
//                'choices'    => $taxes,
//            ]);
//        }

        $this
            ->add('tag', 'tags', [
                'label'      => trans('plugins/ecommerce::products.form.tags'),
                'label_attr' => ['class' => 'control-label'],
                'value'      => $tags,
                'attr'       => [
                    'placeholder' => trans('plugins/ecommerce::products.form.write_some_tags'),
                    'data-url'    => route('product-tag.all'),
                ],
            ])->add('sizes', 'text', [
                'label'      => 'Sizes',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder' => 'Sizes',
                ],
            ])
            /*->add('eta_pre_product', 'date', [
                'label'      => 'ETA',
                'label_attr' => ['class' => 'control-label eta_pre_product'],
                'attr'       => [
                    'class' => 'eta_pre_product',
                ],
            ])*/
            ->add('eta_pre_product', 'text', [
                'label'         => 'ETA',
                'label_attr'    => ['class' => 'control-label'],
                'attr'          => [
                    'class'            => 'form-control datepicker',
                    'data-date-format' => 'd M, yyyy',
                ],
                'default_value' => now(config('app.timezone'))->format('d M, Y'),
                'value'         => $this->model->eta_pre_product ? date('d M, Y', strtotime($this->model->eta_pre_product)) : now(config('app.timezone'))->format('d M, Y')
            ])
            ->add('prod_pieces', 'number', [
                'label'      => 'Pack Product Pieces',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'placeholder' => 'Pack Product Pieces',
                ],
            ])
            ->setBreakFieldPoint('status');

        if (empty($productVariations) || $productVariations->isEmpty()) {

            $this
                ->removeMetaBox('variations')
                ->addAfter('content', 'images[]', 'mediaImages', [
                    'label'      => trans('plugins/ecommerce::products.form.image'),
                    'label_attr' => ['class' => 'control-label'],
                    'values'     => $productId ? $this->getModel()->images : [],
                ])
                ->addMetaBoxes([
                    'general'    => [
                        'title'          => trans('plugins/ecommerce::products.overview'),
                        'content'        => view('plugins/ecommerce::products.partials.general',
                            ['product' => $productId ? $this->getModel() : null])->render(),
                        'before_wrapper' => '<div id="main-manage-product-type">',
                        'priority'       => 2,
                    ],
                    'attributes' => [
                        'title'         => trans('plugins/ecommerce::products.attributes'),
                        'content'       => view('plugins/ecommerce::products.partials.add-product-attributes', [
                            'productAttributeSets' => $productAttributeSets,
                            'productAttributes'    => $productAttributes,
                            'product'              => $productId,
                        ])->render(),
                        'after_wrapper' => '</div>',
                        'priority'      => 3,
                    ],
                ]);
        } elseif ($productId) {

            $this
                ->removeMetaBox('general')
                ->removeMetaBox('attributes')
                ->addMetaBoxes([
                    'variations' => [
                        'title'          => trans('plugins/ecommerce::products.product_has_variations'),
                        'content'        => view('plugins/ecommerce::products.partials.configurable', [
                            'productAttributeSets'       => $productAttributeSets,
                            'productAttributes'          => $productAttributes,
                            'productVariations'          => $productVariations,
                            'productVariationsInfo'      => $productVariationsInfo,
                            'productsRelatedToVariation' => $productsRelatedToVariation,
                            'product'                    => $this->getModel(),
                        ])->render(),
                        'before_wrapper' => '<div id="main-manage-product-type">',
                        'after_wrapper'  => '</div>',
                        'priority'       => 4,
                    ],
                ]);
        }
    }
}
