<?php

namespace Modules\System\Facades;

use Illuminate\Support\Facades\Facade;


class ArticleFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ArticleService';
    }
}
