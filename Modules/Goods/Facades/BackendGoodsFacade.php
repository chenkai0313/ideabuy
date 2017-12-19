<?php
/**
 * Created by PhpStorm.
 * User: fuyuehua
 * Date: 2017/9/19
 * Time: 14:58
 */
namespace Modules\Goods\Facades;

use Illuminate\Support\Facades\Facade;

class BackendGoodsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'BackendGoodsService';
    }
}