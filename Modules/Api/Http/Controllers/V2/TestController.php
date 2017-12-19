<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/31 0031
 * Time: 下午 16:58
 */
namespace Modules\Api\HTTP\Controllers\V2;

use Illuminate\Routing\Controller;

class TestController extends Controller
{
    public function test()
    {
        return '测试版本控制v2';
    }
}