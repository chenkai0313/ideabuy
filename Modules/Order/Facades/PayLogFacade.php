<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15 0015
 * Time: 下午 14:40
 */
namespace Modules\Order\Facades;

use Illuminate\Support\Facades\Facade;

class PayLogFacade extends Facade
{
    protected  static function getFacadeAccessor()
    {
        return 'PayLogService';
    }
}