<?php
namespace Modules\System\Facades;

use Illuminate\Support\Facades\Facade;


class VersionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'VersionService';
    }
}