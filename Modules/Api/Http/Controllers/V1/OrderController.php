<?php
/**
 * 订单模块
 * Author: 葛宏华
 * Date: 2017/8/2
 */

namespace Modules\Api\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    /**
     * 订单列表
     */
    public function orderList(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = get_user_id();
        $result = \OrderService::orderList($params);
        return $result;
    }

    /**
     * 订单添加
     */
    public function orderAdd(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = get_user_id();
        $params['date'] = isset($params['date']) ? $params['date'] : date('Y-m-d H:i:s',time());
        $result = \OrderService::orderAdd($params);
        return $result;
    }

    /**
     * 订单编辑
     */
    public function orderEdit(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = get_user_id();
        $result = \OrderService::orderEdit($params);
        return $result;
    }

    /**
     * 订单删除
     */
    public function orderDelete(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = get_user_id();
        $result = \OrderService::orderDelete($params);
        return $result;
    }

    /**                                                                                                 Í
     * 订单详细
     */
    public function orderDetail(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = get_user_id();
        $result = \OrderService::orderDetail($params);
        return $result;
    }

    /**
     * 订单添加（联通）
     */
    public function tradeInfo(Request $request)
    {
        $params = $request->all();
        $result = \OrderService::tradeInfo($params);
        return $result;
    }
}
