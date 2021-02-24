<?php

namespace Botble\Fits\Providers;

use Botble\Fits\Models\Fits;
use Illuminate\Support\ServiceProvider;
use Botble\Fits\Repositories\Caches\FitsCacheDecorator;
use Botble\Fits\Repositories\Eloquent\FitsRepository;
use Botble\Fits\Repositories\Interfaces\FitsInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class FitsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(FitsInterface::class, function () {
            return new FitsCacheDecorator(new FitsRepository(new Fits));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/fits')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Fits::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-fits',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/fits::fits.name',
                'icon'        => 'fa fa-list',
                'url'         => route('fits.index'),
                'permissions' => ['fits.index'],
            ]);
        });
    }
}
