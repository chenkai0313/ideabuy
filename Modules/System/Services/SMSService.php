<?php
/**
 * Created by PhpStorm.
 * User: 曹晗
 * Date: 2017/7/31
 * Time: 17:19
 */

namespace Modules\System\Services;

use Modules\System\Models\Sms;
use Modules\User\Models\User;
use Overtrue\EasySms\EasySms;
use Modules\System\Models\MessageSms;
use App\Events\NotifyPosh;

class SMSService
{

    /**
     * 发送短信(api)
     * @param $params ['mobile'] 手机号 jwt
     * @param $params ['type'] 类型  （1注册，2找回密码,3重置交易密码,4绑定银行卡,5绑定新手机号,6登陆后重置登录密码）
     * @return array
     */
    public function addSMS($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.sms.sms-send'),
            \Config::get('validator.system.sms.sms-key'),
            \Config::get('validator.system.sms.sms-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $arr = [1, 5];//判断手机号是否存在
        if (in_array($params['type'], $arr) && (!is_null(User::userInfoDetail($params['mobile'])))) {
            return ["code" => 10130, 'msg' => "手机号已被注册"];
        }
        if ($params['type'] == 2 && (is_null(User::userInfoDetail($params['mobile'])))) {
            return ["code" => 10130, 'msg' => "手机号不存在"];
        }
            $params['code'] = $this->rand_code();
        $res_sms = ['code'=>1];
        #发短信接口调用
        try {
            $params_sms=[
                'services'=>'sms',
                'keyword'=>[
                    'number'=>$params['code'],
                ],
                'tag'=>'security_code',
                'user_id'=>[$params['mobile']],
                'sms_send'=>1,
                ];
            if (\Config::get('user.function_open')) {
                \Event::fire(new NotifyPosh($params_sms));
            }
            Sms::smsStatusEdit($params);//废弃短信
        } catch (\Exception $e) {
            Sms::smsStatusEdit($params);//废弃短信
            $res = Sms::smsAdd($params);
            return ['code' => 500, 'msg' => '短信间隔时间太短或超出发送上限，发送失败'];
        }
//        try {
//            $arr2 = [1,2];//不发推送
//            if (!in_array($params['type'],$arr2) ) {   //发推送   注册不发推送
//                $user = User::userInfoDetail($params['mobile']);
//                $temp = \message::templetMessage($content, $tag);
//                $description = $temp['data']['msg'];
//                $push = [
//                    'operate_type' => '3',
//                    'user_id' => $user['user_id'],
//                    'audience' => 'regis_id',
//                    'title' => '您的验证码',
//                    'description' => $description,
//                    'result' => json_encode(['code' => 1, 'msg' => $description]),
//                    'message_type' => 'sms_send',
//                    'type' => 2, // 1公告 2通知
//                ];
//                \MessageService::messageEntry($push);
//            }
//        } catch (\Exception $e1) {
//            return ['code' => 500, 'msg' => '发送失败'];
//        }
        if ($res_sms['code'] == 1) { //发短信成功
            $res = Sms::smsAdd($params);
            if (!is_null($res)) {
                $result['code'] = 1;
                $result['msg'] = '短信发送成功';
            } else {
                $result['code'] = 10131;
                $result['msg'] = '短信发送失败'; //添加数据失败
            }
        } else {
            $result['code'] = 10132;
            $result['msg'] = '短信发送失败'; // 发短信接口调用失败
        }

        return $result;
    }

    /**
     * 短信验证接口
     * @param $params ['mobile'] 手机号 必填
     * @param $params ['type'] 类型  1 注册 2找回密码
     * @return array
     */
    public function validateSMS($params)
    {
        $searchSMS = Sms::searchSMS($params);
        if (is_null($searchSMS)) { //验证码不存在
            $result['code'] = 10133;
            $result['msg'] = '短信验证码不存在';
        } else if ($searchSMS['code'] != $params['code']) { //验证码错误
            $result['code'] = 10133;
            $result['msg'] = '短信验证码错误';
        } else if (($searchSMS['created_at'] + \Config::get('system.time_sms')) < time()) {
            $result['code'] = 10133;
            $result['msg'] = '短信验证码已过期';
            Sms::smsStatusEdit($params);
        } else {
            $result['code'] = 1;
            $result['msg'] = '验证码正确';
            #废弃该条短信验证码
            Sms::smsStatusEdit($params);
        }
        return $result;
    }

    /**
     * 生成随机字符串
     * @param int $length 要生成的随机字符串长度
     * @param int $type 随机码类型：0，数字+大小写字母；1，数字；2，小写字母；3，大写字母；4，特殊字符；5，数字+大小写字母+特殊字符
     * @return string   生成的随机字符串
     */
    private function rand_code($length = 4, $type = 1)
    {
        $arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
        if ($type == 0) {
            array_pop($arr);
            $string = implode("", $arr);
        } elseif ($type == "-1") {
            $string = implode("", $arr);
        } else {
            $string = $arr[$type];
        }
        $count = strlen($string) - 1;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $string[rand(0, $count)];
        }
        return $code;
    }

    // 短信列表
    public function smsList($params){
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $url = \Config::get('interactive.message.message_sms_backend');
        $result = vpost($url,$params);
        return json_decode($result,true);
    }

}