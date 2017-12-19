<?php
/**
 * Created by PhpStorm.
 * User: pc08
 * Date: 2017/9/25
 * Time: 13:57
 */
namespace Modules\User\Services;


use Modules\Order\Models\OrderInfo;
use Modules\System\Models\Sms;
use Modules\User\Models\User;
use Modules\User\Models\UserAddress;
use Modules\User\Models\UserInfo;
use Modules\User\Models\UserStatus;
use Modules\User\Models\UserThird;
use JWTAuth;
use App\Events\NotifyPosh;
use Modules\User\Models\UserWallet;

class PcUserService
{
    /**
     * 注册用户(pc)    首先验证验证码是否正确
     * @param verify_code   图形验证码
     * @param user_mobile
     * @param user_password
     * @param confirm_password
     * @param registration_id   移动端用户设备唯一标识、推送参数
     * @param code                 短信验证码
     * @return array
     */
    public function userAdd($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.user.user.user-add'),
            \Config::get('validator.user.user.user-key'),
            \Config::get('validator.user.user.user-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }

        $params['mobile'] = $params['user_mobile'];
        $params['type'] = 1;
        $sms_info = \SMSService::validateSMS($params);
        if ($sms_info['code'] != 1) {  //短信验证码验证不通过
            return $sms_info;
        } else {
            try {
                $params['user_password_login'] = $params['user_password'];
                $params['user_password'] = bcrypt($params['user_password']);//加密
                $user = User::userAdd($params);

                if ($user) {
                    UserStatus::userStatusAdd(['user_id' => $user['user_id']]); // 添加状态表
                    $params_login = ['user_mobile' => $params['user_mobile'], 'user_password' => $params['user_password_login'],'client_version'=>$params['client_version'],'client_device'=>$params['client_device']];
                    $user_third = ['user_id' => $user['user_id']];
                    if (isset($params['registration_id'])) {
                        $user_third['jpush_token'] = $params['registration_id'];
                        $params_login['registration_id'] = $params['registration_id'];
                    }
                    UserThird::userThirdAdd($user_third); // 第三方信息添加(极光推送\)
                    $login = $this->userLogin($params_login);
                    $result['data'] = $login['data'];
                    $result['code'] = 1;
                    $result['msg'] = '注册成功';
                    //注册成功  发送短信
                    $params_sms=[
                        'services'=>'sms',
                        'keyword'=>[
                            'name'=>$params['user_mobile'],
                        ],
                        'tag'=>'register_success',
                        'user_id'=>[$params['user_mobile']],
                        'sms_send'=>1,
                    ];
                    if (\Config::get('user.function_open')) {
                        \Event::fire(new NotifyPosh($params_sms));
                    }

                    UserInfo::userInfoAdd(['user_id' => $user['user_id']]); // 添加空的信息
                } else {
                    $result['code'] = 10110;
                    $result['msg'] = '注册失败';
                }
            } catch (\Exception $e) {
                return ['code' => 500, 'msg' => '注册失败'];
            }
        }
        return $result;
    }

    /**
     * 用户登录
     * @param user_mobile
     * @param user_password
     * @return array
     */
    public function userLogin($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.user.user.user-login'),
            \Config::get('validator.user.user.user-key'),
            \Config::get('validator.user.user.user-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $user_info = User::userInfoDetail($params['user_mobile']);
        if (!is_null($user_info)) {
            if (password_verify($params['user_password'], $user_info['user_password'])) {
                if (!$user_info['is_black']) {
                    #判断用户信息哪里没有完善 返回标识符 1.银行卡 2.常用联系人 3.审核状态 4.身份证(文字、图片) 5.交易密码
                    $result['data'] = \UserService::getLoginFlag($user_info['user_id']);
                    #返回开放的区域
                    $result['data']['city'] = \Config::get('system.open_city');
                    $result['data']['user_mobile'] = $user_info['user_mobile'];//手机
                    if (!empty($result['data']['user_portrait']))
                        $result['data']['user_portrait'] = \Config::get('services.oss.host') . '/' . $user_info['user_portrait'] . \Config::get('user.avatar_spec');//头像
                    else
                        $result['data']['user_portrait'] = "http://ideabuy.oss-cn-hangzhou.aliyuncs.com/static/avatar/def_icon_head.png" . \Config::get('user.avatar_spec');
                    #生成jwt
                    $customClaim = ['from' => 'user', 'user_id' => $user_info['user_id'], 'user_mobile' => $user_info['user_mobile'],'platform'=>'pc'];
                    $token = JWTAuth::fromUser($user_info, $customClaim);
                    \Cache::store(\Config::get('cache.cache_type'))->put($user_info['user_id'], $token, \Config::get('jwt.refresh_ttl'),'pc');
                    $result['data']['token'] = $token;
                    $result['data']['alias'] = $user_info['user_id'];#登陆的时候添加alias推送到极光 author：吕成
                    $result['code'] = 1;
                    $result['msg'] = '登录成功';
                    #TODO 更新用户 设备信息、版本号 暂时关闭这个功能
                    $params_device = ['user_id'=>$user_info['user_id'],'client_version'=>$params['client_version'],'client_device'=>$params['client_device']];
                    User::userEdit($params_device);

                    //推送配置更新
                    if (isset($params['registration_id'])) {
                        $jpushs = [
                            'user_id' => $user_info['user_id'],
                            'jpush_token' => $params['registration_id'],
                        ];
                        UserThird::userThirdEdit($jpushs);
                        $send_data = array(
                            'user_id' => $user_info['user_id'],
                            'registration_id' => $params['registration_id']
                        );
                        \jpush::userAliasSend($send_data);
                    }
                } else {
                    $result['code'] = 11096;
                    $result['msg'] = '登录失败，您的账号已被加入黑名单，具体请联系相关人员';
                }
            } else {
                $result['code'] = 10111;
                $result['msg'] = '登录失败，账号或密码错误';
            }
        } else {
            $result['code'] = 10127;
            $result['msg'] = '账号不存在';
        }

        return $result;
    }

    /**
     * 找回密码
     * @param user_id
     * @param  user_mobile
     * @param  type
     * @param  user_password
     * @param  confirm_password
     * @param  code
     * @return array
     */
    public function userEdit($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.user.user.pc-user-forgot'),
            \Config::get('validator.user.user.user-key'),
            \Config::get('validator.user.user.user-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }

        #用户不存在时
        $user_isexist = User::userInfoDetail($params['user_mobile']);
        if (!is_null($user_isexist)) {
            $condition = ['mobile' => $params['user_mobile'], 'type' => 2, 'code' => $params['code']];//验证短信的参数
            $sms_info = \SMSService::validateSMS($condition);
            if ($sms_info['code'] != 1) {  //短信验证码验证不通过
                return $sms_info;
            } else {
                $params['user_password'] = bcrypt($params['user_password']);
                $params['user_id'] = $user_isexist['user_id'];
                $user = User::userEdit($params);
                if ($user) {
                    $result = ['code' => 1, 'msg' => '修改登录密码成功'];
                } else {
                    $result = ['code' => 10112, 'msg' => '修改登录密码失败'];
                }
            }
        } else {
            $result = ['code' => 10113, 'msg' => '手机号不存在'];
        }
        return $result;
    }

    /**
     * 用户登录后修改密码
     * @param user_id jwt
     * @param  user_mobile  jwt
     * @param  user_password
     * @param  confirm_password
     *
     * @return array
     */
    public function userChangePassword($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.user.user.pc-user-changepassword'),
            \Config::get('validator.user.user.user-key'),
            \Config::get('validator.user.user.user-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }

        #用户不存在时
        $user_isexist = User::userInfoDetail($params['user_mobile']);
        if (!is_null($user_isexist)) {
            if (!password_verify($params['old_user_password'],$user_isexist['user_password'])) {
                $result = ['code' => 500, 'msg' => '当前登录密码不正确'];
            } else {
                $params['user_password'] = bcrypt($params['user_password']);
                $params['user_id'] = $user_isexist['user_id'];
                $user = User::userEdit($params);
                if ($user) {
                    $result = ['code' => 1, 'msg' => '修改登录密码成功'];
                } else {
                    $result = ['code' => 10112, 'msg' => '修改登录密码失败'];
                }
            }
        } else {
            $result = ['code' => 10113, 'msg' => '手机号不存在'];
        }
        return $result;
    }

    /**
     * PC端验证 短信验证码
     * 只是一个方法 不是接口API
     */
    public function validateSMSPC($params) {
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
            Sms::smsStatusEdit($params);//废弃短信
        } else {
            $result['code'] = 1;
            $result['msg'] = '验证码正确';//不废弃短信下一个页面还要验证(下一个页面废弃短信)
        }
        return $result;
    }

    /**
     * 找回密码时检查短信验证码
     * @param user_mobile
     * @param code
     *
     * @return mixed
     */
    public function checkAllCode($params) {
        $params['mobile'] = $params['user_mobile'];
        $params['type'] = 2;
            $code_res = $this->validateSMSPC($params);
            if ($code_res['code']==1) {
                return ['code'=>1,'msg'=>'验证通过'];
            } else {
                return ['code'=>500,'msg'=>$code_res['msg']];//短信验证码验证失败
            }
    }

    /**
     * TODO 白条首页
     * $params['user_id'] jwt
     * @author 曹晗
     */
    public function whiteIndex($params)
    {
        #判断用户是否激活白条
        $user_status = UserStatus::userStatusFirst($params['user_id']);

        if ($user_status['is_activate'] == 1) {
            #查询信息 信用、白条总额、白条可用、X月待还款、待还款总额、剩余X天
            $user = User::userFind($params);
            $res = ['credit_point' => $user['credit_point'], 'white_amount' => number_format($user['white_amount'], 2, '.', '')];
            $user_white = UserWallet::userWalletInfo($params);  //从wallet里查询用户白条可用
            #初始化数据
            $res['white_available'] = number_format($user_white['white_money'], 2, '.', '');//白条可用
            $res['should_pay_all_amount'] = number_format(0, 2, '.', '');//所有待还款
            $res['should_pay_amount'] = number_format(0, 2, '.', '');//当月待还款

            //RC获取
            $params_send = ['user_id' => $params['user_id']];
            $account_index = vpost(\Config::get('interactive.riskcontrol.account_index'), $params_send);
            $account_index = json_decode($account_index, true);
            if (isset($account_index['code'])) {
                if ($account_index['code'] == 1) {
                    $res['should_pay_amount'] = $account_index['data']['should_pay_fee'];//当月待还款
                    $res['should_pay_all_amount'] = $account_index['data']['total_surplus_pay_fee'];//所有待还款
                }
                //未出账 unbill order_list 不是模拟数据  这个月的  需要加到所有待还款里
                $condition = get_month_time();//这个月的时间戳  月初 下月初
                $condition['user_id'] = $params['user_id'];
                $order_info_bill = OrderInfo::userOrderInfo($condition);//查询订单
                if (!empty($order_info_bill)) {
                    foreach ($order_info_bill as $key => $value) {
                        $res['should_pay_all_amount'] += $order_info_bill[$key]['order_amount'];
                    }
                }
                $res['should_pay_all_amount'] = number_format((string)$res['should_pay_all_amount'], '2', '.', '');

                $params_send3 = ['user_id' => $params['user_id']];

                $account_index2 = vpost(\Config::get('interactive.riskcontrol.pc_user_account_index'), $params_send3);
                $account_index2 = json_decode($account_index2, true);
                $res['account_info'] = $account_index2['data'];

                return ['code' => 1, 'data' => $res];
            } else {
                return ['code' => 500, 'msg' => '请求出错'];
            }
        } else {
            $data = \UserService::getLoginFlag($params['user_id']);
            return ['code' => 10129, 'msg' => '未激活白条', 'data' => ['user_status'=>$data]];
        }
    }



}