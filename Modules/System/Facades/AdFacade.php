<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/7/25
 * Time: 15:55
 */
namespace Modules\System\Facades;

use Illuminate\Support\Facades\Facade;

class AdFacade extends Facade
{
    protected  static function getFacadeAccessor()
    {
        return 'AdService';
    }
}