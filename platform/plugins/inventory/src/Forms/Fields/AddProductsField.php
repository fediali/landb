<?php

namespace Botble\Inventory\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class AddProductsField extends FormField
{

  /**
   * {@inheritDoc}
   */
  protected function getTemplate()
  {
    return 'plugins/inventory::addProducts';
  }
}
