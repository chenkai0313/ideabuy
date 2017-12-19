<?php

namespace app\Facades;

use Illuminate\Support\Facades\Facade;


class RsaFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'third.rsa';
    }
}
