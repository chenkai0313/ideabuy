<?php
/**
 * Created by PhpStorm.
 * User: 吕成
 */
namespace Modules\User\Facades;

use Illuminate\Support\Facades\Facade;

class PcUserFacade extends Facade
{
    protected  static function getFacadeAccessor()
    {
        return 'PcUserService';
    }
}