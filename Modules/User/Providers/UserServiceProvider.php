<?php

namespace Modules\User\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\User\Services\PcUserService;
use Modules\User\Services\UserService;
use Modules\User\Services\BackendUserService;
use Modules\User\Services\UserStatisticsService;
use Modules\User\Services\UserWalletService;


class UserServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->addFacade();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('UserService', function () {
            return new UserService();
        });
        $this->app->singleton('BackendUserService', function () {
            return new BackendUserService();
        });
        $this->app->singleton('UserWalletService', function() {
            return new UserWalletService();
        });
        $this->app->singleton('UserStatisticsService', function() {
            return new UserStatisticsService();
        });
        $this->app->singleton('PcUserService', function() {
            return new PcUserService();
        });

    }

    public function addFacade()
    {
        $loader = AliasLoader::getInstance();

        $loader->alias('BackendUserService', \Modules\User\Facades\BackendUserFacade::class);
        $loader->alias('UserService', \Modules\User\Facades\UserFacade::class);
        $loader->alias('UserWalletService', \Modules\User\Facades\UserWalletFacade::class);
        $loader->alias('UserStatisticsService', \Modules\User\Facades\UserStatisticsFacade::class);
        $loader->alias('PcUserService', \Modules\User\Facades\PcUserFacade::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('user.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'user'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/user');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/user';
        }, \Config::get('view.paths')), [$sourcePath]), 'user');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/user');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'user');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'user');
        }
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (!app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
