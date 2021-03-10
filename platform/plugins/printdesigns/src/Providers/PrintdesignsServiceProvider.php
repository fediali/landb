<?php

namespace Botble\Printdesigns\Providers;

use Botble\Printdesigns\Models\Printdesigns;
use Illuminate\Support\ServiceProvider;
use Botble\Printdesigns\Repositories\Caches\PrintdesignsCacheDecorator;
use Botble\Printdesigns\Repositories\Eloquent\PrintdesignsRepository;
use Botble\Printdesigns\Repositories\Interfaces\PrintdesignsInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class PrintdesignsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(PrintdesignsInterface::class, function () {
            return new PrintdesignsCacheDecorator(new PrintdesignsRepository(new Printdesigns));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/printdesigns')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        /*Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Printdesigns::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-printdesigns',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/printdesigns::printdesigns.name',
                'icon'        => 'fa fa-list',
                'url'         => route('printdesigns.index'),
                'permissions' => ['printdesigns.index'],
            ]);
        });*/
    }
}
