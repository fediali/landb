<?php

namespace Botble\Sourcing\Providers;

use Botble\Sourcing\Models\Sourcing;
use Illuminate\Support\ServiceProvider;
use Botble\Sourcing\Repositories\Caches\SourcingCacheDecorator;
use Botble\Sourcing\Repositories\Eloquent\SourcingRepository;
use Botble\Sourcing\Repositories\Interfaces\SourcingInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class SourcingServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(SourcingInterface::class, function () {
            return new SourcingCacheDecorator(new SourcingRepository(new Sourcing));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/sourcing')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Sourcing::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-sourcing',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/sourcing::sourcing.name',
                'icon'        => 'fa fa-list',
                'url'         => route('sourcing.index'),
                'permissions' => ['sourcing.index'],
            ]);
        });
    }
}
