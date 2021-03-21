<?php

namespace Botble\Threadorders\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class AddThreadOrderVariationFields extends FormField
{

  /**
   * {@inheritDoc}
   */
  protected function getTemplate()
  {
    return 'plugins/threadorders::addThreadOrderVariationFields';
  }
}
