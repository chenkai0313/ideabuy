<?php

namespace Modules\Goods\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Goods\Services\BackendGoodsService;
use Modules\Goods\Services\PcGoodsService;

class GoodsServiceProvider extends ServiceProvider
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
        $this->addFacade();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
    }
    /**
     * 门面注册
     */
    public function addFacade()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('BackendGoodsService', \Modules\Goods\Facades\BackendGoodsFacade::class);
        $loader->alias('PcGoodsService', \Modules\Goods\Facades\PcGoodsFacade::class);

    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('BackendGoodsService',function(){
            return new BackendGoodsService();
        });
        $this->app->singleton('PcGoodsService',function(){
            return new PcGoodsService();
        });
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('goods.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'goods'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/goods');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/goods';
        }, \Config::get('view.paths')), [$sourcePath]), 'goods');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/goods');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'goods');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'goods');
        }
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
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
