<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/7/31
 * Time: 12:01
 */
namespace Modules\Api\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdController extends Controller
{
    /**
     * 广告的获取
     * @param Request $request
     * @return mixed
     */
    public function adObtain(Request $request)
    {
        $params=$request->input();
        return \AdService::adObtain($params);
    }
}