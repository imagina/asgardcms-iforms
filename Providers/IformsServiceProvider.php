<?php

namespace Modules\Iforms\Providers;

use Anhskohbo\NoCaptcha\NoCaptcha;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;
use Modules\Core\Events\BuildingSidebar;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Iforms\Presenters\FormPresenter;
use Modules\Iforms\Events\Handlers\RegisterIformsSidebar;
use Illuminate\Support\Facades\Config;

class IformsServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->app['events']->listen(BuildingSidebar::class, RegisterIformsSidebar::class);

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('forms', Arr::dot(trans('iforms::forms')));
            $event->load('fields', Arr::dot(trans('iforms::fields')));
            $event->load('leads', Arr::dot(trans('iforms::leads')));
        });

    }

    public function boot()
    {
        $this->publishConfig('iforms', 'permissions');
        $this->publishConfig('iforms', 'config');
        $this->publishConfig('iforms', 'settings');
        $this->publishConfig('iforms', 'settings-fields');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $app = $this->app;

        $app['validator']->extend('formCaptcha', function ($attribute, $value) use ($app) {
            return $app['formCaptcha']->verifyResponse($value, $app['request']->getClientIp());
        });
        $this->registerComponents();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('Iforms');
    }

    private function registerBindings()
    {
        $this->app->bind(
            'Modules\Iforms\Repositories\FormRepository',
            function () {
                $repository = new \Modules\Iforms\Repositories\Eloquent\EloquentFormRepository(new \Modules\Iforms\Entities\Form());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Iforms\Repositories\Cache\CacheFormDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Iforms\Repositories\FieldRepository',
            function () {
                $repository = new \Modules\Iforms\Repositories\Eloquent\EloquentFieldRepository(new \Modules\Iforms\Entities\Field());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Iforms\Repositories\Cache\CacheFieldDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Iforms\Repositories\LeadRepository',
            function () {
                $repository = new \Modules\Iforms\Repositories\Eloquent\EloquentLeadRepository(new \Modules\Iforms\Entities\Lead());

                if (! config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Iforms\Repositories\Cache\CacheLeadDecorator($repository);
            }
        );
// add bindings

        $this->app->bind('Modules\Iforms\Presenters\FormPresenter');

    }

    /**
     * Register Blade components
     */

    private function registerComponents(){
        Blade::componentNamespace("Modules\Iforms\View\Components", 'iforms');
    }
}
