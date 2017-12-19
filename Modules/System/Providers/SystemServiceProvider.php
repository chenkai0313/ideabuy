<?php

namespace Modules\System\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\System\Facades\VerifyCodeFacade;
use Modules\System\Services\AdService;
use Modules\System\Services\ArticleService;
use Modules\System\Services\BackendArticleService;
use Modules\System\Services\BackendAdService;
use Modules\System\Services\BackendConstantService;
use Modules\System\Services\BackendVersionService;
use Modules\System\Services\BankInfoService;
use Modules\System\Services\FileService;
use Modules\System\Services\PayService;
use Modules\System\Services\QruuidService;
use Modules\System\Services\RegionService;
use Modules\System\Services\SMSService;
use Modules\System\Services\MsgTemplateService;
use Modules\System\Services\MessageService;
use Modules\System\Services\VerifyCodeService;
use Modules\System\Services\VersionService;


class SystemServiceProvider extends ServiceProvider
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
        $this->addFacades();
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('AdService',function(){
            return new AdService();
        });
        $this->app->singleton('BackendAdService',function(){
            return new BackendAdService();
        });
        $this->app->singleton('RegionService',function(){
            return new RegionService();
        });
        $this->app->singleton('FileService',function(){
            return new FileService();
        });
        $this->app->singleton('ArticleService',function(){
            return new ArticleService();
        });
        $this->app->singleton('BackendArticleService',function(){
            return new BackendArticleService();
        });
        $this->app->singleton('SMSService',function(){
            return new SMSService();
        });
        $this->app->singleton('BackendConstantService',function(){
            return new BackendConstantService();
        });
        $this->app->singleton('BankInfoService',function(){
            return new BankInfoService();
        });
        $this->app->singleton('PayService',function(){
            return new PayService();
        });
        $this->app->singleton('MsgTemplateService',function(){
            return new MsgTemplateService();
        });
        $this->app->singleton('MessageService',function(){
            return new MessageService();
        });
        $this->app->singleton('VersionService',function(){
            return new VersionService();
        });
        $this->app->singleton('BackendVersionService',function(){
            return new BackendVersionService();
        });
        $this->app->singleton('VerifyCodeService',function(){
            return new VerifyCodeService();
        });
        $this->app->singleton('QruuidService',function(){
            return new QruuidService();
        });
    }

    /**
     * 添加模块脸面
     */
    protected function addFacades()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('BackendConstantService',  \Modules\System\Facades\BackendConstantFacade::class);
        $loader->alias('AdService',  \Modules\System\Facades\AdFacade::class);
        $loader->alias('BackendAdService',  \Modules\System\Facades\BackendAdFacade::class);
        $loader->alias('ArticleService',  \Modules\System\Facades\ArticleFacade::class);
        $loader->alias('BackendArticleService',  \Modules\System\Facades\BackendArticleFacade::class);
        $loader->alias('SMSService',  \Modules\System\Facades\SMSFacade::class);
        $loader->alias('RegionService',  \Modules\System\Facades\RegionFacade::class);
        $loader->alias('FileService',  \Modules\System\Facades\FileFacade::class);
        $loader->alias('BankInfoService',  \Modules\System\Facades\BankInfoFacade::class);
        $loader->alias('PayService',  \Modules\System\Facades\PayFacade::class);
        $loader->alias('MsgTemplateService',\Modules\System\Facades\MsgTemplateFacade::class);
        $loader->alias('MessageService',\Modules\System\Facades\MessageFacade::class);
        $loader->alias('VersionService',\Modules\System\Facades\VersionFacade::class);
        $loader->alias('BackendVersionService',\Modules\System\Facades\BackendVersionFacade::class);
        $loader->alias('VerifyCodeService',\Modules\System\Facades\VerifyCodeFacade::class);
        $loader->alias('QruuidService',\Modules\System\Facades\QruuidFacade::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('system.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'system'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/system');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/system';
        }, \Config::get('view.paths')), [$sourcePath]), 'system');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/system');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'system');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'system');
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
