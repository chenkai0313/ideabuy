<?php
/**
 * 支付记录表
 * Author: 葛宏华
 * Date: 2017/8/2
 */
namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;

class PayLog extends Model
{
    protected $table      = 'pay_log';

    protected $primaryKey = 'log_id';

    protected $fillable = ['order_sn','pay_money','pay_id','from_type','trade_no','updated_at'];



    /**
     * 支付记录  添加
     * @param string $order_sn 订单编号
     * @param string $pay_money 支付金额
     * @param int $pay_id 支付方式
     * @param int $from_type 来源
     * @param string $trade_no 第三方交易号
     * @return object
     */
    public static function payLogAdd($params){
        $result = PayLog::updateOrCreate($params);
        return $result;
    }

    /**
     * 支付日志列表总数
     * @param $$params array 搜索关键词条件数组
     * @return int
     */
    public static function payLogCount($params)
    {
        return PayLog::Search($params)->count();
    }

    /**
     * 支付日志查询列表
     * @param $params['limit'] 一页的数据
     * @param $params['page'] 当前页
     * @param string $order_sn 订单编号
     * @param int $pay_id 支付方式
     * @param string $trade_no 第三方交易号
     * @return array
     */
    public static function payLogList($params){
        $offset=($params['page']-1)*$params['limit'];
        $result = PayLog::Search($params)->orderBy('log_id', 'desc')->skip($offset)->take($params['limit'])->get()->toArray();
        return $result;
    }

    #查询构造器 Like
    public function scopeSearch($query, $keyword){
        return $query->where(function($query) use($keyword) {
                // 订单编号
                if (isset($keyword['order_sn']) && $keyword['order_sn']) {
                    $query->where('order_sn', 'like', '%' . strip_tags($keyword['order_sn']) . '%');
                }
                // 支付方式 可为空  1白条，2支付宝，3微信，4银联
                if (isset($keyword['pay_id']) && $keyword['pay_id']) {
                    $query->where('pay_id', $keyword['pay_id']);
                }
                // 第三方交易号
                if (isset($keyword['trade_no']) && $keyword['trade_no']) {
                    $query->where('trade_no', $keyword['trade_no']);
                }
            });
    }

    /**
     * 支付日志详情
     * @param int $log_id 日志id
     * @return array
     */
    public static function payLogDetail($params){
        $result = PayLog::where('log_id', $params['log_id'])->first();
        return $result;
    }
    /**
     * 支付日志详情(order_sn查询)
     * @param int $log_id 日志id
     * @return array
     */
    public static function payLogFind($params){
        $result = PayLog::where('order_sn', $params)->first();
        return $result;
    }

    /**
     * 已order_sn查询，没有就新建
     * @param $params
     * @return Model
     */
    public static function payLogFirstOrCreate($params)
    {
        return PayLog::firstOrCreate(['order_sn'=>$params]);
    }

    /**
     * 修改payLog表
     * @param $params
     * @return mixed
     * @author      liyongchuan
     */
    public static function payLogUpdate($params)
    {
        $pay_log=PayLog::firstOrCreate(['order_sn'=>$params['order_sn']]);
        $pay_log->pay_money=$params['pay_money'];
        $pay_log->pay_id=$params['pay_id'];
        $pay_log->trade_no=$params['trade_no'];
        return $pay_log->save();
    }
}