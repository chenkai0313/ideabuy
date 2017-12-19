<?php

namespace App\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class ThirdServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->addFacade();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerOss();
        $this->registerWeChat();
        $this->registerAlipay();
        $this->registerThirdValidator();
        $this->registerJpush();
        $this->registerYeePay();
        $this->registerMessage();
        $this->registerRsa();
        $this->registerKft();
    }

    public function registerOss()
    {
        $this->app->bind('third.oss', 'Libraries\Service\OssService');
    }
    public function registerMessage()
    {
        $this->app->bind('third.message', 'Libraries\Service\MessageService');
    }
    public function registerYeePay()
    {
        $this->app->bind('third.yeepay', 'Libraries\Service\YeePayService');
    }
    public function registerKft()
    {
        $this->app->bind('third.kft', 'Libraries\Service\KftPayService');
    }

    public function registerWeChat()
    {
        //$this->app->bind('third.wechat', 'Libraries\Services\WechatService');
    }

    public function registerAlipay()
    {
        //$this->app->bind('third.alipay', 'Libraries\Services\AlipayService');
    }
    public function registerThirdValidator()
    {
        $this->app->bind('third.thirdValidator','Libraries\Service\ThirdValidatorService');
    }
    public function registerJpush()
    {
        $this->app->bind('third.jpush','Libraries\Service\JpushService');
    }
    public function registerRsa()
    {
        $this->app->bind('third.rsa','Libraries\Service\RsaService');
    }

    public function addFacade()
    {

        $loader = AliasLoader::getInstance();

        $loader->alias('Oss',  \App\Facades\OssFacade::class);
        $loader->alias('thirdValidator',  \App\Facades\ThirdValidatorFacade::class);
        $loader->alias('jpush',\App\Facades\JpushFacade::class);
        $loader->alias('yeepay',\App\Facades\YeePayFacade::class);
        $loader->alias('message',\App\Facades\MessageFacade::class);
        $loader->alias('Rsa',\App\Facades\RsaFacade::class);
        $loader->alias('kftpay',\App\Facades\KftPayFacade::class);
    }

}
