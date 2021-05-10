<?php

namespace Botble\Vendororderstatuses\Providers;

use Botble\Vendororderstatuses\Models\Vendororderstatuses;
use Illuminate\Support\ServiceProvider;
use Botble\Vendororderstatuses\Repositories\Caches\VendororderstatusesCacheDecorator;
use Botble\Vendororderstatuses\Repositories\Eloquent\VendororderstatusesRepository;
use Botble\Vendororderstatuses\Repositories\Interfaces\VendororderstatusesInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class VendororderstatusesServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(VendororderstatusesInterface::class, function () {
            return new VendororderstatusesCacheDecorator(new VendororderstatusesRepository(new Vendororderstatuses));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/vendororderstatuses')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Vendororderstatuses::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-vendororderstatuses',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/vendororderstatuses::vendororderstatuses.name',
                'icon'        => 'fa fa-list',
                'url'         => route('vendororderstatuses.index'),
                'permissions' => ['vendororderstatuses.index'],
            ]);
        });
    }
}
