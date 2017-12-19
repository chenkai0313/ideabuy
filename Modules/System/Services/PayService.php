<?php
namespace Modules\System\Services;

use \Config;
use Modules\Order\Models\OrderInfo;
use Modules\Order\Models\PayLog;
use Omnipay\Omnipay;

class PayService
{
    /**
     * 支付宝网页支付网关
     */
    protected function aliPayWebGateway($params)
    {
        $gateway = Omnipay::create('Alipay_AopPage');
        $gateway->setSignType(config('alipay.sign_type'));
        $gateway->setAppId(config('alipay.app_id'));
        $gateway->setPrivateKey(file_get_contents(config('alipay.private_key_path')));
        $gateway->setAlipayPublicKey(file_get_contents(config('alipay.public_key_path')));
        $gateway->setNotifyUrl(url(config('alipay.notify_url')));
        if (!empty($params['return_url'])) {
            $gateway->setReturnUrl($params['return_url']);
        }
        return $gateway;
    }
    /**
     * 支付宝网页支付
     * @param string $order_sn 订单编号
     * @return array
     */
    public function aliPayWeb($params)
    {
        #回调地址
        $params['return_url'] =  Config::get('api.domain').'/pc/order-pay-finish';
        $validator = \Validator::make(
            $params,
            Config::get('validator.pay.pay.alipay-web'),
            Config::get('validator.pay.pay.pay-key'),
            Config::get('validator.pay.pay.pay-val')
        );
        if ($validator->fails()){
            return ['code' => 10170, 'msg' => $validator->messages()->first()];
        }
        $total = OrderInfo::orderAmount($params);
        if ($total) {
//            #验证订单状态是否为未支付
//            if ($order['order_status'] == 0) {
                #创建支付单。
                $gateway = $this->aliPayWebGateway($params);
                $request = $gateway->purchase();
                $request->setBizContent([
                    'out_trade_no' => $params['order_sn'],
                    //'total_amount' => number_format($total,2,'.',''),
                    'total_amount' => '0.01',
                    'subject'      => $params['order_sn'],
                    'product_code' => 'FAST_INSTANT_TRADE_PAY',
                ]);
                $response = $request->send();
                #返回支付页面URL。
                return ['code' => 1, 'data' => ['url' => $response->getRedirectUrl()]];
//            }else{
//                return ['code' => 10172, 'msg' => '订单状态不符'];
//            }
        }else{
            return ['code' => 10171, 'msg' => '订单不存在'];
        }
    }

    /**
     * 网页支付异步通知
     * @param array $params  支付宝回调参数
     * @return string
     */
    public function webNotify($params)
    {
        #验证请求
        $gateway = $this->aliPayWebGateway($params);
        try{
            $response = $gateway->completePurchase()->setParams($params)->send();
            if (!$response->isPaid()) {
                die('fail');
            }
            #支付成功
            $info = ['order_sn' => $params['out_trade_no']];
            $order = OrderInfo::orderDetailSn($info['order_sn']);
            #验证订单价格是否正确
            if ($order){
                if ($order['order_amount'] == $params['total_amount'] &&
                    config('alipay.seller_id') == $params['seller_id'] &&
                    config('alipay.app_id') == $params['app_id']
                ) {
                    \DB::beginTransaction();
                    #保存订单状态
                    $info['pay_id'] = 2;    //支付方式  1白条，2支付宝，3微信，4银行卡，5余额
                    $info['order_status'] = 1;  //订单状态（0未付款，1已付款待发货，2已付款待收货，3确认收货，4已取消，5已完成）
                    $rel1 = OrderInfo::orderPayStatusChange($info);
                    $data = [
                        'order_sn' => $params['out_trade_no'],
                        'pay_money' => $params['total_amount'],
                        'pay_id' => 2,      //支付方式  1白条，2支付宝，3微信，4银行卡，5余额
                        'from_type' => 1,   //来源（1订单）
                        'trade_no' => $params['trade_no'],
                    ];
                    #保存支付信息
                    $rel2 = PayLog::payLogAdd($data);
                    if ($rel1 && $rel2) {
                        \DB::commit();
                        die('success');
                    }else{
                        \DB::rollBack();
                        die('fail');
                    }
                }else{
                    die('fail');
                }
            }else{
                die('fail');
            }
        }catch (\Exception $exception){
            die('fail');
        }
    }
}