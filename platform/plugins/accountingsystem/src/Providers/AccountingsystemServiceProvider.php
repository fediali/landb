<?php

namespace Botble\Accountingsystem\Providers;

use Botble\Accountingsystem\Models\Accountingsystem;
use Illuminate\Support\ServiceProvider;
use Botble\Accountingsystem\Repositories\Caches\AccountingsystemCacheDecorator;
use Botble\Accountingsystem\Repositories\Eloquent\AccountingsystemRepository;
use Botble\Accountingsystem\Repositories\Interfaces\AccountingsystemInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class AccountingsystemServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(AccountingsystemInterface::class, function () {
            return new AccountingsystemCacheDecorator(new AccountingsystemRepository(new Accountingsystem));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/accountingsystem')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Accountingsystem::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-accountingsystem',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/accountingsystem::accountingsystem.name',
                'icon'        => 'fa fa-list',
                'url'         => route('accountingsystem.index'),
                'permissions' => ['accountingsystem.index'],
            ]);
        });
    }
}
