<?php
/**
 * 文章模块
 * Author: 曹晗
 */
namespace Modules\Pc\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GoodsController extends Controller
{
    /**
     * 产品列表
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsList(Request $request) {
        $param = $request->input();
        $result = \PcGoodsService::goodsList($param);
        return $result;
    }

    /**
     * 产品详情
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsDetail(Request $request) {
        $param = $request->input();
        $result = \PcGoodsService::goodsDetail($param);
        return $result;
    }


    /**
     * 用户购物车添加
     * @author caohan
     */
    public function cartAdd(Request $request) {
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $result = \PcGoodsService::cartAdd($params);
        return $result;
    }

    /**
     * 用户购物车删除  批量
     * @author caohan
     */
    public function cartDel(Request $request) {
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $result = \PcGoodsService::cartDel($params);
        return $result;
    }

    /**
     * 用户购物车查询
     * @author caohan
     */
    public function cartList(Request $request) {
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $result = \PcGoodsService::cartListByUserId($params);
        return $result;
    }
}
