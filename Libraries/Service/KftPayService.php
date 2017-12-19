<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2017/9/22
 * Time: 09:04
 */
namespace Libraries\Service;

use function EasyWeChat\Payment\get_client_ip;
use Libraries\Help\KFT\Sign;

class KftPayService
{
    public function kftPayRequest($yw_params)
    {
        $bs_params = [
            //接口版本号(测试)
            'version' => \Config::get('services.kft.version'),//正式为1.0.0-PRD
            //参数字符集
            'charset' => 'utf-8',
            //语言
            'language' => 'zh_CN',
            //调用端IP
            'callerIp' => get_client_ip(),
            //商户号
            "merchantId" => \Config::get('services.kft.merchantid'),
        ];
        $params = array_merge($bs_params, $yw_params);
        $basepath = str_replace('\\', '/', base_path()) . '/';
        $pfx_path = $basepath . 'Libraries/Help/KFT/pfx.pfx';
        //测试url
        $request_trade_url = \Config::get('services.kft.request_url');
        $sign = new Sign($pfx_path, '123456');
        //普通交易请求
        $sign_data = $sign->sign_data($params);
        $response_data = $sign->request_kft($params, $sign_data, $request_trade_url, false);
        return $response_data;
    }
}