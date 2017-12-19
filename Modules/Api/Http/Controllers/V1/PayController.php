<?php

namespace Modules\Api\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PhpParser\Node\Scalar\String_;

class PayController extends Controller
{
    /**
     * 支付宝网页支付
     * @param Request $request
     * @return array
     */
    public function aliPayWeb(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = get_user_id();
        $result = \PayService::aliPayWeb($params);
        return $result;
    }

    /**
     * 异步通知
     * @param Request $request
     * @return string
     */
    public function webNotify(Request $request)
    {
        $params = $request->all();
        if (!empty($params['s'])) {
            unset($params['s']);
        }
        $result = \PayService::webNotify($params);
        return $result;
    }

    /**
     * 嘉联支付付款接口
     * @param Request $request
     * @return array
     *
     * @author  liyongchuan
     */
    public function yeePay(Request $request)
    {
        $params = $request->input();
        $params['user_id']=get_user_id();
        $data = $this->parameterConstruction($params['user_id']);
        $is_pay = \PayLogService::payLogFind($data['order_id']);
        if ($is_pay['code'] == 1) {
            if (empty($is_pay['data']['payLog_info']['trade_no'])) {
                $re = ['code' => 10180, 'msg' => '付款请求正在处理，请耐心等候···'];
            } else {
                $re = ['code' => 10181, 'msg' => '付款请求已经处理，请稍后查看···'];
            }
            return $re;
        }
        $data['amount'] = $params['amount'] * 100;
        $data['mer_tag'] = '' . get_user_id();
        $user_card = \UserService::userCartDetail($params);
        if ($user_card['code'] == 1) {
            if (!empty($user_card['data']['card_info']['jl_bind_id'])) {
                $data['bind_id'] = $user_card['data']['card_info']['jl_bind_id'];
                $url = \yeepay::bankPay($data);
                $return = ['code' => 1, 'data' => ['url' => $url]];
            } else {
                $return = ['code' => 10182, 'msg' => '没有bind_id'];
            }
        } else {
            $return = $user_card;
        }
        return $return;
    }

    /**
     * 嘉联付款参数的模拟
     * @return array
     *
     * @author      liyongchuan
     */
    public function parameterConstruction($user_id)
    {
        if(env('TEST_PAY_IS_OPEN')){
            $order_id='PAY' . date('Ymd', time()).$user_id;
        }else{
            $order_id='PAY' . date('mdHis', time()).$user_id;
        }
        return [
            'order_id' => $order_id,
            'transtime' => time(),
            'currency' => 'CNY',
            'productcatalog' => 1,
            'product_name' => '畅想购-还款',
            'callbackurl' => \Config::get('services.yeepay.callbackurl'),
            'fcallbackurl' => \Config::get('services.yeepay.fcallbackurl'),
        ];
    }

    /**
     * 嘉联支付同步通知
     * @param Request $request
     * @return array
     */
    public function yeeReturn(Request $request)
    {
        $params = $request->input();
        $result = json_decode(base64_decode($params['result']), true);
        return \PayLogService::payLogAdd($result['data']['order_id']);
    }

    /**
     * 嘉联支付异步通知
     * @param Request $request
     * @return string
     */
    public function yeeNotify(Request $request)
    {
        $json=[];
        $params =$request->getContent();
        foreach (explode('&', $params) as $key=>$chunk) {
            $param = explode("=", $chunk);
            if ($param) {
                $json[urldecode($param[0])]=urldecode($param[1]);
            }
        }
        $result = \yeepay::callback($json['data'], $json['encryptkey']);
        if ($result['status'] == 0) {
            //todo 修改订单状态
            //修改payLog表
            $data['order_sn']=$result['data']['order_id'];
            $data['pay_money']=$result['data']['amount']/100;
            $data['pay_id']=4;
            $data['trade_no']=$result['data']['jl_orderno'];
            $upd_bool=\PayLogService::payLogUpdate($data);
            //修改rc还款接口
            $pay['user_id']=$result['data']['mer_tag'];
            $payment=json_decode(\UserService::immediateRepayment($pay),true);
            if ($payment['code'] == 1 && $upd_bool['code']==1) {
                return 'success';
            } else {
                return 'fail';
            }
        } else {
            return 'fail';
        }
    }

