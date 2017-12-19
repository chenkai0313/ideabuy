<?php
/**
 * Created by PhpStorm.
 * User: 张燕
 * Date: 2017/9/27
 * Time: 10:45
 */

namespace Modules\Supplier\Facades;

use Illuminate\Support\Facades\Facade;


class SupplierFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'SupplierService';
    }
}