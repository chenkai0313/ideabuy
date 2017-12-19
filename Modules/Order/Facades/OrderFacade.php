<?php

namespace Modules\Order\Facades;

use Illuminate\Support\Facades\Facade;

class OrderFacade extends Facade
{
    protected  static function getFacadeAccessor()
    {
        return 'OrderService';
    }
}