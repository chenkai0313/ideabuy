<?php
namespace Modules\System\Facades;

use Illuminate\Support\Facades\Facade;

class QruuidFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'QruuidService';
    }
}