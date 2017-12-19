<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29 0029
 * Time: 下午 17:16
 */
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class YeePayFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'third.yeepay';
    }
}