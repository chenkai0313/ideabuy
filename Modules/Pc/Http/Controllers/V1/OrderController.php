<?php
/**
 * 订单模块
 * Author: 葛宏华
 * Date: 2017/10/24
 */

namespace Modules\Pc\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    /**
     * 订单 列表
     */
    public function orderList(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = get_user_id();
        $result = \OrderService::orderList($params);
        return $result;
    }
    /**
     * 订单 添加
     */
    public function orderAdd(Request $request)
    {
        $params = $request->all();
        $params = json_decode($params['json_data'],true);
        $params['order_info']['user_id'] = get_user_id();
        $result = \OrderService::apiOrderAdd($params);
        return $result;
    }
    /**
     * 订单 支付完成
     */
    public function orderPayFinish(Request $request)
    {
        $params = $request->all();
        $result = \OrderService::orderPayFinish($params);
        return $result;
    }
    /**
     * 订单 确认收货
     */
    public function orderFinish(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = get_user_id();
        $result = \OrderService::orderFinish($params);
        return $result;
    }
    /**
     * 订单 删除
     */
    public function orderDelete(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = get_user_id();
        $result = \OrderService::orderDelete($params);
        return $result;
    }
    /**                                                                                                 Í
     * 订单 详细
     */
    public function orderDetail(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = get_user_id();
        $result = \OrderService::orderDetail($params);
        return $result;
    }
    /**
     * 订单 添加（联通）
     */
    public function tradeInfo(Request $request)
    {
        $params = $request->all();
        $result = \OrderService::tradeInfo($params);
        return $result;
    }
    /**
     * 订单 确认
     */
    public function orderConfirm(Request $request)
    {
        $params = $request->all();
        $params = json_decode($params['json_data'],true);
        $params['user_id'] = get_user_id();
        $result = \OrderService::orderConfirm($params);
        return $result;
    }
}
