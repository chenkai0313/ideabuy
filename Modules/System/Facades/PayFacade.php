<?php
namespace Modules\System\Facades;

use Illuminate\Support\Facades\Facade;

class PayFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'PayService';
    }
}