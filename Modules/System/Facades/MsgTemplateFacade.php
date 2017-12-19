<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2017/8/14
 * Time: 9:15
 */
namespace Modules\System\Facades;

use Illuminate\Support\Facades\Facade;

class MsgTemplateFacade extends Facade
{
    protected  static function getFacadeAccessor()
    {
        return 'MsgTemplateService';
    }
}