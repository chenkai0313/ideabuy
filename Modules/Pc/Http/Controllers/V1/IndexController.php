<?php
/**
 * Pc首页
 * Author: CK
 */
namespace Modules\Pc\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IndexController extends Controller
{
    /**
     * 商城商品分类
     * @author CK
     * @param Request $request
     * @return array
     */
    public function goodsCategoryList(Request $request)
    {
        $param = $request->input();
        $result = \IndexService::goodsCategoryList($param);
        return $result;
    }

    /**
     * 商城公告
     * @author CK
     * @param Request $request
     * @return array
     */
    public function messageAnnounceList(Request $request)
    {
        $param = $request->input();
        $result = \IndexService::messageAnnounceList($param);
        return $result;
    }

}