<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/15 0015
 * Time: 下午 14:13
 */

namespace Modules\Order\Services;

use Modules\Order\Models\PayLog;

class PayLogService
{
    /**
     * PayLog新增成功
     * @param $params
     * @return array
     *
     * @author  liyongchuan
     */
    public function payLogAdd($params)
    {
        $pay_log = PayLog::payLogFirstOrCreate($params);
        if ($pay_log) {
            $return = ['code' => 1, 'msg' => '新增成功'];
        } else {
            $return = ['code' => 10000, 'msg' => '新增失败'];
        }
        return $return;
    }

    /**
     * payLog查询
     * @param $params
     * @return array
     *
     * @author  liyongchuan
     */
    public function payLogFind($params)
    {
        $pay_log = PayLog::payLogFind($params);
        if ($pay_log) {
            $return = ['code' => 1, 'data' =>['payLog_info'=>$pay_log] , 'msg' => '支付记录'];
        } else {
            $return = ['code' => 10000, 'msg' => '支付记录不存在'];
        }
        return $return;
    }

    /**
     * paylog 修改状态
     *
     * @param $params
     * @return array
     *
     * @author  liyongchuan
     */
    public function payLogUpdate($params)
    {
        $upd_bool=PayLog::payLogUpdate($params);
        if($upd_bool){
            $return=['code'=>1,'msg'=>'修改成功'];
        }else{
            $return=['code'=>10000,'msg'=>'修改失败'];
        }
        return $return;
    }
}
