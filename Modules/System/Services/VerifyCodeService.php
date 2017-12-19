<?php
/**
 * Created by PhpStorm.
 * User: 曹晗
 * Date: 2017/7/31
 * Time: 17:19
 */

namespace Modules\System\Services;

use Illuminate\Support\Facades\Redis;

class VerifyCodeService
{
    /**
     * 通过手机号生成图形验证码
     * @params user_mobile 手机号
     */
    public function addVerifyCode($params) {
        if (isset($params['user_mobile'])) {
            $random = getRandomkeys(4);
            Redis::set($params['user_mobile'], $random);
            $arr = ['user_mobile' => $params['user_mobile'], 'random' => $random];
            return ['code' => 1, 'data' => $arr];
        } else {
            return ['code' => 500, 'msg' => '参数错误'];
        }
    }

    /**
     * 检验图形验证码
     * @param user_mobile 手机号
     * @param  verify_code 图形验证码
     * @return array
     */
    public function CheckVerifyCode($params) {
        if (isset($params['user_mobile']) && isset($params['verify_code'])) {
            if (Redis::exists($params['user_mobile'])) {
                $verify_code = Redis::get($params['user_mobile']);
                if ($params['verify_code'] == $verify_code) {
                    $data = ['verify_code'=>$params['verify_code'],'user_mobile'=>$params['user_mobile']];
                    return ['code' => 1, 'msg' => '验证码正确','data'=>$data];
                } else {
                    return ['code' => 500, 'msg' => '验证码错误'];
                }
            } else {
                return ['code' => 500, 'msg' => '验证码不存在'];
            }
        } else {
            return ['code'=> 500, 'msg'=>'参数错误'];
        }
    }
}