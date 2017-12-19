<?php
/**
 * Created by PhpStorm.
 * User: 曹晗
 */
namespace Modules\User\Facades;

use Illuminate\Support\Facades\Facade;

class UserFacade extends Facade
{
    protected  static function getFacadeAccessor()
    {
        return 'UserService';
    }
}