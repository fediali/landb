<?php

namespace Botble\Textmessages\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class AddCustomerFields extends FormField
{

  /**
   * {@inheritDoc}
   */
  protected function getTemplate()
  {
    return 'plugins/textmessages::addCustomerFields';
  }
}




