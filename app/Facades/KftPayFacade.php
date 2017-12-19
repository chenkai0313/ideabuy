<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2017/9/22
 * Time: 13:12
 */
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class KftPayFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'third.kft';
    }
}