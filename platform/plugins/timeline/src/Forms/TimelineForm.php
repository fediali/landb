<?php

namespace Botble\Timeline\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Timeline\Http\Requests\TimelineRequest;
use Botble\Timeline\Models\Timeline;

class TimelineForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $this
            ->setupModel(new Timeline)
            ->setValidatorClass(TimelineRequest::class)
            ->withCustomFields()
            ->add('product_link', 'text', [
                'label'      => 'Product Link',
                'label_attr' => ['class' => 'control-label required cloneItem'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])->add('product_desc', 'textarea', [
                'label'      => 'Description',
                'label_attr' => ['class' => 'control-label required cloneItem'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])->add('file', 'mediaImage', [
                'label'      => 'Select File',
                'label_attr' => ['class' => 'control-label cloneItem'],
            ])->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
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
                'label'      => 'Date',
                'label_attr' => ['class' => 'control-label'],
            ])->add('clone', 'button', [
                'label' => 'Add More',
                'attr'  => [
                    'class' => 'btn btn-info cloneTimeline',

                ],
            ])
            ->setBreakFieldPoint('name');
    }
}
