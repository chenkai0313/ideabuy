<?php
/**
 * Created by PhpStorm.
 * User: pc08
 * Date: 2017/9/27
 * Time: 11:22
 */

namespace Modules\System\Services;

use Modules\System\Models\Qruuid;
use Modules\User\Models\User;
use JWTAuth;


class QruuidService {
    /**
     * 生成uuid
     * 选填parmas
     * url
     */
    public function QruuidAddAndReturn($params) {
        $params['qruuid'] = substr(md5(uniqid(mt_rand(), true)), 0, 15);//生成uuid
        $add = Qruuid::qruuidAdd($params);
        if ($add) {
            return ['code' => 1, 'data' => ['qruuid' => $params['qruuid'] ]];
        }
        else
            return ['code' => 10190, 'msg' => '生成uuid失败'];
    }

    /**
     * 验证uuid是否被绑定
     * qruuid
     */
    public function QruuidFirst($params) {
        if (!isset($params['qruuid'])) {
            return ['code'=>10195,'msg'=>'参数错误'];
        }
        $res = Qruuid::qruuidFirst($params['qruuid']);
        if (!is_null($res)) {
            if (($res['created_at'] + \Config::get('system.time_sms')) < time()) { //uuid过期
                Qruuid::qruuidEdit($params['qruuid']);//废弃
                return ['code' => 10192, 'msg' => '二维码已过期'];
            }
            $status = $res['status'];
            if ($status == 2) {
                $user_info = User::userInfoDetailById($res['user_id']);
                #判断用户信息哪里没有完善 返回标识符 1.银行卡 2.常用联系人 3.审核状态 4.身份证(文字、图片) 5.交易密码
                $result['data'] = \UserService::getLoginFlag($user_info['user_id']);
                #返回开放的区域
                $result['data']['city'] = \Config::get('system.open_city');
                $result['data']['user_mobile'] = $user_info['user_mobile'];//手机
                if (!empty($result['data']['user_portrait']))
                    $result['data']['user_portrait'] = \Config::get('services.oss.host') . '/' . $user_info['user_portrait'] . \Config::get('user.avatar_spec');//头像
                else
                    $result['data']['user_portrait'] = "http://ideabuy.oss-cn-hangzhou.aliyuncs.com/static/avatar/def_icon_head.png" . \Config::get('user.avatar_spec');

                $result['data']['token'] = $res['token'];
                $result['data']['alias'] = $user_info['user_id'];#登陆的时候添加alias推送到极光 author：吕成
                $result['code'] = 1;
                $result['msg'] = '登录成功';

                $result['data']['url'] = empty($res['url']) ? "" : \Config::get('user.pc_ideabuy_url') . $res['url'];

                return $result;
            }
            if ($status == 0) {
                return ['code'=>10197,'msg'=>'二维码未绑定'];
            }
        }
        else
            return ['code'=>10191,'msg'=>'qruuid不存在'];
    }


    /**
     * app绑定uuid
     * @params user_id jwt
     * @params qruuid
     */
    public function bindQruuid($params) {
        if (!isset($params['qruuid'])) {
            return ['code'=>10196,'msg'=>'参数错误'];
        }

        $res = Qruuid::qruuidDetail($params['qruuid']);

        if (!is_null($res)) { //qruuid存在
            if (($res['created_at'] + \Config::get('system.time_uuid')) < time()) { //uuid过期
                Qruuid::qruuidEdit($params['qruuid']);//废弃
                return ['code' => 10192, 'msg' => '二维码已过期'];
            } else {  //uuid未过期
                $params['status']=2;//设置状态为2 被绑定
                $params['token'] = \JWTAuth::getToken();
                unset($params['s']);
                $res = Qruuid::qruuidBind($params);
                return ['code' => 1, 'msg' => '绑定成功'];
            }
        }
        else
            return ['code'=>10194,'msg'=>'uuid不存在'];
    }
}