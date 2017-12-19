<?php
/**
 * 订单商品商品表
 * Author: 葛宏华
 * Date: 2017/8/2
 */
namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;

class OrderGoods extends Model
{
    protected $table      = 'order_goods';

    protected $primaryKey = 'id';

    protected $fillable = ['order_sn','goods_key','goods_id','product_id','goods_name','goods_thumb','attr_name','attr_value','product_price','market_price','goods_amount','goods_number','goods_unit'];

    public $timestamps = false;

    /**
     * 订单商品  添加
     * @param string $order_sn 订单商品编号
     * @param int $product_id 货品ID
     * @param string $goods_name 商品名称
     * @param string $goods_thumb 商品缩略图
     * @param string $attr_name 商品属性名称
     * @param string $attr_value 商品属性值
     * @param int $product_price 商品平台价
     * @param int $market_price 商品市场价
     * @param int $goods_number 商品数量
     * @param string $goods_unit 商品单位
     * @param string $goods_amount 商品价格合计
     * @return array
     */
    public static function orderGoodsAdd($params){
        $result = OrderGoods::insert($params);
        return $result;
    }
    /**
     * 订单商品  编辑
     * @param int $order_id 订单商品ID
     * @param string $order_password 密码
     * @return array
     */
    public static function orderEdit($params){
        $order = OrderGoods::find($params['order_id']);
        if($params['order_password']){
            $order->order_password = bcrypt($params['order_password']);
        }
        $order->order_sex = $params['order_sex'];
        $order->order_birthday = $params['order_birthday'];
        $order->order_nick = $params['order_nick'];
        $result = $order->save();
        return $result;
    }
    /**
     * 订单商品  清除
     * @param int $order_id 订单ID
     * @return array
     */
    public static function orderGoodsClear($params)
    {
        return OrderGoods::where('order_sn', $params['user_id'])->forceDelete();
    }
    /**
     * 订单商品  详情
     * @param int $order_id 订单商品ID
     * @return array
     */
    public static function orderGoodsDetail($order_sn){
        $result = OrderGoods::select(['order_sn','goods_id','product_id','goods_name','goods_thumb','attr_name','attr_value','product_price','market_price','goods_amount','goods_number','goods_unit'])->where('order_sn',$order_sn)->get();
        foreach($result as $k=>$v){
            $str_attr = '';
            if($v['attr_name']){
                #商品属性字符串
                $temp_attr_name = explode("|",$v['attr_name']);
                $temp_attr_value = explode("|",$v['attr_value']);
                $j = count($temp_attr_name);
                for($i=0;$i<$j;$i++){
                    $str_attr .= $temp_attr_name[$i].":".$temp_attr_value[$i].' ';
                }
            }
            $result[$k]['str_attr'] = $str_attr;
            unset($result[$k]['attr_name']);
            unset($result[$k]['attr_value']);
        }
        return $result;
    }
    /**
     * 订单商品  单条记录
     * @param string $goods_key 订单商品唯一码
     * @return array
     */
    public static function orderGoodsOne($goods_key){
        return OrderGoods::where('goods_key',$goods_key)->first();
    }
    /**
     * 订单商品  详情
     * @param int $order_id 订单商品ID  单条
     * @return array
     */
    public static function orderGoodsDetailFirst($order_sn){
        $result = OrderGoods::select(['goods_id','product_id','goods_name','goods_thumb'])->where('order_sn',$order_sn)->first();
        return $result;
    }

    /**
     * 订单商品  多条
     * @param int $order_id 订单商品ID  单条
     * @return array
     * @author 曹晗
     */
    public static function orderGoodsDetailWhereIn($order_sn){
        $result = OrderGoods::join('order_info','order_goods.order_sn','=','order_info.order_sn')
            ->select(['order_goods.goods_id','order_goods.product_id','order_goods.goods_name','order_goods.goods_thumb'
                ,'order_info.order_sn','order_info.order_from','order_info.order_amount','order_info.created_at'])
            ->whereIn('order_goods.order_sn',$order_sn)->get();
        foreach ($result as $key => $value) {
            $value['created_date'] = substr($value['created_at'],0,10);
            $order_from = $value['order_from'] == 1 ? '线上商城' : '线下门店';
            $value['order_from'] = $order_from;
            $value['order_amount'] = number_format($value['order_amount'],2,'.','');
        }
        return $result;
    }
}