<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/1 0001
 * Time: 下午 15:02
 */
namespace app\Facades;

use Illuminate\Support\Facades\Facade;

class MessageFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'third.message';
    }
}