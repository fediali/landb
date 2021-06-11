<?php

namespace Botble\Chating\Providers;

use Botble\Chating\Models\Chating;
use Illuminate\Support\ServiceProvider;
use Botble\Chating\Repositories\Caches\ChatingCacheDecorator;
use Botble\Chating\Repositories\Eloquent\ChatingRepository;
use Botble\Chating\Repositories\Interfaces\ChatingInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class ChatingServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(ChatingInterface::class, function () {
            return new ChatingCacheDecorator(new ChatingRepository(new Chating));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/chating')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Chating::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-chating',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/chating::chating.name',
                'icon'        => 'fa fa-list',
                'url'         => route('chating.chatRoom'),
                'permissions' => ['chating.index'],
            ]);
        });
    }
}
