<?php

namespace Botble\Threadorders\Providers;

use Botble\Threadorders\Models\Threadorders;
use Illuminate\Support\ServiceProvider;
use Botble\Threadorders\Repositories\Caches\ThreadordersCacheDecorator;
use Botble\Threadorders\Repositories\Eloquent\ThreadordersRepository;
use Botble\Threadorders\Repositories\Interfaces\ThreadordersInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class ThreadordersServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(ThreadordersInterface::class, function () {
            return new ThreadordersCacheDecorator(new ThreadordersRepository(new Threadorders));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/threadorders')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Threadorders::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-threadorders',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/threadorders::threadorders.name',
                'icon'        => 'fa fa-list',
                'url'         => route('threadorders.index'),
                'permissions' => ['threadorders.index'],
            ]);
        });
    }
}
