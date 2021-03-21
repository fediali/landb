<?php

namespace Botble\Fabrics\Providers;

use Botble\Fabrics\Models\Fabrics;
use Illuminate\Support\ServiceProvider;
use Botble\Fabrics\Repositories\Caches\FabricsCacheDecorator;
use Botble\Fabrics\Repositories\Eloquent\FabricsRepository;
use Botble\Fabrics\Repositories\Interfaces\FabricsInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class FabricsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(FabricsInterface::class, function () {
            return new FabricsCacheDecorator(new FabricsRepository(new Fabrics));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/fabrics')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        /*Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Fabrics::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-fabrics',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/fabrics::fabrics.name',
                'icon'        => 'fa fa-list',
                'url'         => route('fabrics.index'),
                'permissions' => ['fabrics.index'],
            ]);
        });*/
    }
}
