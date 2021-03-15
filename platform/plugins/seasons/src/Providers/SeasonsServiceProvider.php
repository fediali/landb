<?php

namespace Botble\Seasons\Providers;

use Botble\Seasons\Models\Seasons;
use Illuminate\Support\ServiceProvider;
use Botble\Seasons\Repositories\Caches\SeasonsCacheDecorator;
use Botble\Seasons\Repositories\Eloquent\SeasonsRepository;
use Botble\Seasons\Repositories\Interfaces\SeasonsInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class SeasonsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(SeasonsInterface::class, function () {
            return new SeasonsCacheDecorator(new SeasonsRepository(new Seasons));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/seasons')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        /*Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Seasons::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-seasons',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/seasons::seasons.name',
                'icon'        => 'fa fa-list',
                'url'         => route('seasons.index'),
                'permissions' => ['seasons.index'],
            ]);
        });*/
    }
}
