<?php

namespace Botble\Inventory\Providers;

use Botble\Inventory\Models\Inventory;
use Illuminate\Support\ServiceProvider;
use Botble\Inventory\Repositories\Caches\InventoryCacheDecorator;
use Botble\Inventory\Repositories\Eloquent\InventoryRepository;
use Botble\Inventory\Repositories\Interfaces\InventoryInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class InventoryServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(InventoryInterface::class, function () {
            return new InventoryCacheDecorator(new InventoryRepository(new Inventory));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/inventory')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Inventory::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-inventory',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/inventory::inventory.name',
                'icon'        => 'fa fa-list',
                'url'         => route('inventory.index'),
                'permissions' => ['inventory.index'],
            ]);
        });
    }
}
