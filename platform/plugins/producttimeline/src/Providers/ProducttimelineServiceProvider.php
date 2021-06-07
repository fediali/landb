<?php

namespace Botble\Producttimeline\Providers;

use Botble\Producttimeline\Models\Producttimeline;
use Illuminate\Support\ServiceProvider;
use Botble\Producttimeline\Repositories\Caches\ProducttimelineCacheDecorator;
use Botble\Producttimeline\Repositories\Eloquent\ProducttimelineRepository;
use Botble\Producttimeline\Repositories\Interfaces\ProducttimelineInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class ProducttimelineServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(ProducttimelineInterface::class, function () {
            return new ProducttimelineCacheDecorator(new ProducttimelineRepository(new Producttimeline));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/producttimeline')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Producttimeline::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-producttimeline',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/producttimeline::producttimeline.name',
                'icon'        => 'fa fa-list',
                'url'         => route('producttimeline.index'),
                'permissions' => ['producttimeline.index'],
            ]);
        });
    }
}