    /**
     * 快付通付款请求接口
     *
     * @param Request $request
     * @return array
     *
     * @author  liyongchuan
     * @time    2017-09-22
     */
    public function kftPay(Request $request)
    {
        $params=$request->input();
        $params['user_id']=get_user_id();
        if(env('TEST_PAY_IS_OPEN')){
            $order_id='PAY' . date('Ymd', time()).$params['user_id'];
        }else{
            $order_id='PAY' . date('mdHis', time()).$params['user_id'];
        }
        $user_info=\UserService::userDetail($params['user_id']);
        $user_card = \UserService::userCartDetail($params);
        if($user_card['code']!=1){
            return ['code'=>10183,'msg'=>'银行卡不存在'];
        }
        $bank_info=\BankInfoService::BankInfoDetail($user_card['data']['card_info']['bank_id']);
        $yw_params = [
            "amount" => $params['amount'] * 100,
            "custBankNo" => $bank_info['data']['bank_info']['bank_line_code'],
            "custBankAccountNo" => $user_card['data']['card_info']['card_number'],
            "custBindPhoneNo" => $user_card['data']['card_info']['card_mobile'],
            //币种
            "currency" => "CNY",
            "custCertificationType" => "0",
            "custID" => $user_info['data']['user_info']['user_idcard'],
            "custName" => $user_info['data']['user_info']['real_name'],
            //产品编号
            'service' => 'gbp_collect_from_bank_account',
            //商家银行卡号
            "merchantBankAccountNo" => \Config::get('services.kft.merchantBankAcc_ountNo'),
            //银行卡类型
            "custAccountCreditOrDebit" => "1",//借记卡
            //产品编号
            'productNo'=>'2ACB0AJJ',
            "orderNo" => $order_id,
            "remark" => '畅想购-还款',
            "tradeName" => "畅想购-还款",
            "tradeTime" => date('YmdHis'),
        ];
        $result=\kftpay::kftPayRequest($yw_params);
        $result=json_decode($result,true);
        if($result['status']==3){
            $return=['code'=>1,
                'data'=>['orderNo'=>$order_id,'amount'=>$params['amount'],'card_mobile'=>$user_card['data']['card_info']['card_mobile']],
                'msg'=>'成功'];
        }else{
            $return=['code'=>10184,'msg'=>$result['failureDetails']];
        }
        return $return;
    }

    /**
     * 快付通确认接口
     * @param Request $request
     * @return mixed
     */
    public function kftConfirmPay(Request $request)
    {
        $params=$request->input();
        $params['user_id']=get_user_id();
        $yw_params=[
            "confirmFlag" => $params['confirmFlag'],
            "custBindPhoneNo" => $params['card_mobile'],
            "orderNo" => $params['orderNo'],
            "productNo" => "2ACB0AJJ",
            "service" => "gbp_confirm_from_sms_code",
            "smsCode" => $params['smsCode'],
        ];
        $result=\kftpay::kftPayRequest($yw_params);
        $result=json_decode($result,true);
        if($result['status']==1){
            $data['order_sn']=$result['orderNo'];
            $data['pay_money']=$params['amount'];
            $data['pay_id']=4;
            $data['trade_no']=$result['bankReturnTime'];
            $upd_bool=\PayLogService::payLogUpdate($data);
            //修改rc还款接口
            $pay['user_id']=$params['user_id'];
            $payment=json_decode(\UserService::immediateRepayment($pay),true);
            $return = ['code'=>1,'msg'=>'付款成功'];
        }else{
            $return = ['code'=>10185,'msg'=>'支付失败'];
        }
        return $return;
    }
}