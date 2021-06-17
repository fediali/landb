<?php

namespace Botble\Timeline\Providers;

use Botble\Timeline\Models\Timeline;
use Illuminate\Support\ServiceProvider;
use Botble\Timeline\Repositories\Caches\TimelineCacheDecorator;
use Botble\Timeline\Repositories\Eloquent\TimelineRepository;
use Botble\Timeline\Repositories\Interfaces\TimelineInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class TimelineServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(TimelineInterface::class, function () {
            return new TimelineCacheDecorator(new TimelineRepository(new Timeline));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/timeline')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Timeline::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-timeline',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/timeline::timeline.name',
                'icon'        => 'fa fa-list',
                'url'         => route('timeline.index'),
                'permissions' => ['timeline.index'],
            ]);
        });
    }
}
