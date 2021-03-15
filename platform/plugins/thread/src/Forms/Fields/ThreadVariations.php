<?php

namespace Botble\Thread\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class ThreadVariations extends FormField
{

  /**
   * {@inheritDoc}
   */
  protected function getTemplate()
  {
    return 'plugins/thread::threadVariations';
  }
}
