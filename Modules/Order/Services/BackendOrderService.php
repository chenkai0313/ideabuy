<?php
/**
 * 订单管理  后台
 * Author: CK
 * Date: 2017/8/10
 */
namespace Modules\Order\Services;


use Modules\Goods\Models\GoodsComment;
use Modules\Goods\Services\BackendGoodsService;
use Modules\Order\Models\OrderGoods;
use Modules\Order\Models\OrderInfo;
use Modules\Order\Models\PayLog;
use Illuminate\Support\Facades\DB;
use Modules\Supplier\Models\Supplier;
use Modules\System\Services\RegionService;
use Modules\User\Models\User;

class BackendOrderService
{
    /**
     * 查询详细订单
     * @param $params ['order_id'] 订单ID
     * @return array
     */
    public function orderDetail($params)
    {

        if (!isset($params['order_id'])) {
            return ['code' => 90002, 'msg' => '订单ID不能为空'];
        }
        $had = OrderInfo::where('order_id', '=', $params['order_id'])->first();
        if (is_null($had)) {
            return ['code' => 90002, 'data' => '订单不存在'];
        }
        $orderDetail = OrderInfo::backendOrderDetail($params);
        #用户地址翻译
        $RegionService = new RegionService();
        $data = array();
        $data['province'] = $orderDetail['province'];
        $data['city'] = $orderDetail['city'];
        $data['district'] = $orderDetail['district'];
        $RegionService = $RegionService->regionGet($data);
        $orderDetail['province'] = $RegionService['data']['province'];
        $orderDetail['city'] = $RegionService['data']['city'];
        $orderDetail['district'] = $RegionService['data']['district'];
        #用户ID翻译
        $real_name = User::where('user_id', '=', $orderDetail['user_id'])->first();
        $orderDetail['user_id'] = $real_name['real_name'];
        if (is_null($orderDetail['user_id'])) {
            $orderDetail['user_id'] = "用户不存在";
        }
        return ['code' => 1, 'data' => $orderDetail];
    }

    /**
     * 查询所有订单
     * @param $params ['limit'] 一页的数据
     * @param $params ['page'] 当前页
     * @param $params ['keyword'] 查询关键字 可为空
     * @return array
     */
    public function orderList($params)
    {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : '';
        $data['list'] = OrderInfo::backendOrderList($params);
        foreach ($data['list'] as &$v) {
            #用户地址翻译
            $RegionService = new RegionService();
            $arr = array();
            $arr['province'] = $v['province'];
            $arr['city'] = $v['city'];
            $arr['district'] = $v['district'];
            $RegionService = $RegionService->regionGet($arr);
            $v['province'] = $RegionService['data']['province'];
            $v['city'] = $RegionService['data']['city'];
            $v['district'] = $RegionService['data']['district'];
            #用户ID翻译
            $real_name = User::where('user_id', '=', $v['user_id'])->first();
            $v['user_name'] = $real_name['real_name'];
            if (is_null($v['user_id'])) {
                $v['user_id'] = "用户不存在";
            }
            foreach($v['goods_info'] as $m){
                $commentExist=GoodsComment::select('comment_id','comment_star','comment_pics','comment_desc','repay_at','comment_repay','created_at')->where('goods_key','=',$m['goods_key'])->first();
                if(!is_null($commentExist['comment_pics'])){
                    $commentExist['comment_pics']= explode("|",$commentExist['comment_pics']);
                }
                if(!is_null($commentExist['comment_id'])){
                    if(is_null($commentExist['repay_at'])){
                        $v['comment']['is_comment']='未回复';
                        unset($commentExist['repay_at']);
                        unset($commentExist['comment_repay']);
                        $v['comment']['comment_info']=$commentExist;
                    }else{
                        $v['comment']['is_comment']='已回复';
                        $v['comment']['comment_info']=$commentExist;

                    }
                }
                else{
                        $v['comment']['is_comment']='未评论';
                    }
            }
        }
        $data['total'] = OrderInfo::backendOrderCount($params);
        return ['code' => 1, 'data' => $data];
    }

