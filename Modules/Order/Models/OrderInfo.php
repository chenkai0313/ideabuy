<?php
/**
 * 订单表
 * Author: 葛宏华
 * Date: 2017/8/2
 */

namespace Modules\Order\Models;

use EasyWeChat\Payment\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Models\User;
class OrderInfo extends Model
{
    use SoftDeletes;

    protected $table = 'order_info';

    protected $primaryKey = 'order_id';

    protected $fillable = ['order_sn', 'user_id','admin_id', 'pay_id', 'order_status', 'goods_amount', 'freight_amount', 'order_amount', 'consignee', 'province', 'city', 'district', 'street', 'address', 'mobile', 'order_from', 'loan_product_id', 'order_remark','created_at'];

    protected $dates = ['deleted_at'];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
        #只添加updated_at不添加created_at
        static::creating(function ($model) {
            $model->updated_at = $model->freshTimestamp();
        });
    }
    /**
     * 订单 列表
     * @param int $limit 每页显示数量
     * @param int $page 当前页数
     * @return array
     */
    public static function orderList($params)
    {
        #参数
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 10;
        $params['status'] = isset($params['status']) ? $params['status'] : '';
        $where = [];
        switch ($params['status']){
            case 'dfk' : $where = ['order_status' => 0]; break;
            case 'dfh' : $where = ['order_status' => 1]; break;
            case 'dsh' : $where = ['order_status' => 2]; break;
            case 'dpj' : $where = ['order_status' =>  3,'is_comment'=>0]; break;
            default;
        }
        #获取数据
        $keyword = isset($params['keyword']) ? $params['keyword'] : '';
        $total = OrderInfo::where(['user_id'=>$params['user_id']])
            ->where($where)
            ->where(function ($query) use ($keyword) {
            $query->where('order_sn', 'like', '%' . strip_tags($keyword) . '%');
        })->count();
        $pages = ceil($total / $params['limit']);
        $list = OrderInfo::select(['order_id','order_sn', 'user_id', 'pay_id', 'order_status', 'goods_amount', 'freight_amount', 'order_amount', 'loan_product_id', 'order_remark'])
            ->where(['user_id'=>$params['user_id']])
            ->where($where)
            ->where(function ($query) use ($keyword) {
                $query->where('order_sn', 'like', '%' . strip_tags($keyword) . '%');
            })
            ->orderBy('created_at', 'DESC')->paginate($params['limit'])->toArray()['data'];
        foreach ($list as $k => $v) {
            switch ($v['order_status']) {
                case 0 : $status_name = '待付款'; break;
                case 1 : $status_name = '待发货'; break;
                case 2 : $status_name = '待收货'; break;
                case 3 : $status_name = '已完成'; break;
                case 4 : $status_name = '已取消'; break;
                default : $status_name = '';
            }
            $list[$k]['status_name'] = $status_name;
            $list[$k]['order_amount'] = number_format($v['order_amount'],2,'.','');
        }
        #返回
        $result['list'] = $list;
        $result['total'] = $total;
        $result['pages'] = $pages;
        return $result;
    }
    /**
     * 订单  添加
     * @param string $order_sn 账号
     * @param string $order_password 密码
     * @return array
     */
    public static function orderAdd($params)
    {
        $result = OrderInfo::insert($params);
        return $result;
    }
    /**
     * 订单 支付完成
     * @param int $order_id 订单ID
     * @param string $order_password 密码
     * @return array
     */
    public static function orderPayFinish($order_sn)
    {
        $result = OrderInfo::where('order_sn',$order_sn)->update(['order_status'=>1]);
        return $result;
    }
    /**
     * 订单 确认收货
     * @param int $order_id 订单ID
     * @param string $order_password 密码
     * @return array
     */
    public static function orderFinish($params)
    {
        $result = OrderInfo::where(['order_sn'=>$params['order_sn'],'user_id'=>$params['user_id']])->update(['order_status'=>3]);
        return $result;
    }
    /**
     * 订单  更新product_id
     * @return array
     * @author 曹晗
     */
    public static function orderEditProductID($order_sn_arr) {
        $result = OrderInfo::whereIn('order_sn',$order_sn_arr)->update(['loan_product_id'=>'1']);
        return $result;
    }
    /**
     * 订单  详情
     * @param int $order_id 订单ID
     * @return array
     */
    public static function orderDetail($params)
    {
        $result = OrderInfo::select(['order_id','order_sn', 'user_id', 'pay_id', 'order_status', 'goods_amount', 'freight_amount', 'order_amount', 'consignee', 'province', 'city', 'district', 'street', 'address', 'mobile', 'order_from', 'loan_product_id', 'order_remark','created_at'])->where(['order_sn' => $params['order_sn'], 'user_id' => $params['user_id']])->first();
        return $result;
    }
    /**
     * 订单  详情
     * @param int $order_id 订单ID
     * @return array
     */
    public static function orderAmount($params)
    {
        $arr = explode(',',$params['order_sn']);
        $result = OrderInfo::where('user_id',$params['user_id'])
            ->where('order_status',0)
            ->whereIn('order_sn',$arr)->sum('order_amount');
        return $result;
    }
    /**
     * 订单  详情  根据订单号
     * @param int $order_sn 订单ID
     * @return array
     */
    public static function orderDetailSn($order_sn)
    {
        return OrderInfo::where(['order_sn' => $order_sn])->first()->toArray();
    }
    /**
     * 订单支付支付宝状态保存
     * @param $params
     * @return mixed
     */
    public static function orderPayStatusChange($params){
        $order = OrderInfo::where('order_sn', $params['order_sn'])->first();
        $order['pay_id'] = $params['pay_id'];
        $order['order_status'] = $params['order_status'];
        $result = $order->save();
        return $result;
    }
    /**
     * 订单  删除
     * @param int $order_id 订单ID
     * @return array
     */
    public static function orderDelete($params)
    {
        $order_ids = explode(',', $params['order_id']);
        $result = OrderInfo::whereIn('order_id', $order_ids)->where('user_id', $params['user_id'])->delete();
        return $result;
    }
    /**
     * 订单  清除
     * @param int $order_id 订单ID
     * @return array
     */
    public static function orderClear($params)
    {
        return OrderInfo::where('user_id', $params['user_id'])->forceDelete();
    }
    /**
     * 发票 列表
     * @param int $limit 每页显示数量
     * @param int $page 当前页数
     * @return array
     */
    public static function invoiceList($params)
    {
        #参数
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 10;
        $status =  isset($params['status']) ? $params['status'] : '';
        #获取数据
        $total = OrderInfo::where(['user_id'=>$params['user_id']])
            ->where('invoice_type','>',0)
            ->where(function ($query) use ($status) {
                if($status!==''){
                    $query->where('invoice_issue','=',$status);
                }
            })
            ->count();
        $pages = ceil($total / $params['limit']);
        $list = OrderInfo::select(['order_id','order_sn','invoice_issue','created_at'])
            ->where(['user_id'=>$params['user_id']])
            ->where('invoice_type','>',0)
            ->where(function ($query) use ($status) {
                if($status!==''){
                    $query->where('invoice_issue','=',$status);
                }
            })
            ->orderBy('created_at', 'DESC')->paginate($params['limit'])->toArray()['data'];
        #返回
        $result['list'] = $list;
        $result['total'] = $total;
        $result['pages'] = $pages;
        return $result;
    }
    /**
     * 发票 详细
     * @param int $order_sn,发票订单号
     * @return array
     */
    public static function invoiceDetail($params)
    {
        $result = OrderInfo::select(['order_id','order_sn','invoice_code','invoice_title','invoice_type','invoice_issue'])
            ->where(['user_id'=>$params['user_id']])
            ->where(['order_sn'=>$params['order_sn']])
            ->where('invoice_type','>',0)
            ->first();
        return $result;
    }
    /**
     * 不分期当月所有用户
     * @param $params
     * @return mixed
     */
    public static function orderUserId($params)
    {
        return OrderInfo::UnLoan($params)->groupBy('user_id')->pluck('user_id');
    }
    public static function orderOrderSn($params)
    {
        return OrderInfo::where('created_at', '>=', $params['this_month'])->
        where('created_at', '<', $params['next_month'])->where('loan_product_id', 2)->
        where('pay_id', 1)->where('user_id',$params['user_id'])->pluck('order_sn');
    }
    /**
     * 获取用户下的本月所有订单
     * @param $params
     * @return mixed
     */
    public static function userOrderInfo($params)
    {
        return OrderInfo::select('order_sn','order_amount','order_from','created_at')->UnLoan($params)->where('user_id',$params['user_id'])->get();
    }

    /**
     * 不分期查询订单条件
     * @param $query
     * @param $params
     * @return mixed
     */
    public function scopeUnLoan($query, $params)
    {
        return $query->where('created_at', '>=', $params['this_month'])->
        where('created_at', '<', $params['next_month'])->where('loan_product_id', 2)->
        where('white_is_pay_off', 0)->where('pay_id', 1);
    }
    /**
     * 指派订单给供应商
     * @param int $limit 每页显示数量
     * @param int $page 当前页数
     * @return array
     */
    public static function assignOrder($params)
    {
        $data = OrderInfo::where('order_sn',$params['order_sn']);
        $data->supplier_id = $params['supplier_id'];
        $data->assign_at = date('Y-m-d H:i:s',time());
        $res = $data->save();
        return $res;
    }
    /**
     * 供应商订单查询
     * @param int $limit 每页显示数量
     * @param int $page 当前页数
     * @param int $supplier_id 供应商ID
     * @return array
     */
    public static function supplierOrderList($params)
    {
        #参数
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 10;
        #获取数据
        $keyword = isset($params['keyword']) ? $params['keyword'] : '';
        $total = OrderInfo::where(['supplier_id'=>$params['supplier_id']])->where(function ($query) use ($keyword) {
            $query->where('order_sn', 'like', '%' . strip_tags($keyword) . '%');
        })->count();
        $pages = ceil($total / $params['limit']);
        $list = OrderInfo::select(['order_sn', 'user_id', 'pay_id', 'order_status', 'goods_amount', 'freight_amount', 'order_amount', 'loan_product_id', 'order_remark'])
            ->where('supplier_id',$params['supplier_id'])
            ->where(function ($query) use ($keyword) {
                $query->where('order_sn', 'like', '%' . strip_tags($keyword) . '%');
            })
            ->orderBy('created_at', 'DESC')->paginate($params['limit'])->toArray()['data'];
        foreach ($list as $k => $v) {
            switch ($v['order_status']) {
                case 0;
                    $status_name = '待付款';
                    break;
                case 1;
                    $status_name = '待发货';
                    break;
                case 2;
                    $status_name = '待收货';
                    break;
                case 3;
                    $status_name = '确认收货';
                    break;
                case 4;
                    $status_name = '已取消';
                    break;
                case 5;
                    $status_name = '已完成';
                    break;
                default;
                    $status_name = '';
            }
            $list[$k]['status_name'] = $status_name;
            $list[$k]['order_amount'] = number_format($v['order_amount'],2,'.','');
        }
        #返回
        $result['list'] = $list;
        $result['total'] = $total;
        $result['pages'] = $pages;
        return $result;
    }
    /**
     * 供应商发货操作
     * @param int $order_status 订单状态
     * @param int $send_at 供应商发货时间
     * @param int $supplier_id 供应商ID
     * @param int $order_id 订单ID
     * @return bool
     */
    public static function supplierOrderSend($params)
    {
        $data = OrderInfo::find($params['order_id']);
        $data -> order_status = 2;
        $data -> send_at = date('Y-m-d H:i:s',time());
        $res = $data -> save();
        return $res;
    }
    public static function orderInfoCanInstall($params) {
        $result = OrderInfo::whereIn('order_sn',$params['order_sn_arr_explode'])->where('loan_product_id',2)
            ->where('created_at', '>=', $params['this_month'])->
            where('created_at', '<', $params['next_month'])
            ->count();
        return $result;
    }
    /**
     * 后台查询订单详情
     * @Author  CK
     * @param $params ['order_id'] 订单ID
     * @return array
     */
    public static function backendOrderDetail($params)
    {
        $data = OrderInfo::select('order_id', 'user_id', 'order_sn', 'pay_id', 'order_status', 'goods_amount', 'freight_amount',
            'order_amount', 'consignee', 'province', 'city', 'district', 'address', 'mobile', 'order_from', 'loan_product_id', 'month',
            'order_remark', 'white_is_pay_off', 'created_at')
            ->where('order_id', '=', $params['order_id'])
            ->first();
        $data['goods_info'] = OrderGoods::where('order_sn', '=', $data['order_sn'])->get();
        #订单商品数据
        if ($data['order_status'] == 0) {
            $data['order_status'] = '待付款';
        }
        if ($data['order_status'] == 1) {
            $data['order_status'] = '待发货';
        }
        if ($data['order_status'] == 2) {
            $data['order_status'] = '待收货';
        }
        if ($data['order_status'] == 3) {
            $data['order_status'] = '确认收货';
        }
        if ($data['order_status'] == 4) {
            $data['order_status'] = '已取消';
        }
        if ($data['order_status'] == 5) {
            $data['order_status'] = '已完成';
        }
        #商品属性字符串
        foreach ($data['goods_info'] as $k => $v) {
            $str_attr = '';
            if ($v['attr_name']) {
                $temp_attr_name = explode("|", $v['attr_name']);
                $temp_attr_value = explode("|", $v['attr_value']);
                $j = count($temp_attr_name);
                for ($i = 0; $i < $j; $i++) {
                    $str_attr .= $temp_attr_name[$i] . ":" . $temp_attr_value[$i] . ' ';
                }
            }
            $data['goods_info'][$k]['str_attr'] = $str_attr;
            unset($data['goods_info'] [$k]['attr_name']);
            unset($data['goods_info'] [$k]['attr_value']);
        }
        return $data;
    }
    /**
     * 查询所有订单
     * @Author  CK
     * @param $params ['limit'] 一页的数据
     * @param $params ['page'] 当前页
     * @param $params ['keyword'] 查询关键字 可为空
     * @return array
     */
    public static function backendOrderList($params)
    {
        $offset = ($params['page'] - 1) * $params['limit'];
        $data = OrderInfo::leftJoin('users', 'users.user_id', '=', 'order_info.user_id')
            ->Search($params)
            ->select('order_info.order_id', 'order_info.user_id',
                'order_info.order_sn', 'order_info.pay_id', 'order_info.order_status', 'order_info.goods_amount'
                , 'order_info.freight_amount', 'order_info.order_amount', 'order_info.consignee', 'order_info.province'
                , 'order_info.city', 'order_info.district', 'order_info.address', 'order_info.mobile'
                , 'order_info.order_from', 'order_info.loan_product_id', 'order_info.month', 'order_info.order_remark', 'order_info.white_is_pay_off'
                , 'order_info.created_at')
            ->orderBy('order_id', 'desc')
            ->skip($offset)
            ->take($params['limit'])
            ->get()->toArray();
        if($data){
            foreach ($data as $k => $v) {
                $good_info = OrderGoods::where('order_sn', '=', $v['order_sn'])->get();
                $data[$k] ['goods_info'] = $good_info;
                #订单商品数据
                if ($v['order_status'] == 0) {
                    $v['order_status'] = '待付款';
                }
                if ($v['order_status'] == 1) {
                    $v['order_status'] = '待发货';
                }
                if ($v['order_status'] == 2) {
                    $v['order_status'] = '待收货';
                }
                if ($v['order_status'] == 3) {
                    $v['order_status'] = '确认收货';
                }
                if ($v['order_status'] == 4) {
                    $v['order_status'] = '已取消';
                }
                if ($v['order_status'] == 5) {
                    $v['order_status'] = '已完成';
                }
                #商品属性字符串
                if (isset($good_info)) {
                    foreach ($good_info as $k => $v) {
                        $str_attr = '';
                        if ($v['attr_name']) {
                            $temp_attr_name = explode("|", $v['attr_name']);
                            $temp_attr_value = explode("|", $v['attr_value']);
                            $j = count($temp_attr_name);
                            for ($i = 0; $i < $j; $i++) {
                                $str_attr .= $temp_attr_name[$i] . ":" . $temp_attr_value[$i] . ' ';
                            }
                        }
                        $good_info[$k]['str_attr'] = $str_attr;
                        unset($good_info [$k]['attr_name']);
                        unset($good_info [$k]['attr_value']);
                    }
                }
            }
        }
        return $data;
    }

    public static function backendOrderCount($params)
    {
        return OrderInfo::leftJoin('users', 'users.user_id', '=', 'order_info.user_id')->Search($params)
            ->count();
    }

    #查询构造器 Like
    public function scopeSearch($query, $params)
    {
        if (!empty($params['keyword'])) {
            return $query->where('users.real_name', 'like', '%' . $params['keyword'] . '%')
                ->orwhere('order_info.mobile', '=', $params['keyword'])
                ->orwhere('order_info.order_sn', '=', $params['keyword'])
                ->orwhere('users.user_idcard', '=', $params['keyword']);
        }
    }
}