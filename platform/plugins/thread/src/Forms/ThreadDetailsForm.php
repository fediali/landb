<?php

namespace Botble\Thread\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\ProductCategory;
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
      $wash = get_washes();

      $selectedRegCat = $selectedPluCat =  $reg_cat = $plus_cat = null;
      $reg_sku = $plus_sku = '';
      if($this->getModel()){
        $selectedRegCat = $this->getModel()->regular_product_categories()->first(['product_category_id', 'sku']);
        $selectedPluCat = $this->getModel()->plus_product_categories()->first(['product_category_id', 'sku']);

        if(!empty($selectedRegCat->product_category_id)){
          $reg_cat = ProductCategory::with('category_sizes')->find($selectedRegCat->product_category_id);
        }if(!empty($selectedPluCat->product_category_id)){
          $plus_cat = ProductCategory::with('category_sizes')->find($selectedPluCat->product_category_id);
        }

        $reg_sku = @$selectedRegCat->sku;
        $plus_sku = @$selectedPluCat->sku;
      }

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
                'data' => ['thread' => $this->model, 'printdesigns' => $printdesigns, 'variations' => $variations, 'seasons' => $seasons, 'rises' => $rises, 'fits' => $fits, 'fabrics' => $fabrics, 'washes' => $wash, 'reg_cat' => $reg_cat, 'plus_cat' => $plus_cat, 'reg_sku' => $reg_sku, 'plus_sku' => $plus_sku]
                ]
            )->add('Variations', 'threadVariations', [
                'label'      => 'threadVariations',
                'data' => ['thread' => $this->model, 'printdesigns' => $printdesigns, 'variations' => $variations, 'fabrics' => $fabrics,'washes' => $wash, 'reg_cat' => $reg_cat, 'plus_cat' => $plus_cat]
                ]
            )->add('Comments', 'CommentBox', [
                'label'      => 'CommentBox',
                'data' => ['thread' => $this->model, 'printdesigns' => $printdesigns, 'variations' => $variations, 'comments' => $comments]
                ]
            );
    }
}
