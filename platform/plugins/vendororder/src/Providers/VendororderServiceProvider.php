<?php

namespace Botble\Vendororder\Providers;

use Botble\Vendororder\Models\Vendororder;
use Illuminate\Support\ServiceProvider;
use Botble\Vendororder\Repositories\Caches\VendororderCacheDecorator;
use Botble\Vendororder\Repositories\Eloquent\VendororderRepository;
use Botble\Vendororder\Repositories\Interfaces\VendororderInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class VendororderServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(VendororderInterface::class, function () {
            return new VendororderCacheDecorator(new VendororderRepository(new Vendororder));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/vendororder')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Vendororder::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-vendororder',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/vendororder::vendororder.name',
                'icon'        => 'fa fa-list',
                'url'         => route('vendororder.index'),
                'permissions' => ['vendororder.index'],
            ]);
        });
    }
}
