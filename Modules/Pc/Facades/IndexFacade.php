<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/10/11
 * Time: 15:55
 */
namespace Modules\Pc\Facades;

use Illuminate\Support\Facades\Facade;

class IndexFacade extends Facade
{
    protected  static function getFacadeAccessor()
    {
        return 'IndexService';
    }
}