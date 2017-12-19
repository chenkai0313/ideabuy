<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/8/3
 * Time: 19:05
 */

namespace Modules\System\Services;

use Modules\System\Models\BankInfo;

class BankInfoService
{
    /**
     * 银行ID
     * @param $bank_id
     * @return array
     */
    public function bankInfoGet($bank_id)
    {
        $bank_id = BankInfo::bankInfoGet($bank_id);
        if ($bank_id) {
            $return = ['code' => 1, 'data' => $bank_id];
        } else {
            $return = ['code' => 10146, 'msg' => '暂不支持该银行'];
        }
        return $return;
    }

    /**
     * 获取银行的所有名字
     * @return \Illuminate\Support\Collection
     */
    public function bankNameList()
    {
        $bank = BankInfo::bankNameList();
        foreach ($bank as $key=>$vo){
            $bank[$key]['bank_logo']=\Config::get('services.oss.host') . '/' . $vo['bank_logo'];
        }
        return ['code' => 1, 'data' => $bank];
    }

    /**
     * 获取bankInfo详情
     *
     * @param $bank_id
     * @return array
     *
     * @author  liyongchuan
     * @time    2017-09-22
     */
    public function BankInfoDetail($bank_id)
    {
        $bankInfo = BankInfo::BankInfoDetail($bank_id);
        if ($bankInfo) {
            $return = ['code' => 1, 'data' => ['bank_info' => $bankInfo]];
        } else {
            $return = ['code'=>10147,'msg'=>'查询失败'];
        }
        return $return;
    }
}