<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/8/2
 * Time: 10:04
 */
namespace Modules\System\Facades;

use Illuminate\Support\Facades\Facade;

class FileFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'FileService';
    }
}