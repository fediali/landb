<?php

namespace Botble\Orderstatuses\Providers;

use Botble\Orderstatuses\Models\Orderstatuses;
use Illuminate\Support\ServiceProvider;
use Botble\Orderstatuses\Repositories\Caches\OrderstatusesCacheDecorator;
use Botble\Orderstatuses\Repositories\Eloquent\OrderstatusesRepository;
use Botble\Orderstatuses\Repositories\Interfaces\OrderstatusesInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class OrderstatusesServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(OrderstatusesInterface::class, function () {
            return new OrderstatusesCacheDecorator(new OrderstatusesRepository(new Orderstatuses));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/orderstatuses')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Orderstatuses::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-orderstatuses',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/orderstatuses::orderstatuses.name',
                'icon'        => 'fa fa-list',
                'url'         => route('orderstatuses.index'),
                'permissions' => ['orderstatuses.index'],
            ]);
        });
    }
}
