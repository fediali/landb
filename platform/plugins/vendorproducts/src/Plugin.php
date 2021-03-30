<?php

namespace Botble\Vendorproducts;

use Schema;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('vendorproducts');
    }
}
