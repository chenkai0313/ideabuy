<?php
/**
 * Goods.
 * User: caohan
 * Date: 2017/10/24
 */
namespace Modules\Api\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class GoodsController extends  Controller{
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