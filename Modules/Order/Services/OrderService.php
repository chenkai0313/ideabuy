<?php
/**
 * 订单模块
 * Author: 葛宏华
 * Date: 2017/8/2
 */

namespace Modules\Order\Services;

use Modules\Goods\Models\Goods;
use Modules\Goods\Models\GoodsAttr;
use Modules\Goods\Models\GoodsProducts;
use Modules\Order\Models\OrderInfo;
use Modules\Order\Models\OrderGoods;
use Modules\Order\Models\PayLog;
use Modules\User\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\User\Models\UserCard;

class OrderService
{
    /**
     * 订单 列表
     * @param int $limit 每页显示数量
     * @param int $page 当前页数
     * @return array
     */
    public function orderList($params)
    {
        $res = OrderInfo::orderList($params);
        #订单商品数据
        foreach ($res['list'] as $k => $v) {
            $res['list'][$k] ['goods_info'] = OrderGoods::orderGoodsDetail($v['order_sn']);
        }
        $result['data']['order_list'] = $res['list'];
        $result['data']['total'] = $res['total'];
        $result['data']['pages'] = $res['pages'];
        $result['code'] = 1;
        return $result;
    }
    /**
     * 订单  添加
     * @return array
     */
    public function orderAdd($params)
    {
        if (!empty($params['mobile']) && !preg_match("/^1[34578]\d{9}$/", $params['mobile'])) {
            $result['code'] = 90002;
            $result['msg'] = '手机号码格式不正确';
        } else {
            #订单来源(线下门店),目前仅针对单个商品
            if ($params['order_from'] == 2) {
                DB::beginTransaction();
                #order_info表 插入数据
                $data_order['user_id'] = $params['user_id'] ? $params['user_id'] : '';
                $data_order['admin_id'] = isset($params['admin_id']) ? $params['admin_id'] : 1;
                $data_order['order_sn'] = get_sn('O');
                $data_order['order_from'] = $params['order_from'];//订单来源（1线上商城，2线下门店）
                $data_order['pay_id'] = $params['pay_id'] ? $params['pay_id'] : 1;//支付方式（1白条，2支付宝，3微信，4银行卡，5余额）
                $data_order['order_status'] = 5;
                $data_order['order_remark'] = $params['order_remark'];
                $data_order['consignee'] = $params['consignee'];
                $data_order['mobile'] = $params['mobile'];
                $data_order['province'] = $params['province'];
                $data_order['city'] = $params['city'];
                $data_order['district'] = $params['district'];
                $data_order['address'] = $params['address'];
                $data_order['loan_product_id'] = $params['loan_product_id'] ? $params['loan_product_id'] : 0;
                $data_order['goods_amount'] = $params['goods_number'] * $params['product_price'];
                $data_order['freight_amount'] = $params['freight_amount'];
                //$data_order['order_amount'] = $params['order_amount'];
                $data_order['order_amount'] = $params['goods_number'] * $params['product_price'];
                $data_order['created_at'] = isset($params['date']) ? $params['date'] : date('Y-m-d H:i:s', time());
                $res1 = OrderInfo::orderAdd($data_order);
                #order_goods表 插入数据 单商品
                //$data['goods']['goods_id']     = $params['goods_id'];
                //$data['goods']['product_id']   = $params['product_id'];
                $data_goods['order_sn'] = $data_order['order_sn'];
                $data_goods['goods_key'] = get_goods_key();//订单商品唯一码
                $data_goods['goods_number'] = $params['goods_number'];
                $data_goods['goods_name'] = $params['goods_name'];
                $data_goods['goods_thumb'] = $params['goods_thumb'];
                $data_goods['attr_name'] = $params['attr_name'];
                $data_goods['attr_value'] = $params['attr_value'];
                $data_goods['market_price'] = $params['market_price'];
                $data_goods['product_price'] = $params['product_price'];
                $data_goods['goods_amount'] = $params['goods_number'] * $params['product_price'];
                $res2 = OrderGoods::orderGoodsAdd($data_goods);
                #若白条支付
                if ($params['pay_id'] == 1) {
                    #判断白条余额是否充足
                    $white_money = \UserWalletService::UserWalletInfo($params);
                    if ($white_money['data']['white_money'] < $data_order['order_amount']) {
                        return ['code' => 10074, 'msg' => '您的白条余额不足'];
                    }
                    #总还款金额
                    $data_wallet['user_id'] = $data_order['user_id'];
                    $data_wallet['change_money'] = '-' . $data_order['order_amount'];
                    $data_wallet['type'] = 1;
                    $data_wallet['status'] = 1;
                    $res3 = \UserWalletService::UserWalletDetailAdd($data_wallet);
                    if ($params['loan_product_id'] == 1) {
                        $params['order_sn'] = $data_order['order_sn'];
                        $params['amount'] = $data_order['order_amount'];
                        $params['loan_product_name'] = '分期产品';
                        $res4 = self::installAdd($params);
                    } else {
                        $res4 = ['code' => 1];
                    }
                }
            } else {
                #其他订单来源
                return ['code'=>1, 'msg'=>'其他订单来源，还在开发中'];
            }
            if ($res1 && $res2 && $res3['code'] == 1 && $res4['code'] == 1) {
                #pay_log表 插入数据
                $data_log['order_sn'] = $data_order['order_sn'];
                $data_log['trade_no'] = $params['trade_no'];
                $data_log['from_type'] = 1;
                $data_log['pay_money'] = $data_order['order_amount'];
                $data_log['pay_id'] = $params['pay_id'];
                $res3 = PayLog::payLogAdd($data_log);
                if ($res3) {
                    DB::commit();
                    $result['code'] = 1;
                    $result['msg'] = '提交成功';
                } else {
                    DB::rollback();
                    $result['code'] = 10151;
                    $result['msg'] = '插入订单支付记录失败';
                }
            } else {
                $result['code'] = 10150;
                $result['msg'] = '提交订单失败';
            }
        }
        return $result;
    }
    /**
     * 订单  添加
     * @param array $order_info 订单基础信息，因为一样可复用（收货信息、物流信息、配送信息、发票信息、支付方式）
     * @param array $goods_info 订单商品信息（$product_id，$goods_number）
     * @return array
     */
    public function  apiOrderAdd($params)
    {
        $params_order = $params['order_info'];//订单信息
        $params_goods = $params['goods_info'];//订单商品信息
        $res = $this->orderConfirmMain($params_goods);
        if (!empty($params_order['mobile']) && !preg_match("/^1[34578]\d{9}$/", $params_order['mobile'])) {
            $result['code'] = 90002;
            $result['msg'] = '手机号码格式不正确';
        } else {
            #订单来源（线上商城）
            if ($params_order['order_from'] == 1) {
                DB::beginTransaction();
                $str_order_sn = '';
                foreach($res['store_list'] as $v){
                    #初始化
                    $data_order = [];
                    $data_goods = [];
                    #order_info表 插入数据
                    $data_order['user_id'] = $params_order['user_id'] ? $params_order['user_id'] : '';
                    $data_order['admin_id'] = isset($v['admin_id']) ? $v['admin_id'] : 1;
                    $data_order['order_sn'] = get_sn('O');
                    $data_order['order_from'] = $params_order['order_from'];//订单来源（1线上商城，2线下门店）
                    $data_order['pay_id'] = $params_order['pay_id'] ? $params_order['pay_id'] : 1;//支付方式（1白条，2支付宝，3微信，4银行卡，5余额）
                    #获取收货地址信息
                    $address_info = \UserService::userAddressDetail(['user_id'=>$params_order['user_id'],'address_id'=>$params_order['address_id']]);
                    if($address_info['code']!=1){
                        return ['code'=>$address_info['code'],'msg'=>$address_info['msg']];
                    }
                    $address_info = $address_info['data']['address_info'];
                    $data_order['consignee'] = $address_info['consignee'];
                    $data_order['mobile'] = $address_info['mobile'];
                    $data_order['province'] = $address_info['province'];
                    $data_order['city'] = $address_info['city'];
                    $data_order['district'] = $address_info['district'];
                    $data_order['address'] = $address_info['address'];
                    $data_order['loan_product_id'] = $params_order['loan_product_id'] ? $params_order['loan_product_id'] : 2;
                    $data_order['freight_amount'] = $v['freight_amount'];
                    $data_order['shipping_name'] = $params_order['shipping_name'];
                    $data_order['shipping_time'] = $params_order['shipping_time'];
                    $data_order['invoice_type'] = $params_order['invoice_type'];
                    $data_order['invoice_title'] = $params_order['invoice_title'];
                    $data_order['invoice_code'] = $params_order['invoice_code'];
                    //$data_order[$m]['created_at'] = isset($params_order['date']) ? $params_order['date'] : date('Y-m-d H:i:s', time());
                    #order_goods表 插入数据
                    $goods_amount = 0;
                    foreach($v['goods_list'] as $m=>$n){
                        #整理order_goods表参数
                        $data_goods[$m]['goods_id']     = $n['goods_id'];
                        $data_goods[$m]['product_id']   = $n['product_id'];
                        $data_goods[$m]['order_sn'] = $data_order['order_sn'];
                        $data_goods[$m]['goods_key'] = get_goods_key();//订单商品唯一码
                        $data_goods[$m]['goods_number'] = $n['goods_number'];
                        $data_goods[$m]['goods_name'] = $n['goods_name'];
                        $data_goods[$m]['goods_thumb'] = $n['goods_thumb'];
                        $data_goods[$m]['str_goods_attr'] = $n['str_goods_attr'];
                        $data_goods[$m]['market_price'] = $n['market_price'];
                        $data_goods[$m]['product_price'] = $n['product_price'];
                        $data_goods[$m]['goods_amount'] = $n['goods_number'] * $n['product_price'];//同类商品小计
                        $goods_amount += $data_goods[$m]['goods_amount'];
                    }
                    $data_order['goods_amount'] = $goods_amount;
                    $data_order['order_amount'] = $goods_amount;
                    #插入order_info表，order_goods表
                    $res1 = OrderInfo::orderAdd($data_order);
                    $res2 = OrderGoods::orderGoodsAdd($data_goods);
                    $str_order_sn .= $data_order['order_sn'].',';//拼接order_sn
                }
                #若白条支付
                if ($params_order['pay_id'] == 1) {
                    #判断白条余额是否充足
                    $white_money = \UserWalletService::UserWalletInfo($params_order);
                    if ($white_money['data']['white_money'] < $goods_amount) {
                        return ['code' => 10074, 'msg' => '您的白条余额不足'];
                    }
                    #总还款金额
                    $data_wallet['user_id'] = $params_order['user_id'];
                    $data_wallet['change_money'] = '-' . $goods_amount;
                    $data_wallet['type'] = 1;
                    $data_wallet['status'] = 1;
                    $res3 = \UserWalletService::UserWalletDetailAdd($data_wallet);
                    if ($params_order['loan_product_id'] == 1) {
                        $params_order['order_sn'] = $data_order['order_sn'];
                        $params_order['amount'] = $data_order['order_amount'];
                        $params_order['loan_product_name'] = '分期产品';
                        $res4 = self::installAdd($params_order);
                    } else {
                        $res4 = ['code' => 1];
                    }
                }
            } else {
                #其他支付方式
                //return ['code'=>90001, 'msg'=>'其他订单来源，还在开发中'];
            }
            if ($res1 && $res2) {
//            if ($res1 && $res2 && $res3['code'] == 1 && $res4['code'] == 1) {
//                #pay_log表 插入数据
//                $data_log['order_sn'] = $data_order['order_sn'];
//                $data_log['trade_no'] = $params_order['trade_no'];//第三方交易号
//                $data_log['from_type'] = 1;
//                $data_log['pay_money'] = $data_order['order_amount'];
//                $data_log['pay_id'] = $params_order['pay_id'];
//                $res3 = PayLog::payLogAdd($data_log);
//                if ($res3) {
                    DB::commit();
                    $result['code'] = 1;
                    $result['msg'] = '提交成功';
                    $result['data']['order_sn'] = rtrim($str_order_sn,',');
//                } else {
//                    DB::rollback();
//                    $result['code'] = 10151;
//                    $result['msg'] = '插入订单支付记录失败';
//                }
            } else {
                $result['code'] = 10150;
                $result['msg'] = '提交订单失败';
            }
        }
        return $result;
    }
    /**
     * 订单  确认
     * @param $goods_info json 购物车的商品信息(admin_id,product_id,goods_number)
     * @param $user_id int 用户ID
     * @return array
     */
    public function orderConfirm($params)
    {
        $json_goods_info = $params['goods_info'];//二维数组
        $user_id = $params['user_id'];
        $res = $this->orderConfirmMain($json_goods_info);
        #收货地址信息
        $res_address = \UserService::userAddressList(['user_id'=>$user_id]);
        $address_list = $res_address['data']['address_list'];
        #物流信息
        $shipping_name_list = ['0'=>'不限物流'];
        #配送时间
        $shipping_time_list = ['0'=>'不限时间','1'=>'仅工作日','2'=>'仅周末/节假日'];
        #支付方式
        $pay_list = ['1'=>'白条','2'=>'支付宝','3'=>'微信'];
        #白条分期
        $vpost_res = vpost(\Config::get('interactive.riskcontrol.install_getinstalltypeplan'), ['amount'=>$res['total']['total_order_amount']]);
        $vpost_res = json_decode($vpost_res);
        $white_list =  $vpost_res->data->list;
        #整理返回参数
        $result['code'] = 1;
        $result['data']['store_list'] = $res['store_list'];
        $result['data']['address_list'] = $address_list;
        $result['data']['shipping_name_list'] = $shipping_name_list;
        $result['data']['shipping_time_list'] = $shipping_time_list;
        $result['data']['pay_list'] = $pay_list;
        $result['data']['white_list'] = $white_list;
        $result['data']['total'] = $res['total'];
        return $result;
    }
    /**
     * 订单  确认（门店信息+费用合计信息）
     * @return array
     */
    public function orderConfirmMain($params)
    {
        #获取商品的店铺信息
        foreach($params as $k=>$v){
            $params[$k]['admin_id'] = $arr_admin_id[$k] = GoodsProducts::getAdminId($v['product_id']);
        }
        $total_goods_amount = 0;
        #店铺ID去重
        $arr_admin_id = array_unique($arr_admin_id);
        foreach($arr_admin_id as $k=>$v){
            #初始化
            $product_info = [];
            #店铺信息
            $res_store = \AdminService::adminDetail($v);
            $store_list[$k]['admin_id'] = $v;
            $store_list[$k]['admin_nick'] = $res_store['data']['admin_info']['admin_nick'];
            $store_goods_amount = 0;
            foreach($params as $m=>$n){
                if($v==$n['admin_id']){//同一店铺商品
                    #货品信息
                    $product_info[$m] = GoodsProducts::where('goods_products.product_id',$n['product_id'])
                        ->leftJoin('goods','goods.goods_id','goods_products.goods_id')
                        ->select('goods.goods_name','goods.goods_thumb','goods_products.goods_id','goods_products.product_id','goods_products.market_price','goods_products.product_price','goods_products.goods_attr')
                        ->first();
                    #货品属性信息
                    $goods_attr = GoodsAttr::goodsAttrList([$n['product_id']]);
//                    if($goods_attr){
//                        $str_goods_str = '';
//                        foreach($goods_attr as $t){
//                            $str_goods_str .= $t['attr_name'].'：'.$t['attr_value'].',';
//                        }
//                    }
//                    $product_info[$m]['str_goods_attr'] = rtrim($str_goods_str,',');
                    $product_info[$m]['goods_attr'] = $goods_attr;
                    $product_info[$m]['market_price'] = number_format($product_info[$m]['market_price'],'2','.','');
                    $product_info[$m]['product_price'] = number_format($product_info[$m]['product_price'],'2','.','');
                    $product_info[$m]['goods_thumb'] =  \Config::get('services.oss.host').'/'.$product_info[$m]['goods_thumb'];
                    $product_info[$m]['goods_number'] =  $n['goods_number'];
                    #价格累加
                    $total_goods_amount += $product_info[$m]['product_price']*$n['goods_number'];//所有店铺累计
                    $store_goods_amount += $product_info[$m]['product_price']*$n['goods_number'];//单个店铺累计
                }
            }
            $store_list[$k]['goods_amount'] = number_format($store_goods_amount,2,'.','');
            $store_list[$k]['freight_amount'] = '0.00';//暂定
            $store_list[$k]['service_amount'] = '0.00';//暂定
            $store_list[$k]['goods_list'] = $product_info;
        }
        #支付费用
        $total['total_goods_amount']= number_format($total_goods_amount,2,'.','');
        $total['total_freight_amount'] = '0.00';//暂定
        $total['total_service_amount'] = '0.00';//暂定
        $total['total_order_amount'] =number_format($total_goods_amount,2,'.','') ;//支付费用=商品总费用+运费+服务费
        return ['store_list'=>$store_list,'total'=>$total];
    }
    /**
     * 分期合同的生成
     * @param $params ['user_id']    int     用户ID
     * @param $params ['order_sn']    string     订单sn,多个已,号隔开
     * @param $params ['month']    int     分期月数
     * @param $params ['amount']    int     总金额
     * @return array|mixed
     *
     * @author  liyongchuan
     */
    public function installAdd($params)
    {
        #合同信息
        $data_risk['user_id'] = $params['user_id'];
        $data_risk['order_sn'] = $params['order_sn'];
        $data_risk['month'] = $params['month'];
        $data_risk['amount'] = $params['amount'];
        $data_risk['is_must'] = 0;//初始化，是否插入用户信息
        #判断风控系统是否存有该用户
        $user_result = vget(\Config::get('interactive.riskcontrol.risk_is_user') . '?user_id=' . $params['user_id']);
        $user_result = json_decode($user_result, true);
        #获取用户信息
        $user_params['user_id'] = $params['user_id'];
        $user_params['isset_loan'] = $user_result['code'];
        $user_info = \UserService::userInfo($user_params);
        $data_risk['user_name'] = $user_info['data']['real_name'];
        $data_risk['user_mobile'] = $user_info['data']['user_mobile'];
        $data_risk['user_idcard'] = $user_info['data']['user_idcard'];
        if ($user_result['code'] != 1) {
            $data['user_id'] = $params['user_id'];
            $date = explode('-', $params['date']);
            $year = $date[0];
            $month = $date[1];
            $data['first_bill_date'] = date("Y-m-d H:i:s", mktime(0, 0, 0, $month + 1, get_constant_cache('statement_date', 'credit'), $year));
            $data['first_pay_date'] = date("Y-m-d H:i:s", mktime(0, 0, 0, $month + 1, get_constant_cache('repayment_date', 'credit'), $year));
            \UserService::userEditDate($data);
            $data_risk['is_must'] = 1;
            $data_risk['real_name'] = $user_info['data']['real_name'];
            $data_risk['user_portrait'] = $user_info['data']['user_portrait'];
            $data_risk['white_amount'] = $user_info['data']['white_amount'];
            $data_risk['user_education'] = $user_info['data']['info']['user_education'];
            $data_risk['user_profession'] = $user_info['data']['info']['user_profession'];
            $data_risk['user_company'] = $user_info['data']['info']['user_company'];
            $data_risk['user_income'] = $user_info['data']['info']['user_income'];
            $data_risk['user_qq'] = $user_info['data']['info']['user_qq'];
            $data_risk['user_email'] = $user_info['data']['info']['user_email'];
            $data_risk['link_man'] = $user_info['data']['info']['link_man'];
            $data_risk['link_mobile'] = $user_info['data']['info']['link_mobile'];
            $data_risk['link_relation'] = $user_info['data']['info']['link_relation'];
        }
        $data_risk['date'] = $params['date'];
        $data_risk['loan_product_id'] = $params['loan_product_id'];
        //$data_risk = array_filter($data_risk);
        #插入合同+分期借贷+会员信息（可选）
        $res4 = vpost(\Config::get('interactive.riskcontrol.install_contract'), $data_risk);
        $res4 = json_decode($res4, true);
        return $res4;
    }


