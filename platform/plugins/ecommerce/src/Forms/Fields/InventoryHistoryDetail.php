<?php

namespace Botble\Ecommerce\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class InventoryHistoryDetail extends FormField
{

    /**
     * {@inheritDoc}
     */
    protected function getTemplate()
    {
        return 'plugins/ecommerce::products.partials.inventory_history';
    }
}