    /**
     * 指派订单给供应商
     * @return array
     */
    public function orderAssign($params)
    {
        if (!isset($params['order_sn'])) {
            return ['code' => 90001, 'msg' => '订单编号必填'];
        } else {
            $order = OrderInfo::orderDetailSn($params['order_sn']);
            if (is_null($order)) {
                return ['code' => 10260, 'msg' => '未找到该订单'];
            }
        }
        if (!isset($params['supplier_id'])) {
            return ['code' => 90001, 'msg' => '指派供应商id必填'];
        } else {
            $supplier = Supplier::find($params['supplier_id']);
            if (is_null($supplier)) {
                return ['code' => 10262, 'msg' => '该供应商不存在'];
            }
        }
        if (OrderInfo::assignOrder($params)) {
            $res['code'] = 1;
            $res['msg'] = '操作成功';
        }
        return $res;
    }

    /**
     * 订单拆分
     * Author: ck
     * @return array
     */
    public function orderApart($params)
    {
        #获取原始订单
        $order_info = OrderInfo::orderDetailSn($params['order_sn']);
        if ($order_info) {
            if ($order_info['order_status'] == 1) {
                DB::beginTransaction();
                $new = array();//新订单
                $old = array();//老订单
                #新订单order_info表数据
                $new['order'] = $order_info;
                $new['order']['parent_id'] = $order_info['order_id'];
                $new['order']['order_sn'] = get_sn('O');
                $new['order']['freight_amount'] = 0;
                #新订单order_goods表数据
                $arr_goods_key = explode(',', $params['goods_key']);
                $arr_goods_number = explode(',', $params['goods_number']);
                $order_amount = 0;
                foreach ($arr_goods_key as $k => $v) {
                    $order_goods = OrderGoods::orderGoodsOne($v);
                    $new['goods'][$k] = $order_goods->toArray();
                    $new['goods'][$k]['order_sn'] = $new['order']['order_sn'];
                    $new['goods'][$k]['goods_key'] = get_goods_key();
                    $new['goods'][$k]['goods_number'] = $arr_goods_number[$k];
                    $new['goods'][$k]['goods_amount'] = $arr_goods_number[$k] * $order_goods['product_price'];
                    unset($new['goods'][$k]['id']);
                    $order_amount += $new['goods'][$k]['goods_amount'];
                    #原始订单order_goods数据
                    $old['goods'][$k]['id'] = $order_goods['id'];
                    $old['goods'][$k]['goods_number'] = $order_goods['goods_number'] - $arr_goods_number[$k];
                    $old['goods'][$k]['goods_amount'] = ($order_goods['goods_number'] - $arr_goods_number[$k]) * $order_goods['product_price'];
                }
                #插入新订单数据(先商品,再订单)

                $new['order_from'] = 3;  // 临时处理 by yefan

                $new['order']['order_amount'] = $order_amount;
                $res = \OrderService::orderAdd($new);
                #更新原始订单order_info表数据
                $old['order']['order_sn'] = $order_info['order_sn'];
                $old['order']['apart_at'] = date('Y-m-d H:i:s', time());
                $old['order']['order_amount'] = $order_info['order_amount'] - $order_amount;
                $res2 = OrderInfo::orderEdit($old['order']);
                $res3 = update_batch('wk_order_goods', $old['goods']);
                if ($res['code'] == 1 && $res2 && $res3) {
                    DB::commit();
                    $result['code'] = 1;
                    $result['msg'] = '拆单成功';
                } else {
                    DB::rollback();
                    $result['code'] = $res['code'];
                    $result['msg'] = $res['msg'];
                }
            } else {
                $result['code'] = 10156;
                $result['msg'] = '该阶段不能拆单';
            }
        } else {
            $result['code'] = 10153;
            $result['msg'] = '未找到该订单';
        }
        return $result;
    }
}