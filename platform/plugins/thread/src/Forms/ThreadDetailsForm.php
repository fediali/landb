<?php

namespace Botble\Thread\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Thread\Forms\Fields\AddDenimFields;
use Botble\Thread\Forms\Fields\CommentBox;
use Botble\Thread\Forms\Fields\ThreadDetails;
use Botble\Thread\Forms\Fields\ThreadVariations;
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
      $variations = get_thread_variations($this->model->id);
      $comments = get_thread_comments($this->model->id);
      $seasons = get_seasons();
      $fits = get_fits();
      $rises = get_rises();
      $fabrics = get_fabrics();
      //dd($fits);
      //dd($comments);
        $this->formHelper->addCustomField('threadDetails', ThreadDetails::class);
        $this->formHelper->addCustomField('threadVariations', ThreadVariations::class);
        $this->formHelper->addCustomField('CommentBox', CommentBox::class);
        $this->setupModel(new Thread)
            ->setValidatorClass(ThreadRequest::class)
            ->withCustomFields()
            ->add('Details', 'threadDetails', [
                'label'      => 'Details',
                'data' => ['thread' => $this->model, 'printdesigns' => $printdesigns, 'variations' => $variations, 'seasons' => $seasons, 'rises' => $rises, 'fits' => $fits, 'fabrics' => $fabrics]
                ]
            )->add('Variations', 'threadVariations', [
                'label'      => 'threadVariations',
                'data' => ['thread' => $this->model, 'printdesigns' => $printdesigns, 'variations' => $variations]
                ]
            )->add('Comments', 'CommentBox', [
                'label'      => 'CommentBox',
                'data' => ['thread' => $this->model, 'printdesigns' => $printdesigns, 'variations' => $variations, 'comments' => $comments]
                ]
            );
    }
}
