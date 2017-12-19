<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/8/3
 * Time: 19:11
 */
namespace Modules\System\Facades;

use Illuminate\Support\Facades\Facade;

class BackendVersionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'BackendVersionService';
    }
}