<?php

namespace Botble\Threadvariationsamples\Providers;

use Botble\Threadvariationsamples\Models\Threadvariationsamples;
use Illuminate\Support\ServiceProvider;
use Botble\Threadvariationsamples\Repositories\Caches\ThreadvariationsamplesCacheDecorator;
use Botble\Threadvariationsamples\Repositories\Eloquent\ThreadvariationsamplesRepository;
use Botble\Threadvariationsamples\Repositories\Interfaces\ThreadvariationsamplesInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class ThreadvariationsamplesServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(ThreadvariationsamplesInterface::class, function () {
            return new ThreadvariationsamplesCacheDecorator(new ThreadvariationsamplesRepository(new Threadvariationsamples));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/threadvariationsamples')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Threadvariationsamples::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-threadvariationsamples',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/threadvariationsamples::threadvariationsamples.name',
                'icon'        => 'fa fa-list',
                'url'         => route('threadvariationsamples.index'),
                'permissions' => ['threadvariationsamples.index'],
            ]);
        });
    }
}
