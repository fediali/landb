<?php

namespace Botble\Vendorproducts\Providers;

use Botble\Vendorproducts\Models\Vendorproducts;
use Illuminate\Support\ServiceProvider;
use Botble\Vendorproducts\Repositories\Caches\VendorproductsCacheDecorator;
use Botble\Vendorproducts\Repositories\Eloquent\VendorproductsRepository;
use Botble\Vendorproducts\Repositories\Interfaces\VendorproductsInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class VendorproductsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(VendorproductsInterface::class, function () {
            return new VendorproductsCacheDecorator(new VendorproductsRepository(new Vendorproducts));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/vendorproducts')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Vendorproducts::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-vendorproducts',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/vendorproducts::vendorproducts.name',
                'icon'        => 'fa fa-list',
                'url'         => route('vendorproducts.index'),
                'permissions' => ['vendorproducts.index'],
            ]);
        });
    }
}
