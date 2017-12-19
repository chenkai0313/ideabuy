<?php
/**
 * Created by PhpStorm.
 * User: 曹晗
 * Date: 2017/8/1
 */

namespace Modules\System\Facades;

use Illuminate\Support\Facades\Facade;

class BackendArticleFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'BackendArticleService';
    }
}