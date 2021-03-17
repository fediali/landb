<?php

namespace Botble\Wash\Providers;

use Botble\Wash\Models\Wash;
use Illuminate\Support\ServiceProvider;
use Botble\Wash\Repositories\Caches\WashCacheDecorator;
use Botble\Wash\Repositories\Eloquent\WashRepository;
use Botble\Wash\Repositories\Interfaces\WashInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class WashServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(WashInterface::class, function () {
            return new WashCacheDecorator(new WashRepository(new Wash));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/wash')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Wash::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-wash',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/wash::wash.name',
                'icon'        => 'fa fa-list',
                'url'         => route('wash.index'),
                'permissions' => ['wash.index'],
            ]);
        });
    }
}
