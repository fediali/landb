<?php

namespace Botble\Thread\Providers;

use Botble\Thread\Models\Thread;
use Botble\Thread\Repositories\Caches\ThreadCacheDecorator;
use Botble\Thread\Repositories\Eloquent\ThreadRepository;
use Botble\Thread\Repositories\Interfaces\ThreadInterface;
use Illuminate\Support\ServiceProvider;
use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
use Event;
use Language;
use Note;
use SeoHelper;
use SlugHelper;


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
            dashboard_menu()
                ->registerItem([
                    'id'          => 'cms-plugins-thread',
                    'priority'    => 5,
                    'parent_id'   => null,
                    'name'        => 'plugins/thread::thread.name',
                    'icon'        => 'fa fa-list',
                    'url'         => route('thread.index'),
                    'permissions' => ['thread.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-thread-list',
                    'priority'    => 1,
                    'parent_id'   => 'cms-plugins-thread',
                    'name'        => 'plugins/thread::thread.name',
                    'icon'        => null,
                    'url'         => route('thread.index'),
                    'permissions' => ['thread.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-packs',
                    'priority'    => 2,
                    'parent_id'   => 'cms-plugins-thread',
                    'name'        => 'plugins/packs::packs.name',
                    'icon'        => null,
                    'url'         => route('packs.index'),
                    'permissions' => ['packs.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-fits',
                    'priority'    => 3,
                    'parent_id'   => 'cms-plugins-thread',
                    'name'        => 'plugins/fits::fits.name',
                    'icon'        => null,
                    'url'         => route('fits.index'),
                    'permissions' => ['fits.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-rises',
                    'priority'    => 4,
                    'parent_id'   => 'cms-plugins-thread',
                    'name'        => 'plugins/rises::rises.name',
                    'icon'        => null,
                    'url'         => route('rises.index'),
                    'permissions' => ['rises.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-fabrics',
                    'priority'    => 5,
                    'parent_id'   => 'cms-plugins-thread',
                    'name'        => 'plugins/fabrics::fabrics.name',
                    'icon'        => null,
                    'url'         => route('fabrics.index'),
                    'permissions' => ['fabrics.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-seasons',
                    'priority'    => 6,
                    'parent_id'   => 'cms-plugins-thread',
                    'name'        => 'plugins/seasons::seasons.name',
                    'icon'        => null,
                    'url'         => route('seasons.index'),
                    'permissions' => ['seasons.index'],
                ])
                ->registerItem([
                    'id'          => 'cms-plugins-printdesigns',
                    'priority'    => 7,
                    'parent_id'   => 'cms-plugins-thread',
                    'name'        => 'plugins/printdesigns::printdesigns.name',
                    'icon'        => null,
                    'url'         => route('printdesigns.index'),
                    'permissions' => ['printdesigns.index'],
                ]);
        });

    }
}
