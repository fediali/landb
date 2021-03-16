<?php

namespace Botble\Thread\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Thread\Forms\Fields\AddThreadOrderFields;
use Botble\Thread\Http\Requests\ThreadRequest;
use Botble\Thread\Models\ThreadOrder;

class ThreadOrderForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        $this->formHelper->addCustomField('addThreadOrderFields', AddThreadOrderFields::class);
        $this
            ->setupModel(new ThreadOrder)
            ->setValidatorClass(ThreadRequest::class)
            ->withCustomFields()
            ->add('ThreadOrderFields', 'addThreadOrderFields', [
                'label' => 'Thread Order Fields',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => 'Thread Order Fields',
                ],
                'data' => $this->model
            ]);
    }
}
