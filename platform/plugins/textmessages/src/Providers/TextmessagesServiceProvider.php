<?php

namespace Botble\Textmessages\Providers;

use Botble\Textmessages\Models\Textmessages;
use Illuminate\Support\ServiceProvider;
use Botble\Textmessages\Repositories\Caches\TextmessagesCacheDecorator;
use Botble\Textmessages\Repositories\Eloquent\TextmessagesRepository;
use Botble\Textmessages\Repositories\Interfaces\TextmessagesInterface;
use Botble\Base\Supports\Helper;
use Event;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;

class TextmessagesServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind(TextmessagesInterface::class, function () {
            return new TextmessagesCacheDecorator(new TextmessagesRepository(new Textmessages));
        });

        Helper::autoload(__DIR__ . '/../../helpers');
    }

    public function boot()
    {
        $this->setNamespace('plugins/textmessages')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadMigrations()
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes(['web']);

        Event::listen(RouteMatched::class, function () {
            if (defined('LANGUAGE_MODULE_SCREEN_NAME')) {
                \Language::registerModule([Textmessages::class]);
            }

            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-textmessages',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'plugins/textmessages::textmessages.name',
                'icon'        => 'fa fa-list',
                'url'         => route('textmessages.index'),
                'permissions' => ['textmessages.index'],
            ]);
        });
    }
}
