<?php

namespace Botble\Thread\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Thread\Forms\Fields\AddDenimFields;
use Botble\Thread\Forms\Fields\ThreadDetails;
use Botble\Thread\Http\Requests\ThreadRequest;
use Botble\Thread\Models\Thread;

class ThreadDetailsForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
      $printdesigns = get_print_designs();
        $this->formHelper->addCustomField('threadDetails', ThreadDetails::class);
        $this->setupModel(new Thread)
            ->setValidatorClass(ThreadRequest::class)
            ->withCustomFields()
            ->add('Details', 'threadDetails', [
                'label'      => 'Details',
                'data' => ['thread' => $this->model, 'printdesigns' => $printdesigns]
                ]
            );
    }
}
