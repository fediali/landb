<?php

namespace Botble\Categorysizes\Providers;

use Botble\Categorysizes\Models\Categorysizes;
use Illuminate\Support\ServiceProvider;
use Botble\Categorysizes\Repositories\Caches\CategorysizesCacheDecorator;
use Botble\Categorysizes\Repositories\Eloquent\CategorysizesRepository;
use Botble\Categorysizes\Repositories\Interfaces\CategorysizesInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class CategorysizesServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(CategorysizesInterface::class, function () {
            return new CategorysizesCacheDecorator(new CategorysizesRepository(new Categorysizes));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/categorysizes')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        /*Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Categorysizes::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-categorysizes',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/categorysizes::categorysizes.name',
                'icon'        => 'fa fa-list',
                'url'         => route('categorysizes.index'),
                'permissions' => ['categorysizes.index'],
            ]);
        });*/
    }
}
