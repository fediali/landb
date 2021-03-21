<?php

namespace Botble\Rises\Providers;

use Botble\Rises\Models\Rises;
use Illuminate\Support\ServiceProvider;
use Botble\Rises\Repositories\Caches\RisesCacheDecorator;
use Botble\Rises\Repositories\Eloquent\RisesRepository;
use Botble\Rises\Repositories\Interfaces\RisesInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class RisesServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(RisesInterface::class, function () {
            return new RisesCacheDecorator(new RisesRepository(new Rises));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/rises')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        /*Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Rises::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-rises',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/rises::rises.name',
                'icon'        => 'fa fa-list',
                'url'         => route('rises.index'),
                'permissions' => ['rises.index'],
            ]);
        });*/
    }
}
