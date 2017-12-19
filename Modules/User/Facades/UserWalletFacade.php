<?php
/**
 * Created by PhpStorm.
 * User: pc08
 * Date: 2017/8/24
 * Time: 10:10
 */

namespace Modules\User\Facades;

use Illuminate\Support\Facades\Facade;

class UserWalletFacade extends Facade
{
    protected  static function getFacadeAccessor()
    {
        return 'UserWalletService';
    }
}