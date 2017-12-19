<?php
/**
 * Created by PhpStorm.
 * User: pc06
 * Date: 2017/8/21
 * Time: 14:40
 */

namespace Modules\System\Facades;

use Illuminate\Support\Facades\Facade;

class MessageFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'MessageService';
    }
}
