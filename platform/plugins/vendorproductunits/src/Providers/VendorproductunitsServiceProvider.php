<?php

namespace Botble\Vendorproductunits\Providers;

use Botble\Vendorproductunits\Models\Vendorproductunits;
use Illuminate\Support\ServiceProvider;
use Botble\Vendorproductunits\Repositories\Caches\VendorproductunitsCacheDecorator;
use Botble\Vendorproductunits\Repositories\Eloquent\VendorproductunitsRepository;
use Botble\Vendorproductunits\Repositories\Interfaces\VendorproductunitsInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class VendorproductunitsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(VendorproductunitsInterface::class, function () {
            return new VendorproductunitsCacheDecorator(new VendorproductunitsRepository(new Vendorproductunits));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/vendorproductunits')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Vendorproductunits::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-vendorproductunits',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/vendorproductunits::vendorproductunits.name',
                'icon'        => 'fa fa-list',
                'url'         => route('vendorproductunits.index'),
                'permissions' => ['vendorproductunits.index'],
            ]);
        });
    }
}