    /**
     *确认分期  1.调用installAdd方法 2.如果成功 修改order_info的loan_product_id
     * @param $params ['user_id']    int     用户ID
     * @param $params ['order_sn']    string     订单sn,多个已,号隔开
     * @param $params ['month']    int     分期月数
     * @param $params ['amount']    int     总金额
     * @return mixed
     */
    public function confirmInstall($params)
    {
        $params['date'] = isset($params['date']) ? $params['date'] : date('Y-m-d H:i:s', time());
        #判断订单是否有效
        $conditon = get_month_time();
        $conditon['order_sn_arr_explode'] = explode(",", $params['order_sn']);//str割成arr
        if (count($conditon['order_sn_arr_explode']) == OrderInfo::orderInfoCanInstall($conditon)) {
            $result = $this->installAdd($params);//进行分期
            if ($result['code'] == 1) {
                OrderInfo::orderEditProductID($conditon['order_sn_arr_explode']);//更新订单 变成分期
            }
            return $result;
        } else {
            return ['code' => 500, 'msg' => '订单号有误，分期失败'];
        }

    }
    /**
     * 订单 支付完成
     * @param string $order_sn 订单ID
     * @param string $order_status 订单状态
     * @return array
     */
    public function orderPayFinish($params)
    {
        if ($params['out_trade_no']) {
            $res = OrderInfo::orderPayFinish($params['out_trade_no']);dd($res);
            if ($res) {
                Header('Location:http://ip.d.weknet.cn/mall/pay-return');
                exit;
//                $result['code'] = 1;
//                $result['msg'] = '订单支付成功';
            } else {
                Header('Location:http://ip.d.weknet.cn/mall/pay-return');
                exit;
//                $result['code'] = 10152;
//                $result['msg'] = '订单支付失败';
            }
        } else {
            Header('Location:http://ip.d.weknet.cn/mall/pay-return');
            exit;
//            $result['code'] = 90001;
//            $result['msg'] = '传参错误';
        }
//        return $result;
    }
    /**
     * 订单 确认收货
     * @param string $order_sn 订单ID
     * @param string $order_status 订单状态
     * @return array
     */
    public function orderFinish($params)
    {
        if ($params['order_sn']) {
            $res = OrderInfo::orderFinish($params);
            if ($res) {
                $result['code'] = 1;
                $result['msg'] = '订单更新成功';
            } else {
                $result['code'] = 10152;
                $result['msg'] = '订单更新失败';
            }
        } else {
            $result['code'] = 90001;
            $result['msg'] = '传参错误';
        }
        return $result;
    }
    /**
     * 订单  详情
     * @param int $order_sn 订单编号
     * @return array
     */
    public function orderDetail($params)
    {
        $order_info = OrderInfo::orderDetail($params);
        $order_goods = OrderGoods::orderGoodsDetail($params['order_sn']);
        if ($order_info && $order_goods) {
            $result['code'] = 1;
            $result['data']['order_info'] = $order_info;
            $result['data']['order_info'] ['goods_info'] = $order_goods;
        } else {
            $result['code'] = 10153;
            $result['msg'] = '未找到该订单';
        }
        return $result;
    }
    /**
     * 订单  删除
     * @param int order_id 订单ID
     * @return array
     */
    public function orderDelete($params)
    {
        $res = OrderInfo::orderDelete($params);
        if ($res) {
            $result['code'] = 1;
            $result['msg'] = '删除成功';
        } else {
            $result['code'] = 10154;
            $result['msg'] = '删除失败';
        }
        return $result;
    }
    /**
     * 订单  清除
     * @param string  order_sn 订单编号
     * @return array
     */
    public function orderClear($params)
    {
        $res1 = OrderInfo::orderClear($params);
        $res2 = OrderGoods::orderGoodsClear($params);
        if ($res1!==false && $res2!==false) {
            $result['code'] = 1;
            $result['msg'] = '订单清除成功';
        } else {
            $result['code'] = 10155;
            $result['msg'] = '订单清除失败';
        }
        return $result;
    }
    /**
     * 不分期合同生成脚本
     * @param $params
     * @return array
     *
     * author liyongchuan
     */
    public function orderUnLoan($params)
    {
        $data['date'] = isset($params['date']) ? $params['date'] : 0;
        if ($data['date'] == 0) {
            $time = time();
        } else {
            $time = strtotime($data['date']);
        }
        $month = get_month($time);
        $user_id_arr = OrderInfo::orderUserId($month);
        $result = ['code'=>10000,'msg'=>'没有不分期订单'];
        if (count($user_id_arr) != 0) {
            $data_risk = [];
            foreach ($user_id_arr as $key => $value) {
                $is_user=\UserService::userIsExistence($value);
                if($is_user['code']!=1){
                    continue;
                }
                $month['user_id'] = $value;
                $order = OrderInfo::userOrderInfo($month);
                $order_sn = '';
                $amount = 0;
                foreach ($order as $vo) {
                    $order_sn .= $vo['order_sn'] . ',';
                    $amount += $vo['order_amount'];
                }
                $order_sn = substr($order_sn, 0, strlen($order_sn) - 1);
                $data_risk[$key]['order_sn'] = $order_sn;
                $data_risk[$key]['month'] = 1;
                $data_risk[$key]['amount'] = $amount;
                $data_risk[$key]['user_id'] = $value;
                $data_risk[$key]['is_must'] = 0;
                #判断风控系统是否存有该用户
                $user_result = vget(\Config::get('interactive.riskcontrol.risk_is_user') . '?user_id=' . $value);
                $user_result = json_decode($user_result, true);
                $user_params['user_id'] = $value;
                $user_params['isset_loan'] = $user_result['code'];
                $user_info = \UserService::userInfo($user_params);
                $data_risk[$key]['user_name'] = $user_info['data']['real_name'];
                $data_risk[$key]['user_mobile'] = $user_info['data']['user_mobile'];
                $data_risk[$key]['user_idcard'] = $user_info['data']['user_idcard'];
                $data_risk[$key]['loan_product_id'] = 2;
                if ($user_result['code'] != 1) {
                    $user_data['user_id'] = $value;
                    $user_data['first_bill_date'] = date('Y-m', $time) . '-' . get_constant_cache('statement_date', 'credit') . ' 00:00:00';
                    $user_data['first_pay_date'] = date('Y-m', $time) . '-' . get_constant_cache('repayment_date', 'credit') . ' 00:00:00';
                    \UserService::userEditDate($user_data);
                    $data_risk[$key]['is_must'] = 1;
                    $data_risk[$key]['real_name'] = $user_info['data']['real_name'];
                    $data_risk[$key]['user_portrait'] = $user_info['data']['user_portrait'];
                    $data_risk[$key]['white_amount'] = $user_info['data']['white_amount'];
                    $data_risk[$key]['user_education'] = $user_info['data']['info']['user_education'];
                    $data_risk[$key]['user_profession'] = $user_info['data']['info']['user_profession'];
                    $data_risk[$key]['user_company'] = $user_info['data']['info']['user_company'];
                    $data_risk[$key]['user_income'] = $user_info['data']['info']['user_income'];
                    $data_risk[$key]['user_qq'] = $user_info['data']['info']['user_qq'];
                    $data_risk[$key]['user_email'] = $user_info['data']['info']['user_email'];
                    $data_risk[$key]['link_man'] = $user_info['data']['info']['link_man'];
                    $data_risk[$key]['link_mobile'] = $user_info['data']['info']['link_mobile'];
                    $data_risk[$key]['link_relation'] = $user_info['data']['info']['link_relation'];
                }
            }
            $data['data'] = json_encode($data_risk);
            $res = vpost(\Config::get('interactive.riskcontrol.uninstall_contract'), $data);
            $res = json_decode($res, true);
            if ($res['code'] != 1) {
                \Log::error('不分期生成合同出错', $res['data']);
                $result['code'] = 10000;
                $result['msg'] = '不分期账单日计划任务执行失败';
            } else {
                $result['code'] = 1;
                $result['msg'] = '不分期账单日计划任务执行结束';
            }
        }
        return $result;
    }
    /**
     * 我的账单
     * $params['user_id'] jwt
     * api/usercontroller调用
     *
     * @author caohan
     */
    public function userRepaymentsOrder($params)
    {
        $result = ['code' => 1, 'msg' => '查询成功'];
        $user_card = UserCard::userCardList($params);
        $user = User::userFind($params);
        #银行卡 支付方式
        $num = mb_strlen($user['real_name']);
        if ($num == 2) {
            $name = '*' . mb_substr($user['real_name'], -1, 1);
        } elseif ($num == 3) {
            $name = '*' . mb_substr($user['real_name'], -2, 2);
        } elseif ($num > 3) {
            $name = '**' . mb_substr($user['real_name'], -2, 2);
        } else {
            return ['code' => 10145, 'msg' => '真实姓名有误'];
        }
        foreach ($user_card as $key => $value) {
            $user_card[$key]['bank_logo'] = \Config::get('services.oss.host') . '/' . $user_card[$key]['bank_logo'];
            $user_card[$key]['card_name'] = $name;
            $user_card[$key]['card_type'] = "储蓄卡";
            if ($user_card[$key]['card_id'] == $user['card_id']) {
                $user_card[$key]['is_card_first'] = 1;
            } else {
                $user_card[$key]['is_card_first'] = 0;
            }
        }
        $result['data']['user_card'] = $user_card;

        #初始化后面要用的数据
        $bill_list = [
            'account' =>
                ['info' => [
                    "total_surplus_pay_fee" => "0.00",
                    "total_should_pay_fee" => "0.00",
                    "total_real_pay_fee" => "0.00",
                    "total_overdue_fee" => "0.00"
                ], 'install_list' => [], 'uninstall_list' => [],],
            'unaccount' =>
                ['info' => '', 'install_list' => [], 'uninstall_list' => [],]
        ];
        /*TODO *****************************************************************往期************************************************************/
        if (isset($params['date'])) {  //选择月份的查询
            $params_send = ['user_id' => $params['user_id'], 'date' => $params['date']];
            $data_result = vpost(\Config::get('interactive.riskcontrol.account_list'), $params_send);
            $data_result = json_decode($data_result, true);//获取RC的数据
            if (isset($data_result['code'])) {
                if ($data_result['code'] == 1) {
                    $bill_list = $data_result['data'];
                    #按日期查询 order_list
                    //$time = strtotime($params['date']);
                    //$year=date('Y',$time);
                    //$month=date('m',$time);
                    //$condition['this_month']=date("Y-m-d H:i:s",mktime(0,0,0,$month-1,1,$year));
                    //$condition['next_month']=date("Y-m-d H:i:s",mktime(0,0,0,$month,1,$year));
                    //$condition['user_id'] = $params['user_id'];
                    //$order_sn_arr = OrderInfo::orderOrderSn($condition);
                    $order_sn_arr = $data_result['data']['account']["uninstall_list"]["order_sn"];
                    unset($bill_list['account']["uninstall_list"]["order_sn"]);
                    $order_sn_arr_explode = explode(",", $order_sn_arr);
                    if (!empty($order_sn_arr)) {
                        $bill_list['account']['uninstall_list'] = OrderGoods::orderGoodsDetailWhereIn($order_sn_arr_explode);   //通过order_sn wherein数据 添加到list里
                    }
                }
            }
        } /*TODO *************************************************************当期*************************************************************/
        else {
            $params_send = ['user_id' => $params['user_id'], 'date' => ''];
            $data_result = vpost(\Config::get('interactive.riskcontrol.account_list'), $params_send);
            $data_result = json_decode($data_result, true);
            if (isset($data_result['code'])) {
                if ($data_result['code'] == 1) {
                    $bill_list = $data_result['data'];
                    #查询已出账 order_list （数据是上个月的  这个月需要还款）
                    $order_sn_arr = $data_result['data']['account']["uninstall_list"]["order_sn"];
                    unset($bill_list['account']["uninstall_list"]["order_sn"]);
                    if (!empty($order_sn_arr)) {
                        $order_sn_arr_explode = explode(",", $order_sn_arr);
                        $bill_list['account']['uninstall_list'] = OrderGoods::orderGoodsDetailWhereIn($order_sn_arr_explode);
                    }
                    #以下查询方法注释
//                    $condition = get_month();
//                    $condition['user_id'] = $params['user_id'];
//                    $order_sn_arr = OrderInfo::orderOrderSn($condition);
//                    if (!empty($order_sn_arr)) {
//                        $bill_list['account']['uninstall_list'] = OrderGoods::orderGoodsDetailWhereIn($order_sn_arr);   //通过order_sn wherein数据 添加到list里
//                    }
                }
                #未出账  order_list 不是模拟数据  这个月的
                $condition = get_month_time();//这个月的时间戳  月初 下月初
                $condition['user_id'] = $params['user_id'];
                $order_info_bill = OrderInfo::userOrderInfo($condition);//查询订单
                if (!empty($order_info_bill)) {
                    foreach ($order_info_bill as $key => $value) {
                        $value['created_date'] = substr($value['created_at'], 0, 10);
                        $value['order_amount'] = number_format($value['order_amount'], 2, '.', '');
                        $order_from = $value['order_from'] == 1 ? '线上商城' : '线下门店';
                        $value['order_from'] = $order_from;
                        $goods_info = OrderGoods::orderGoodsDetailFirst($value['order_sn']);
                        $value['goods_id'] = $goods_info['goods_id'];
                        $value['product_id'] = $goods_info['product_id'];
                        $value['goods_name'] = $goods_info['goods_name'];
                        $value['goods_thumb'] = $goods_info['goods_thumb'];
                        $bill_list['unaccount']['info']['total_plan_pay_fee'] += $value['order_amount'];//剩余应还 累加

                    }
                    $bill_list['unaccount']['info']['total_plan_pay_fee'] = number_format((string)$bill_list['unaccount']['info']['total_plan_pay_fee'], 2, '.', '');
                    $bill_list['unaccount']['uninstall_list'] = $order_info_bill;
                }
            }
        }
        /*TODO *************************************************************结束*************************************************************/
        $result['data']['bill_list'] = $bill_list;//赋值
        $result['data']['pay_type']=env('PAY_TYPE',1);
        return $result;
    }
    /**
     * 订单添加（联通）
     * @param string  order_sn 订单编号
     * @return array
     */
    public function tradeInfo($params)
    {
        $order_sn = get_sn('O');
        $params['ex_trade_id'] = $order_sn;
        $params['trade_inmode'] = '001';
        $params['trade_source'] = 'EMALL';
        $params['trade_acc_type'] = 'SELF00';
        $params['client_name'] = '光光';
        $params['client_ip'] = get_ip();
        $params['trade_time'] = date('Ymd H:i:s',time());
        $params['trade_staff_id'] = '';
        $params['trade_dept_id'] = '';
        $params['trade_province'] = '0010';
        $params['trade_city'] = '0011';
        $params['remark'] = '尽早发货';
        $params['invoice_type'] = '01';
        $params['invoice_title'] = '宁波维凯网络发票抬头';
        $params['invoice_content'] = '宁波维凯网络发票内容';
        //dd(env('CUCC_DOMAIN').'/OrderReceive/tradeInfo');
        //$result = vpost(env('CUCC_DOMAIN').'/OrderReceive/tradeInfo', $params);
        $t = '15105840179';
        $p = 'G';
        $b = '224';
        $a = '0';
        $u = '11';
        $m = md5($t.$p.$b.$a.$u);
        //dd(env('CUCC_DOMAIN').'/bj-vip/productOrder/t/'.$t.'/p/'.$p.'/b/'.$b.'/a/'.$a.'/u/'.$u.'/m/'.$m);
        $result = vget(env('CUCC_DOMAIN').'/bj-vip/productOrder/t/'.$t.'/p/'.$p.'/b/'.$b.'/a/'.$a.'/u/'.$u.'/m/'.$m);
        //dd($result);
        return $result;
    }
}
