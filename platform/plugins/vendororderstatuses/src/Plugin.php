<?php

namespace Botble\Vendororderstatuses;

use Schema;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Schema::dropIfExists('vendororderstatuses');
    }
}
