<?php

namespace Botble\Packs\Providers;

use Botble\Packs\Models\Packs;
use Illuminate\Support\ServiceProvider;
use Botble\Packs\Repositories\Caches\PacksCacheDecorator;
use Botble\Packs\Repositories\Eloquent\PacksRepository;
use Botble\Packs\Repositories\Interfaces\PacksInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class PacksServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(PacksInterface::class, function () {
            return new PacksCacheDecorator(new PacksRepository(new Packs));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/packs')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        /*Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Packs::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-packs',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/packs::packs.name',
                'icon'        => 'fa fa-list',
                'url'         => route('packs.index'),
                'permissions' => ['packs.index'],
            ]);
        });*/
    }
}
