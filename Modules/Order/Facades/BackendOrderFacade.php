<?php
/**
 * Created by PhpStorm.
 * User: CK
 * Date: 2017/8/10
 */
namespace Modules\Order\Facades;

use Illuminate\Support\Facades\Facade;

class BackendOrderFacade extends Facade
{
    protected  static function getFacadeAccessor()
    {
        return 'BackendOrderService';
    }
}