<?php

namespace Botble\Threadsample\Providers;

use Botble\Threadsample\Models\Threadsample;
use Illuminate\Support\ServiceProvider;
use Botble\Threadsample\Repositories\Caches\ThreadsampleCacheDecorator;
use Botble\Threadsample\Repositories\Eloquent\ThreadsampleRepository;
use Botble\Threadsample\Repositories\Interfaces\ThreadsampleInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class ThreadsampleServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(ThreadsampleInterface::class, function () {
            return new ThreadsampleCacheDecorator(new ThreadsampleRepository(new Threadsample));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/threadsample')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Threadsample::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-threadsample',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/threadsample::threadsample.name',
                'icon'        => 'fa fa-list',
                'url'         => route('threadsample.index'),
                'permissions' => ['threadsample.index'],
            ]);
        });
    }
}
