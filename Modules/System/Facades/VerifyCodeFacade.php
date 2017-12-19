<?php
namespace Modules\System\Facades;

use Illuminate\Support\Facades\Facade;


class VerifyCodeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'VerifyCodeService';
    }
}