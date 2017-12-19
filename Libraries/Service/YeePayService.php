<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29 0029
 * Time: 下午 16:18
 */

namespace Libraries\Service;

use Libraries\Help\YeePay\yeepayMPay;

class YeePayService
{
    public $yeepay;
    public $merchantaccount;

    public function __construct()
    {
        $this->yeepay = new yeepayMPay(
            \Config::get('services.yeepay.merchantaccount'),
            \Config::get('services.yeepay.merchantPublicKey'),
            \Config::get('services.yeepay.merchantPrivateKey'),
            \Config::get('services.yeepay.yeepayPublicKey'));
        $this->merchantaccount = \Config::get('services.yeepay.merchantaccount');
    }

    /**
     * 第三放绑定银行卡，并且返回bind_id
     * @param $cardno   string      银行卡号
     * @param $name     string      真实姓名
     * @param $card_type    string  卡类型，01借记卡，02信用卡
     * @param $certificate_type     int 认证方式，1身份证
     * @param $identity     string  身份证号
     * @param $telno    string      预留手机号
     * @param $mer_tag  string      用户标识
     * @return array
     *
     * @author      liyongchuan
     * @time        2017.8.30
     */
    public function bankCard($cardno, $name, $card_type, $certificate_type, $identity, $telno, $mer_tag)
    {
        try {
            $is_success_bank = $this->yeepay->bind($cardno, $name, $card_type, $certificate_type, $identity, '', $telno, '', $mer_tag);
            if ($is_success_bank['status'] == 0) {
                $return = ['code' => 1, 'data' => $is_success_bank['data']];
            } else {
                $return = ['code' => 10000,'data'=>$is_success_bank, 'msg' => '第三方绑定银行卡失败'];
            }
            return $return;
        } catch (\Exception $e) {
            return ['code' => 99999, 'msg' => '运行有误'];
        }
    }

    /**
     * 第三方解绑银行卡
     * @param $cardno   string     银行卡卡号
     * @param $bind_id  string      绑卡id
     * @return array
     *
     * @author      liyongchuan
     */
    public function unbank($cardno, $bind_id)
    {
        try {
            $is_success_unbank = $this->yeepay->unBind($bind_id,$cardno);
            if ($is_success_unbank['status'] == 0) {
                $return = ['code' => 1, 'data' => $is_success_unbank];
            } else {
                $return = ['code' => 10000, 'data'=>$is_success_unbank,'msg' => '第三方解绑银行卡失败'];
            }
        } catch (\Exception $e) {
            return ['code' => 99999, 'msg' => '运行有误'];
        }
        return $return;
    }

    /**
     * 发送支付请求
     * @param $params
     *
     * @return string
     */
    public function bankPay($params)
    {
        return $this->yeepay->webPay($params);
    }

    /**
     * 解析异步回调的值
     * @param $data
     * @param $encryptkey
     * @return array
     */
    public function callback($data,$encryptkey)
    {
        return $this->yeepay->callback($data,$encryptkey);
    }
}