<?php

namespace Botble\Printdesigns\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Printdesigns\Http\Requests\PrintdesignsRequest;
use Botble\Printdesigns\Models\Printdesigns;

class PrintdesignsForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $designers = get_designers_for_thread();
        $designers = [0 => trans('plugins/blog::categories.none')] + $designers;

        $this
            ->setupModel(new Printdesigns)
            ->setValidatorClass(PrintdesignsRequest::class)
            ->withCustomFields()
            ->add('designer_id', 'customSelect', [
                'label'      => 'Select Designer',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'select-search-full',
                ],
                'choices'    => $designers,
            ])
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('sku', 'text', [
                'label'      => 'SKU',
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => 'SKU',
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
            ])
            ->add('file', 'mediaImage', [
                'label'      => 'Select File',
                'label_attr' => ['class' => 'control-label'],
            ])
            ->setBreakFieldPoint('status');
    }
}
