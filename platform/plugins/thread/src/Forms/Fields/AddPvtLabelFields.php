<?php

namespace Botble\Thread\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class AddPvtLabelFields extends FormField
{

  /**
   * {@inheritDoc}
   */
  protected function getTemplate()
  {
    return 'plugins/thread::addPvtLabelFields';
  }
}
