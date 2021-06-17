<?php

namespace Botble\Threadsample\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Threadsample\Http\Requests\ThreadsampleRequest;
use Botble\Threadsample\Models\Threadsample;

class ThreadsampleForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $thread = get_thread();

        $this
            ->setupModel(new Threadsample)
            ->setValidatorClass(ThreadsampleRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label'      => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'placeholder'  => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])->add('notes', 'textarea', [
                'label'      => 'Notes',
                'label_attr' => ['class' => 'control-label'],
            ])
            ->add('status', 'customSelect', [
                'label'      => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'attr'       => [
                    'class' => 'form-control select-full',
                ],
                'choices'    => BaseStatusEnum::$THREADSAMPLE,
            ])->add('thread_id', 'button', [
                'label'      => 'View Thread',
                'label_attr' => ['class' => 'control-label '],
                'attr'       => [
                    'class' => 'form-control btn btn-info threadSampletech',
                    'value' => isset($this->model->thread_id) ? route('thread.details', $this->model->thread_id) : '',
                ],

            ])
            ->setBreakFieldPoint('status');
    }
}

