<?php

namespace Botble\Thread\Providers;

use Botble\Thread\Models\Thread;
use Illuminate\Support\ServiceProvider;
use Botble\Thread\Repositories\Caches\ThreadCacheDecorator;
use Botble\Thread\Repositories\Eloquent\ThreadRepository;
use Botble\Thread\Repositories\Interfaces\ThreadInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class ThreadServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(ThreadInterface::class, function () {
            return new ThreadCacheDecorator(new ThreadRepository(new Thread));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/thread')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Thread::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-thread',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/thread::thread.name',
                'icon'        => 'fa fa-list',
                'url'         => route('thread.index'),
                'permissions' => ['thread.index'],
            ]);
        });
    }
}
