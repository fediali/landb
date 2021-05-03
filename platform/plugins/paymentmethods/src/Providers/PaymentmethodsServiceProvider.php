<?php

namespace Botble\Paymentmethods\Providers;

use Botble\Paymentmethods\Models\Paymentmethods;
use Illuminate\Support\ServiceProvider;
use Botble\Paymentmethods\Repositories\Caches\PaymentmethodsCacheDecorator;
use Botble\Paymentmethods\Repositories\Eloquent\PaymentmethodsRepository;
use Botble\Paymentmethods\Repositories\Interfaces\PaymentmethodsInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class PaymentmethodsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(PaymentmethodsInterface::class, function () {
            return new PaymentmethodsCacheDecorator(new PaymentmethodsRepository(new Paymentmethods));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/paymentmethods')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Paymentmethods::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-paymentmethods',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/paymentmethods::paymentmethods.name',
                'icon'        => 'fa fa-list',
                'url'         => route('paymentmethods.index'),
                'permissions' => ['paymentmethods.index'],
            ]);
        });
    }
}
