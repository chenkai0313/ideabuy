<?php
/**
 * 订单模块
 * Author: CK
 * Date: 2017/8/10
 */
namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    /**
     * 订单列表
     */
    public function orderList(Request $request) {
        $params = $request->input();
        $result = \BackendOrderService::orderList($params);
        return $result;
    }
    /**
     * 订单详情
     */
    public  function  orderDetail(Request $request){
        $params = $request->input();
        $result = \BackendOrderService::orderDetail($params);
        return $result;
    }
    /**
     * 订单添加
     */
    public function orderAdd(Request $request)
    {
        $params = $request->all();
        //$params['user_id'] = get_user_id();
        $params['date'] = isset($params['date']) ? $params['date'] : date('Y-m-d H:i:s',time());
        $result = \OrderService::orderAdd($params);
        return $result;
    }
    /**
     * 订单添加
     */
    public function apiOrderAdd(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = get_user_id();
        $params['date'] = isset($params['date']) ? $params['date'] : date('Y-m-d H:i:s',time());
        $result = \OrderService::apiOrderAdd($params);
        return $result;
    }
    /**
     * 清除订单
     */
    public function orderClear(Request $request)
    {
        $params = $request->all();
        $res1 = \OrderService::orderClear($params);
        $res2 = \UserWalletService::UserWalletClear($params);
        $res3 = \UserWalletService::UserWalletDetailClear($params);
        $res4 = \UserService::UserClear($params);
        return $res1;
    }
    /**
     * 拆分订单
     */
    public function orderApart(Request $request)
    {
        $params = $request->input();
        return  \BackendOrderService::orderApart($params);
    }
    /**
     * 指派订单
     */
    public function orderAssign(Request $request)
    {
        $params = $request->input();
        return  \OrderService::orderAssign($params);
    }
}
