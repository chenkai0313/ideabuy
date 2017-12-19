<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/8/2
 * Time: 18:46
 */
namespace Modules\System\Facades;

use Illuminate\Support\Facades\Facade;

class BackendConstantFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'BackendConstantService';
    }
}