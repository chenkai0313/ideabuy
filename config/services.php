<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'oss'=>[
        'key'=>env('ACCESS_KEY_ID','LTAIXijUalhN321o'),
        'secret'=>env('ACCESS_KEY_SECRET','d0ouwDjnRV7CpG5M1EptbAIhnYJQlD'),
        'endpoint'=>env('ACCESS_ENDPOINT','http://vpc100-oss-cn-hangzhou.aliyuncs.com'),
        'bucket'=>env('ACCESS_BUCKET','ideabuy'),
        'filepath'=>env('FILEPATH','./Storage/Uploads/tmp/'),
        'host'=>env('ACCESS_HOST','https://ideabuy.oss-cn-hangzhou.aliyuncs.com'),
    ],
    'bank'=>[
        'appcode'=>env('BANK_APP_CODE'),
        'apisecret'=>env('BANK_API_SECRET'),
        'apikey'=>env('BANK_API_KEY'),
    ],
    'jpush'=>[
        'key'=>env('JPUSH_APP_KEY','88becdd71b6d85e5b858c348'),
        'secret'=>env('JPUSH_MASTER_SECRET','44aefc3ef8749e738b8191c0'),
        'apns_production'=>env('APNS_PRODUCTION',false)
    ],
    'yeepay'=>[
        'merchantaccount'=>env('YEEPAY_MERCHANT_ACCOUNT'),
        'merchantPrivateKey'=>env('YEEPAY_MERCHANT_PRIVATE_KEY'),
        'merchantPublicKey'=>env('YEEPAY_MERCHANT_PUBLIC_KEY'),
        'yeepayPublicKey'=>env('YEEPAY_PUBLIC_KEY'),
        'fcallbackurl'=>env('FRONT_DOMAIN').'/payCallback.html',
        'callbackurl'=>env('IDEABUY_DOMAIN').'/api/yee-notify',
    ],
    'kft'=>[
        'merchantid'=>env('KFT_MERCHANTID'),//快付通商家ID
        'version'=>env('KFT_VERSION'),//快付通版本
        'merchantBankAcc_ountNo'=>env('KFT_MERCHAN_BANK_NO',''),//快付通商家银行卡号
        'request_url'=>env('KFT_REQUEST_URL')//快付通请求地址
    ],
];
