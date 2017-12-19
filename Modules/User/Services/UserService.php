<?php
/**
 * Created by PhpStorm.
 * User: 曹晗
 * Date: 2017/7/31
 * 用户登录等操作 逻辑层
 */

namespace Modules\User\Services;

use App\Events\NotifyPosh;
use JWTAuth;
use Libraries\Help\Util\Validator;
use Mockery\Exception;
use Modules\Order\Models\OrderInfo;
use Modules\Order\Models\OrderGoods;
use Modules\System\Models\Region;
use Modules\User\Models\Address;
use Modules\User\Models\User;
use Modules\User\Models\UserAddress;
use Modules\User\Models\UserApply;
use Modules\User\Models\UserCard;
use Modules\User\Models\UserInfo;
use Modules\User\Models\UserStatus;
use Modules\User\Models\UserThird;
use Modules\User\Models\UserWallet;
use Modules\User\Models\UserWalletDetail;

class UserService
{
    /**
     * 注册用户(api)
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
                return ['code'=>'500','msg'=>'注册失败'];
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
                    $result['data'] = $this->getLoginFlag($user_info['user_id']);
                    #返回开放的区域
                    $result['data']['city'] = \Config::get('system.open_city');
                    $result['data']['user_mobile'] = $user_info['user_mobile'];//手机
                    if (!empty($result['data']['user_portrait']))
                        $result['data']['user_portrait'] = \Config::get('services.oss.host') . '/' . $user_info['user_portrait'] . \Config::get('user.avatar_spec');//头像
                    else
                        $result['data']['user_portrait'] = "http://ideabuy.oss-cn-hangzhou.aliyuncs.com/static/avatar/def_icon_head.png" . \Config::get('user.avatar_spec');
                    #生成jwt
                    $customClaim = ['from' => 'user', 'user_id' => $user_info['user_id'], 'user_mobile' => $user_info['user_mobile']];
                    $token = JWTAuth::fromUser($user_info, $customClaim);
                    rewrite_cache()->put($user_info['user_id'], $token, \Config::get('jwt.refresh_ttl'));
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


    #查询用户填写信息完整度
    public function getLoginFlag($user_id)
    {
        $params['user_id'] = $user_id;

        $info1 = User::userFind($params);
        $info2 = UserStatus::userStatusFirst($params['user_id']);
        $result['status'] = $info2['status'];//审核状态
        $result['is_activate'] = $info2['is_activate'];//是否激活白条
        $result['is_linkman'] = $info2['is_linkman'];//常用联系人
        $result['is_idcard'] = $info2['is_idcard'];//身份证 文字
        $result['is_idcard_img'] = $info2['is_idcard_img'];//图片
        if ($info1['card_id'] == 0) {//银行卡
            $result['is_bankcard'] = 0;
        } else {
            $result['is_bankcard'] = 1;
        }
        if (empty($info1['pay_password'])) {//交易密码
            $result['is_pay_password'] = 0;
        } else {
            $result['is_pay_password'] = 1;
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
            \Config::get('validator.user.user.user-forgot'),
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
     * 用户设置交易密码
     * @param $params ['user_id']        int     用户ID
     * @return array
     */
    public function userSetPayPwd($params)
    {
        if (!isset($params['pay_password'])) {
            return ['code' => 90002, 'msg' => '参数错误'];
        }
        $params['pay_password'] = bcrypt($params['pay_password']);
        $user = User::userEdit($params);
        if ($user) {
            $return = ['code' => 1, 'msg' => '设置交易密码成功'];
        } else {
            $return = ['code' => 10114, 'msg' => '设置交易密码失败'];
        }
        return $return;
    }

    /**
     * 修改交易密码
     * @param $params ['mobile']     int     用户手机号
     * @param $params ['type']     int       code类型
     * @param $params ['pay_passwprd']     int       支付密码
     * @param $params ['user_id']     int       用户ID
     * @return array
     */
    public function userEditPayPwd($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.user.user.user-editpaypwd'),
            \Config::get('validator.user.user.user-key'),
            \Config::get('validator.user.user.user-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $sms = \SMSService::validateSMS($params);
        if ($sms['code'] != 1) {
            return $sms;
        }
        $params['pay_password'] = bcrypt($params['pay_password']);
        $user = User::userEdit($params);
        if ($user) {
            $return = ['code' => 1, 'msg' => '修改交易密码成功'];
        } else {
            $return = ['code' => 10115, 'msg' => '修改交易密码失败'];
        }
        return $return;
    }

    /**
     * 用户身份证添加
     * @param $params ['user_idcard']        string      用户身份证号
     * @param $params ['real_name']        string      用户真实姓名
     * @param $params ['user_id']        string      用户ID
     * @return bool
     */
    public function userEditIdCard($params)
    {
        if (!isset($params['real_name']) || !isset($params['user_idcard'])) {
            return ['code' => 90002, 'msg' => '参数错误'];
        }
        $card = Validator::vdCard($params['user_idcard']);
        if ($card['code'] != 1) {
            return $card;
        }
        $params['apply_type'] = 1;
        $params['status'] = 1;
        $count = UserApply::userApplyGet($params);
        $user_id = UserApply::userIdCard($params);
        if ($count == 0 && is_null($user_id)) {
            $userApply = UserApply::userApplyAdd($params);
        } else {
            if ($user_id != $params['user_id']) {
                $userApply = UserApply::userApplyEditCard($params);
            } else {
                return ['code' => 10134, 'msg' => '该身份证已有人在使用'];
            }
        }
        if ($userApply) {
            $return = ['code' => 1, 'msg' => '身份证添加成功'];
            $data['user_id'] = $params['user_id'];
            $data['is_idcard'] = 1;
            $data['status'] = 0;
            UserStatus::userStatusUpdate($data);
        } else {
            $return = ['code' => 10116, 'msg' => '身份证添加失败'];
        }
        return $return;
    }

    /**
     * 添加常用联系人
     * @param $params ['user_id']
     * @param $params ['link_man']
     * @param $params ['link_mobile']
     * @param $params ['link_relation']
     * @return array
     */
    public function linkManAdd($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.user.user_info.user_info-linkman'),
            \Config::get('validator.user.user_info.user_info-key'),
            \Config::get('validator.user.user_info.user_info-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        if ($params['user_mobile'] == $params['link_mobile']) {
            return ['code' => 10117, 'msg' => '联系人手机号不能和自己的手机相同'];
        }
        $params['info_id'] = UserInfo::userInfoId($params);
        if (empty($params['info_id'])) {
            $user_info = UserInfo::userInfoAdd($params);
        } else {
            $user_info = UserInfo::userInfoEdit($params);
        }
        if ($user_info) {
            $return = ['code' => 1, 'msg' => '添加常用联系人成功'];
            $condition = ['user_id' => $params['user_id'], 'is_linkman' => 1];
            UserStatus::userStatusUpdate($condition);//修改user表的is_link状态
        } else {
            $return = ['code' => 10118, 'msg' => '添加常用联系人失败'];
        }
        return $return;
    }

    /**
     * 完善用户信息
     * @param $params ['user_id']
     * @param $params ['user_education']     学历
     * @param $params ['user_profession']    专业
     * @param $params ['user_income']        收入
     * @param $params ['user_company']       公司
     * @param $params ['user_qq']            QQ
     * @param $params ['user_email']         Email
     * @param $params ['user_portrait']        头像url
     * /////////////////////////////////////////////
     * @param $params ['province']           省
     * @param $params ['city']               市
     * @param $params ['district']           区
     * @param $params ['address']            详细地址
     * @return array
     */
    public function userInfoAdd($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.user.user_info.user_info-add'),
            \Config::get('validator.user.user_info.user_info-key'),
            \Config::get('validator.user.user_info.user_info-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        if (!empty($params['user_email'])) {
            $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
            if (!preg_match($pattern, $params['user_email'])) {
                return ['code' => 90002, 'msg' => "您输入的电子邮件地址不合法"];
            }
        }

        foreach ($params as $key => $value) {
            $params[$key] = is_null($params[$key]) ? "" : $value;
        }

        if (!empty($params['province'])) {
            $area = [
                $params['province'],
                $params['city'],
                $params['district'],
            ];
            \RegionService::regionGet($area);
        }


        #信息添加
        $params['info_id'] = UserInfo::userInfoId($params);

        if (empty($params['info_id'])) {
            $user_info = UserInfo::userInfoAdd($params);
        } else {
            $user_info = UserInfo::userInfoEdit($params);
        }
        if ($user_info) {
            $user_status = UserStatus::userStatusFirst($params['user_id']);
            if ($user_status['status'] == 2) {//判断是否通过审核
                if ($user_status['is_activate'] == 1) {//判断是否激活白条 然后返回信用分
                    $user_info2 = UserInfo::userInfoDetail($params['user_id']);
                    $params_send = ['user_education' => $user_info2['user_education'], 'user_profession' => $user_info2['user_profession'],
                        'user_company' => $user_info2['user_company'], 'user_income' => $user_info2['user_income'],
                        'user_qq' => $user_info2['user_qq'], 'user_email' => $user_info2['user_email']];
                    #发送到风控系统
                    $user_credit = vpost(\Config::get('interactive.riskcontrol.credit'), $params_send);
                    $user_credit = json_decode($user_credit, true);
                    #返回信用分 更新数据库
                    if ($user_credit['code'] == 1) {
                        $user = User::userFind($params);
                        User::userEdit(['user_id' => $params['user_id'], 'credit_point' => $user_credit['data']['credit_point'], 'white_amount' => $user_credit['data']['white_amount']]);
                        // 信用积分增加 发送短消息通知

                        //对比white_amount 更新白条收支记录user_wallet_detail  更新白条可用余额user_wallet
                        $compare = $user_credit['data']['white_amount'] - $user['white_amount'];//对比如果=0 不做任何处理
                        if ($compare > 0) {
                            $params_condition = ['user_id' => $params['user_id'], 'change_money' => $compare, 'type' => 1, 'status' => 2];
                            \UserWalletService::UserWalletDetailAdd($params_condition);
                        }
                        if ($compare < 0) {
                            $params_condition = ['user_id' => $params['user_id'], 'change_money' => $compare, 'type' => 1, 'status' => 1];
                            \UserWalletService::UserWalletDetailAdd($params_condition);
                        }
                        /**
                         * if ($compare != 0) {  //信用分变化 发送jpush
                         * $description = '您的信用分有变化，请查看。';
                         * $push = [
                         * 'operate_type' => '3',
                         * 'user_id' => $user['user_id'],
                         * 'audience' => 'regis_id',
                         * 'title' => '信用分有变化',
                         * 'description' => $description,
                         * 'result' => json_encode(['code' => 1, 'msg' => $description]),
                         * 'message_type' => 'credit_score',
                         * 'type' => 2, // 1公告 2通知
                         * ];
                         * \MessageService::messageEntry($push);
                         * }
                         */
                    }
                }
            }
            $params['user_portrait'] = isset($params['user_portrait']) ? $params['user_portrait'] : "";
            $condition_head = ['user_id' => $params['user_id'], 'user_portrait' => $params['user_portrait']];
            $this->userHeadImgUpload($condition_head);//头像添加
            $result = ['code' => 1, 'msg' => '完善信息成功'];
        } else {
            #出错直接return
            return $result = ['code' => 10119, 'msg' => '完善信息出错'];
        }

        return $result;

    }

    /**
     * 用户身份证照片的添加
     * @param $params ['file']     string    照片地址用','隔开的字符串
     * @param $params ['user_id']     int    用户ID
     * @return array
     */
    public function userEditIdImg($params)
    {
        if (!isset($params['file'])) {
            return ['code' => 90002, 'msg' => '参数错误'];
        }
        $params['file_type'] = 1;
        $userapply_find=UserApply::userApplyFind($params);
        if($userapply_find){
            $params['del_id']=$userapply_find['id_img'];
        }else{
            $params['del_id']=null;
        }
        $file = \FileService::fileAdd($params);
        $params['id_img'] = $file['data'];
        $userapply = UserApply::userApplyEditImg($params);
        if ($userapply) {
            $data['user_id'] = $params['user_id'];
            $data['is_idcard_img'] = 1;
            $data['status'] = 1;
            UserStatus::userStatusUpdate($data);
            $return = ['code' => 1, 'msg' => '身份证照片添加成功'];
        } else {
            $return = ['code' => 10120, 'msg' => '身份证照片添加失败'];
        }
        return $return;
    }

    /**
     * 头像上传
     * @param $params ['user_id']
     * @param $params ['user_portrait']
     * @return array
     */
    public function userHeadImgUpload($params)
    {
        $user = User::userEdit($params);
        if ($user) {
            $result = ['code' => 1, 'msg' => '头像修改成功'];
        } else {
            $result = ['code' => 10121, 'msg' => '头像修改失败'];
        }
        return $result;
    }

    /**
     * 用户详情接口
     * @param $params ['user_id']  jwt
     * @return array
     */
    public function userInfoDetail($params)
    {
        $user_detail = User::userDetail($params['user_id']);
        if ($user_detail) {
            if (!empty($user_detail['user_portrait'])) {
                $user_detail['user_portrait'] = \Config::get('services.oss.host') . '/' . $user_detail['user_portrait'];//头像
            }
            $user_detail['user_mobile'] = substr_replace($params['user_mobile'], '****', 3, 4);
            $user_detail['user_idcard'] = substr_replace($user_detail['user_idcard'], '********', 6, 8);
            $user_detail['user_education'] = $user_detail['user_education'] ? [$user_detail['user_education']] : [];//数组格式返回 学历
            $user_detail['user_income'] = $user_detail['user_income'] ? [$user_detail['user_income']] : [];//数组格式返回 收入
            $result['data']['user'] = $user_detail;
            $result += ['code' => 1, 'msg' => '查询成功'];
        } else {
            $result['data']['user'] = ['msg' => '暂无用户信息数据'];
        }
        $user_addresscode = $user_detail;
        if ($user_addresscode) {
            $result['data']['user']['user_address'] = $user_addresscode['address'];
            if (empty($user_addresscode['province'])) {
                $area = [];
            } else {
                $area = [
                    $user_addresscode['province'],
                    $user_addresscode['city'],
                    $user_addresscode['district'],
                ];
            }
            if(count($area)>0){
                $zhong=\RegionService::regionGet($area);
                $address_zhong=$zhong['data'];
            }else{
                $address_zhong=[
                    "province"=> '请选择省/其他',
                 "city"=> '请选择城市',
                 "district"=> '请选择区/县'
                ];
            }
            $result['data']['user']['user_address_zhong'] = $address_zhong;
            $result['data']['user']['user_address_code'] = $area;
            $result += ['code' => 1, 'msg' => '查询成功'];
        } else {
            $result['data']['address'] = ['msg' => '暂无用户地址数据'];
        }
        return $result;
    }

    /**
     * 查询用户的详情(内部调用)
     * @param $user_id
     * @return array
     */
    public function userDetail($user_id)
    {
        $user_detail = User::userDetail($user_id);
        if ($user_detail) {
            $return = ['code' => 1, 'data' => ['user_info' => $user_detail]];
        } else {
            $return = ['code' => 10124, 'msg' => '查询失败'];
        }
        return $return;
    }

    /**
     * 用户绑定新的手机号
     * @param $params ['user_id'] jwt
     * @param $params ['code']
     * @param $params ['user_mobile']
     * @param $params ['type'] 从controller传过来
     * @return array
     */
    public function userChangeMobile($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.user.user.user-changemobile'),
            \Config::get('validator.user.user.user-key'),
            \Config::get('validator.user.user.user-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }

        $condition = ['mobile' => $params['user_mobile'], 'code' => $params['code'], 'type' => $params['type']];
        $sms_info = \SMSService::validateSMS($condition);
        if ($sms_info['code'] != 1) {  //短信验证码验证不通过
            return $sms_info;
        } else {
            $conditionEdit = ['user_id' => $params['user_id'], 'user_mobile' => $params['user_mobile']];
            $user = User::userEdit($conditionEdit);
            if ($user) {
                $result = ['code' => 1, 'msg' => '更换手机号成功'];
            } else {
                $result = ['code' => 10122, 'msg' => '更换手机号失败'];
            }
        }
        return $result;
    }

    /**
     * 用户银行卡添加
     *
     * @param $params ['user_id']    int     用户id
     * @param $params ['card_number']    string     用户银行卡卡号
     * @return array
     */
    public function userCardAdd($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.user.user_card.user_card-add'),
            \Config::get('validator.user.user_card.user_card-key'),
            \Config::get('validator.user.user_card.user_card-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $params['mobile']=$params['card_mobile'];
        #短信验证
        $sms = \SMSService::validateSMS($params);
        if ($sms['code'] != 1) {
            return $sms;
        }
        $user = User::userFind($params);
        $userStatus = UserStatus::userStatusFirst($params['user_id']);
        if ($userStatus['status'] == 2) {
            $vaBack = \thirdValidator::vaBankCard($params['card_number'], $user['user_idcard'], $params['mobile'], $user['real_name']);
            $vaBack = json_decode($vaBack, true);
            if ($vaBack['data']['resultCode'] == 'R001') {
                #4证一致
                if ($vaBack['data']['bankCardBin']['cardTy'] == 'D') {
                    $bank_id = \BankInfoService::bankInfoGet($vaBack['data']['bankCardBin']['bankId']);
                    if ($bank_id['code'] == 1) {
                        //todo 先这样写着，后期重写，集成其他支付
//                        $yeepay = \yeepay::bankCard($params['card_number'], $user['real_name'], '01', 1, $user['user_idcard'], $params['mobile'], $user['user_id']);
//                        if ($yeepay['code'] == 1) {
//                            $params['jl_bind_id'] = $yeepay['data']['bind_id'];
                            $params['bank_id'] = $bank_id['data'];
                            $userCard = UserCard::userCardAdd($params);
                            if ($user['card_id'] == 0) {
                                $data['card_id'] = $userCard['card_id'];
                                $data['user_id'] = $params['user_id'];
                                User::userEdit($data);
                            }
                            if ($userCard) {
                                $return = ['code' => 1, 'msg' => '银行卡添加成功'];
                            } else {
                                $return = ['code' => 10143, 'msg' => '银行卡添加失败'];
                            }
//                        } else {
//                            $return = $yeepay;
//                        }
                    } else {
                        $return = $bank_id;
                    }
                } else {
                    $return = ['code' => 10142, 'msg' => '只支持借记卡'];
                }
            } else {
                $return = ['code' => 10141, 'msg' => '身份证和银行卡不是同一个人'];
            }
        } else {
            $return = ['code' => 10140, 'msg' => '没有通过审核,不能添加银行卡'];
        }
        return $return;
    }

    /**
     * 用户银行卡的列表
     * @param $params ['user_id']    int     用户ID
     * @param $params ['page']    int     页码
     * @param $params ['limit']    int     页数
     * @return array
     */
    public function userCardList($params)
    {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $userCard = UserCard::userCardList($params);
        foreach ($userCard as $key => $vo) {
            if (!empty($vo['bank_logo'])) {
                $userCard[$key]['bank_logo'] = \Config::get('services.oss.host') . '/' . $vo['bank_logo'];
            }
            $userCard[$key]['card_number'] = substr($vo['card_number'], -4);
        }
        $data['card_list'] = $userCard;
        $data['total'] = UserCard::userCardListCount($params);
        return ['code' => 1, 'data' => $data];
    }

    /**
     * 查询用户card信息
     *
     * @param $params
     * @return array
     *
     * @author  liyongchuan
     */
    public function userCartDetail($params)
    {
        $userCard = UserCard::userCardDetail($params['card_id']);
        if ($userCard) {

            $return = ['code' => 1, 'data' => ['card_info' => $userCard]];
        } else {
            $return = ['code' => 10146, 'msg' => '查询失败'];
        }
        return $return;
    }

    /**
     * 用户银行卡删除
     * @param $params ['user_id']    int     用户ID
     * @param $params ['card_id']    int     用户银行卡ID
     * @return mixed
     */
    public function userCartDelete($params)
    {
        if (!isset($params['card_id'])) {
            return ['code' => 90002, 'msg' => '参数错误'];
        }
        $user_card = UserCard::userCardDetail($params['card_id']);
        if (!empty($user_card['jl_bind_id'])) {
            $unbank = \yeepay::unbank($user_card['card_number'], $user_card['jl_bind_id']);
            if ($unbank['code'] != 1) {
                return ['code' => 10144, 'msg' => '银行卡解绑失败'];
            }
        }
        $del = UserCard::userCardDelete($params);
        $user = User::userFind($params);
        if ($user['card_id'] == $params['card_id']) {
            $data['card_id'] = 0;
            $data['user_id'] = $params['user_id'];
            $card_id = UserCard::userCardGetId($params);
            if (!count($card_id) == 0) {
                $data['card_id'] = $card_id['0'];
            }
            User::userEdit($data);
        }
        if ($del) {
            $return['code'] = 1;
            $return['msg'] = '银行卡解绑成功';
        } else {
            $return['code'] = 10144;
            $return['msg'] = '银行卡解绑失败';
        }
        return $return;
    }

    /**
     * 获取真实姓名
     * @param $params ['user_id']        int     用户ID
     * @return array
     */
    public function userCard($params)
    {
        $user = User::userFind($params);
        $num = mb_strlen($user['real_name']);
        if ($num == 2) {
            $name = '*' . mb_substr($user['real_name'], -1, 1);
        } elseif ($num == 3) {
            $name = '*' . mb_substr($user['real_name'], -2, 2);
        } elseif ($num > 3) {
            $name = '**' . mb_substr($user['real_name'], -2, 2);
        } else {
            return ['code' => 10145, 'msg' => '真实姓名有误'];
        }
        $band= \BankInfoService::bankNameList();
        return ['code' => 1,
            'data' => ['real_name' => $name,
                'bank' => $band['data'],
                'user_mobile' => substr_replace(get_jwt('user_mobile'), '****', 3, 4)]
        ];
    }

    /**
     * 登录后更换登录密码
     * @param user_id -- jwt
     * @param mobile -- jwt
     * @param password
     * @param confirm_password
     * @return array
     */
    public function userChangePassword($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.user.user.user-changepassword'),
            \Config::get('validator.user.user.user-key'),
            \Config::get('validator.user.user.user-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }

        $condition = ['mobile' => $params['user_mobile'], 'code' => $params['code'], 'type' => 6];
        $sms_info = \SMSService::validateSMS($condition);
        if ($sms_info['code'] != 1) {  //短信验证码验证不通过
            return $sms_info;
        } else {
            $conditionEdit = ['user_id' => $params['user_id'], 'user_password' => bcrypt($params['user_password'])];
            $user = User::userEdit($conditionEdit);
            if ($user) {
                $result = ['code' => 1, 'msg' => '修改登录密码成功'];
            } else {
                $result = ['code' => 10123, 'msg' => '修改登录密码失败'];
            }
        }
        return $result;
    }

    /**
     * 激活白条功能  接口废弃  审核通过直接激活 调用service
     * jwt user_id
     */
    public function userActiveWhite($params)
    {
        #判断用户是否通过审核
        #不通过：拒绝激活 通过：调用接口返回信用值插入数据库
        $user_status = UserStatus::userStatusFirst($params['user_id']);
        if ($user_status['is_activate'] == 1) {
            return ['code' => 500, 'msg' => '白条已激活'];
        }
        if ($user_status['status'] == 2) {
            #调用接口获得信用分
            $user_info = UserInfo::userInfoDetail($params['user_id']);
            if (empty($user_info)) {
                $params_send = ['is_user_info' => 1];//未填写详细信息
            } else {
                $params_send = ['user_education' => $user_info['user_education'], 'user_profession' => $user_info['user_profession'],
                    'user_company' => $user_info['user_company'], 'user_income' => $user_info['user_income'],
                    'user_qq' => $user_info['user_qq'], 'user_email' => $user_info['user_email']];
            }
            #发送到风控系统
            $user_credit = vpost(\Config::get('interactive.riskcontrol.credit'), $params_send);
            $user_credit = json_decode($user_credit, true);
            #返回信用分 更新数据库
            if ($user_credit['code'] == 1) {
                User::userEdit(['user_id' => $params['user_id'], 'credit_point' => $user_credit['data']['credit_point'], 'white_amount' => $user_credit['data']['white_amount']]);//更新信用分
                #第一次产生白条数据，在wallet里添加数据，并在wallet_detail里记录
                UserWallet::userWalletAdd(
                    ['user_id' => $params['user_id'], 'white_money' => $user_credit['data']['white_amount']
                    ]);
                UserWalletDetail::userWalletDetailAdd(
                    ['user_id' => $params['user_id'], 'change_money' => $user_credit['data']['white_amount'], 'type' => 1, 'status' => 2, 'surplus_white_money' => $user_credit['data']['white_amount']
                    ]);
                UserStatus::userStatusUpdate(['user_id' => $params['user_id'], 'is_activate' => 1]);//更新激活白条状态
                #生成一个唯一的授信码 并插入数据库
                try {
                    $credit_code = $this->updateUserCreditCode($params);
                } catch (Exception $e) {
                    $credit_code = $this->updateUserCreditCode($params);
                }
            }

//            $description = '恭喜您授信成功，获得白条额度'.$user_credit['data']['white_amount'].'，授信码为'.$credit_code.'。';
//            $push = [
//                'operate_type' => '3',
//                'user_id' => $params['user_id'],
//                'audience' => 'regis_id',
//                'title' => '白条激活成功',
//                'description' => $description,
//                'result' => json_encode(['code' => 1, 'msg' => $description]),
//                'message_type' => 'active_white',
//                'type' => 2, // 1公告 2通知
//            ];
//            \MessageService::messageEntry($push);

            $result = ['code' => 1, 'msg' => '激活白条成功'];
        } else {
            $result = ['code' => 10129, 'msg' => '激活白条失败，审核未通过'];
        }
        return $result;
    }

    /**
     * 更新用户授信码  被调用
     */
    public function updateUserCreditCode($params)
    {
        $credit_code = getRandomkeys();
        $condition = ['user_id' => $params['user_id'], 'credit_code' => $credit_code];
        User::userEdit($condition);
        return $credit_code;
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
                $res['days_remind_str'] = ''; //设置初始值   还款日
                if ($res['should_pay_amount'] > 0) {
                    $res['days_remind_str'] = $this->getDaysRemindStr(); //获取还款日还有X天
                }
                $user_status = $this->getLoginFlag($params['user_id']);
                $res['status'] = $user_status['status'];
                return ['code' => 1, 'data' => $res];
            } else {
                return ['code' => 500, 'msg' => '请求出错'];
            }
        } else {
            $data = $this->getLoginFlag($params['user_id']);
            return ['code' => 10129, 'msg' => '未激活白条', 'data' => $data];
        }
    }

    /**
     * 获取还款日还有X天
     * @return string
     */
    public function getDaysRemindStr()
    {
//        $now_date_d = date('d');
//        $compare = 20 - $now_date_d;
//        $days = get_constant_cache('surplus_day','credit');
//        if ($compare <= $days && $compare > 0)
//            return $days_remind_str = "距离还款日还有" . $compare . '天';
//        else if ($compare == 0)
//            return $days_remind_str = "还款日就是今天";
//        else
//            return $days_remind_str = "";


        $days = get_constant_cache('surplus_day', 'credit');
        \Log::info('redis remindDays is ' . json_encode($days));
        $now_date_d = date('d');
        if ($now_date_d <= 20) { //如果当月小于20号
            if ($now_date_d == 20) {
                return $days_remind_str = "还款日就是今天";
            } else {
                $compare = 20 - $now_date_d;
                if ($compare <= $days && $compare > 0)
                    return $days_remind_str = "距离还款日还有" . $compare . '天';
                else
                    return $days_remind_str = "";//不显示
            }
        } else {    //如果当月大于20号  查下个月的出账日 即Y-(m+1)-20
            $next_m = date("m", strtotime("+1 month"));
            $next_date_str = date('Y') . '-' . $next_m . '-' . '20';
            $today = strtotime(date('Y-m-d'));
            $compare = ceil((strtotime($next_date_str) - $today) / 86400); //获取距离下个月20号剩余X天
            if ($compare <= $days) {
                return $days_remind_str = "距离还款日还有" . $compare . '天';
            } else {
                return $days_remind_str = "";//不显示
            }
        }
    }

    /**
     * 分期明细(该分期的大概信息 + 该分期的所有期数的信息)
     * $params['user_id'] jwt
     * $params['contract_sn'] 合同ID
     */
    public function userInstalmentInfo($params)
    {
        $result = ['code' => 1, 'msg' => '查询成功'];
        $params_send = ['user_id' => get_user_id(), 'contract_sn' => $params['contract_sn']];
        $install_info = vpost(\Config::get('interactive.riskcontrol.install_info'), $params_send);
        $install_info = json_decode($install_info, true);
        if ($install_info['code'] == 1) {
            $result['data']['bill_info'] = $install_info['data'];
        } else {
            $result['data']['bill_info'] = [];
        }

        return $result;

    }

    /**
     * TODO 我的首页
     * $params['user_id'] jwt
     * @author 曹晗
     */
    public function myIndex($params)
    {
        $user = User::userFind($params);
        $user_status = UserStatus::userStatusFirst($params['user_id']);
        if (!empty($user['user_portrait']))
            $user['user_portrait'] = \Config::get('services.oss.host') . '/' . $user['user_portrait'] . \Config::get('user.avatar_spec');//头像
        else
            $user['user_portrait'] = "http://ideabuy.oss-cn-hangzhou.aliyuncs.com/static/avatar/def_icon_head.png" . \Config::get('user.avatar_spec');
        #头像 手机 信用分
        $data = ['user_portrait' => $user['user_portrait'], 'user_mobile' => $user['user_mobile'], 'credit_point' => $user['credit_point'], 'status' => $user_status['status']];
        $data += $this->getLoginFlag($params['user_id']);
        $data['order_flag'] = 0;//订单红点
        #TODO 模拟数据 联通接口  实时话费 余额 流量
        $data += ['real_time_charge' => number_format(0, 2, '.', ''),
            'phone_balance' => number_format(0, 2, '.', ''),
            'residual_data' => number_format(0, 2, '.', '')];
        return ['code' => 1, 'data' => $data];
    }

    /**
     * 添加收获地址
     * @param int $param ['province']   省
     * @param int $param ['city']       市
     * @param int $param ['district']   区
     * @param int $param ['street']   街道
     * @param string $param ['address'] 详细地址
     * @return array
     */
    public function userAddressAdd($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.user.address.address-add'),
            \Config::get('validator.user.address.address-key'),
            \Config::get('validator.user.address.address-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $result = ['code' => 10171, 'msg' => "添加失败"];
        $count=Address::where('user_id','=',$params['user_id'])->get();
       if(count($count)>10){
           return ['code' => 10130, 'msg' => "最多为10条收货地址"];
       }
        $addInfo = Address::userAddressAdd($params);
        $region_info = \RegionService::regionGet(['province'=>$addInfo['province'],'city'=>$addInfo['city'],'district'=>$addInfo['district']]);
        $addInfo['province_name'] = $region_info['data']['province'];
        $addInfo['city_name'] = $region_info['data']['city'];
        $addInfo['district_name'] = $region_info['data']['district'];
        if ($addInfo) {
            $result['code'] = 1;
            $result['msg'] = "添加成功";
            #添加成功返回最新添加的address_id
            $result['address_id']=$addInfo;
        }
        return $result;
    }

    /**
     * 删除地址
     * @param int $param ['address_id']   收获地址ID
     * @return array
     */
    public function userAddressDelete($params)
    {
        $result = ['code' => 10172, 'msg' => "删除失败"];
        if (!isset($params['address_id'])) {
            return ['code' => 90002, 'msg' => '请输入地址ID'];
        }
        #判断用户是否删除自己的地址
        $hadConfirm = Address::userConfirm($params);
        if ($hadConfirm['user_id'] == $params['user_id']) {
            $deleteInfo = Address::userAddressDelete($params);
            if ($deleteInfo) {
                $result['code'] = 1;
                $result['msg'] = "删除成功";
            }
            return $result;
        }
        return $result;
    }

    /**
     * 修改账单，还款日期
     * @param $params
     * @return array
     */
    public function userEditDate($params)
    {
        $edit = User::userEdit($params);
        if ($edit) {
            $return = ['code' => 1, 'msg' => '修改成功'];
        } else {
            $return = ['code' => 10175, 'msg' => '修改失败'];
        }
        return $return;
    }

    /**
     * 查询地址详情
     * @param int $param ['address_id'] 地址ID 必选
     * @return array
     */
    public function userAddressDetail($params)
    {
        if (!isset($params['address_id'])) {
            return ['code' => 90002, 'msg' => '请输入用户收货ID'];
        }
//        $hadAdd = Address::userAddressHadAdd($params);
//        if (count($hadAdd) == 0) {
//            return ['code' => 10174, 'msg' => "用户没有添加过地址，无法查询"];
//        }
        $address_info = Address::userAddressDetail($params['address_id']);
        if($address_info){
            $result['data']['address_info'] = $address_info;
            $result['code'] = 1;
            $result['msg'] = "查询成功";
        }else{
            $result['code'] = 10175;
            $result['code'] = '该地址不存在或已删除';
        }
        return $result;
    }

    /**
     * 查询用户收货地址
     * @param int $param ['address_id'] 地址ID 必选
     * @return arrayist
     */
    public function userAddressList($params)
    {
//        $hadAdd = Address::userAddressHadAdd($params);
//        if (count($hadAdd) == 0) {
//            return ['code' => 10174, 'msg' => "用户没有添加过地址，无法查询"];
//        }
        $result['data']['address_list'] = Address::userAddressList($params['user_id']);
        $result['code'] = 1;
        $result['msg'] = "查询成功";
        return $result;
    }
    /**
     * 设为默认地址
     * @param int $param ['address_id'] 地址ID 必选
     * @return arrayist
     */
    public function userAddressDefault($params)
    {
        if (!isset($params['address_id'])) {
            return ['code' => 90001, 'msg' => '请输入用户收货ID'];
        }
        $res= Address::userAddressDefault($params);
        if($res){
            $result['code'] = 1;
            $result['msg'] = "设置成功";
        }else{
            return ['code'=>10131,'msg'=>'设置失败'];
        }
        return $result;
    }
    /**
     * 编辑地址
     * @param int $param ['province']   省
     * @param int $param ['city']       市
     * @param int $param ['district']   区
     * @param int $param ['street']   街道
     * @param string $param ['address'] 详细地址
     * @return array
     */
    public function userAddressEdit($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.user.address.address-edit'),
            \Config::get('validator.user.address.address-key'),
            \Config::get('validator.user.address.address-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        if (!isset($params['address_id'])) {
            return ['code' => 90002, 'msg' => '请输入地址ID'];
        }
        $result = ['code' => 10173, 'msg' => "更新失败"];
        #判断用户是否更新自己的地址
        $hadConfirm = Address::userConfirm($params);
        if ($hadConfirm['user_id'] == $params['user_id']) {
            $address_id = $params['address_id'];
            unset($params['user_id']);
            unset($params['s']);
            $editinfo = Address::userAddressEdit($address_id, $params);
            if ($editinfo == 1) {
                $result['code'] = 1;
                $result['msg'] = "更新成功";
                $data=Address::where('address_id',$address_id)->first();
                $region_info = \RegionService::regionGet(['province'=>$data['province'],'city'=>$data['city'],'district'=>$data['district']]);
                $data['province_name'] = $region_info['data']['province'];
                $data['city_name'] = $region_info['data']['city'];
                $data['district_name'] = $region_info['data']['district'];
                $result['data']=$data;
            }
            return $result;
        }

        return $result;
    }


    /**
     * 授信二维码的json
     * @param $params ['user_id']  jwt
     * @return array
     */
    public function userCreditCodeJson($params)
    {
        $user = User::userFind($params);
        $result = ['code' => 1, 'msg' => '查询成功'];
        $credit_code_hint = substr($user['credit_code'], 0, 4) . "****";
        $result['data'] = [
            'credit_code' => $user['credit_code'],
            'credit_code_hint' => $credit_code_hint,
        ];
        return $result;
    }

    /**
     * 验证授权码JSON
     * @param $params ['credit_code'] 授权码
     * @return array
     */
    public function userValidateCreditCode($params)
    {
        if (isset($params['credit_code'])) {
            $user = User::userValidateCreditCode($params);
            if ($user > 0) {
                $result = ['code' => 1, 'msg' => '授信码验证通过'];
            } else {
                $result = ['code' => 10128, 'msg' => '授信码验证不通过'];
            }
        } else {
            $result = ['code' => 90002, 'msg' => 'credit_code必填'];
        }
        return $result;
    }

    /**
     * 畅想购首页
     */
    public function ideabuyIndex()
    {
        $data['search_keyword'] = \Config::get('user.search_keyword');//首页 查询关键词 hint
        $notice_ste = \MessageService::getFirstAnnouncement();
        if ($notice_ste['code'] == 1)
            $data['notice_str'] = $notice_ste['data'];//首页 滚动语句
        else
            $data['notice_str'] = "";
//         $data['notice_str'] = '今天，2017年8月28日，中国的情人节，七夕到了~~~~~~~~';
        $data_foreach = ['mainindex_ad_banner', 'mainindex_ad_hot', 'mainindex_ad_flow'];
        foreach ($data_foreach as $key => $vo) {
            $params['type'] = $vo;
            $ad_info = \AdService::adObtain($params);
            if ($ad_info['code'] == 1)
                $data[$vo] = $ad_info['data'];
            else
                $data[$vo] = [];
        }
        return ['code' => 1, 'msg' => '查询成功', 'data' => $data];
    }

    /**
     * 获取用户信息填写完整度的接口
     */
    public function getLoginFlagData($user_id)
    {
        $data = $this->getLoginFlag($user_id);
        return ['code' => 1, 'msg' => '查询成功', 'data' => $data];
    }

    /**
     *立即还款
     */
    public function immediateRepayment($params)
    {
        $date = date("Y-m-d");
        $params_send = ['user_id' => $params['user_id'], 'date' => $date];
        $immediate_repayment = vpost(\Config::get('interactive.riskcontrol.immediate_repayment'), $params_send);
        return $immediate_repayment;
    }

    /**
     * 简易的获取用户信息
     * @param $params ['isset_loan']     int     1查询user_info表，0不查
     * @param $params ['user_id']     int     用户ID
     * @return array
     */
    public function userInfo($params)
    {
        $user = User::userFind($params);
        if ($params['isset_loan'] != 1) {
            $user['info'] = UserInfo::userInfoDetail($params['user_id']);
        }
        return ['code' => 1, 'data' => $user];
    }

    /**
     * 用户逾期详情
     */
    public function userOverdueInfo($params)
    {
        $params_send = ['user_id' => $params['user_id'], 'date' => $params['date']];
        $overdue_info = vpost(\Config::get('interactive.riskcontrol.overdue_list'), $params_send);
        $overdue_info = json_decode($overdue_info, true);
        return $overdue_info;
    }

    /**
     * 全部账单页
     * @param $params ['user_id'] jwt
     * @return mixed
     */
    public function userAllBill($params)
    {
        $params_send = ['user_id' => $params['user_id']];
        $all_bill = vpost(\Config::get('interactive.riskcontrol.all_bill'), $params_send);
        $all_bill = json_decode($all_bill, true);
        return $all_bill;
    }

    /**
     * 通过jwt获取 真实姓名和身份证
     */
    public function userRealNameIDCard($params)
    {
        $user = User::userFind($params);
        $data = ['user_id' => $user['user_id'], 'real_name' => $user['real_name'], 'user_idcard' => $user['user_idcard']];
        return ['code' => 1, 'msg' => '查询成功', 'data' => $data];
    }


    /**
     * 用户黑名单设置
     * @param $params
     * @return array
     */
    public function userBlackStatus($params)
    {
        $validator = \Validator::make(
            $params,
            ['user_id' => 'required', 'status' => 'required']
        );
        if ($validator->fails()) {
            return ['code' => 0, 'msg' => '参数错误', 'data' => $validator->messages()];
        }
        try {
            $res = User::userBlackStatus($params);
            if ($res) {
                return ['code' => 1, 'msg' => '修改成功'];
            } else {
                return ['code' => 0, 'msg' => '修改失败'];
            }
        } catch (\Exception $exception) {
            return ['code' => 0, 'msg' => '数据修改失败'];
        }
    }

    public function userWhiteAmount($params)
    {
        $user = User::userFind($params);
        return ['code' => 1, 'msg' => '查询成功', 'data' => ['white_amount' => number_format($user['white_amount'], 2, '.', '')]];
    }

    /**
     * 会员重置
     * @param $params
     * @return array
     */
    public function userClear($params)
    {
        User::userClear($params);
        return ['code' => 1];
    }

    /**
     * 判断用户是否存在，并且通过审核了
     * @param $params
     * @return array
     */
    public function userIsExistence($params)
    {
        $data['user_id'] = $params;
        $user = User::userFind($data);
        if ($user) {
            if ($user['real_name']) {
                $return = ['code' => 1, 'msg' => '存在'];
            } else {
                $return = ['code' => 10131, 'msg' => '用户审核不通过'];
            }

        } else {
            $return = ['code' => 10130, 'msg' => '用户不存在'];
        }
        return $return;
    }

    /**
     * 查询推送第三方token
     * @param $params
     * @return array
     */
    public function userThirdFind($params)
    {
        $userThird=UserThird::userThirdFind($params);
        foreach ($userThird as $key=>$vo){
            $user=User::userFind(['user_id'=>$vo['user_id']]);
            $userThird[$key]['user_mobile']=$user['user_mobile'];
        }
        return ['code'=>1,'data'=>$userThird];
    }

    /**
     * 获取用户最后使用的设备
     * @param $params
     * @return array
     */
    public function userDeviceFind($params)
    {
        $user=User::userDeviceFind($params);
        return ['code'=>1,'data'=>$user];
    }
    /**
     * 发票 列表
     * @param $params
     * @return array
     */
    public function invoiceList($params)
    {
        $res = OrderInfo::invoiceList($params);
        #订单商品数据
        foreach ($res['list'] as $k => $v) {
            $order_goods = OrderGoods::orderGoodsDetail($v['order_sn']);
            foreach($order_goods as $m=>$n){
                $res['list'][$k] ['order_goods'][$m]['goods_name'] = $n['goods_name'];
                $res['list'][$k] ['order_goods'][$m]['goods_thumb'] = $n['goods_thumb'];
                $res['list'][$k] ['order_goods'][$m]['goods_number'] = $n['goods_number'];
                $res['list'][$k] ['order_goods'][$m]['str_attr'] = $n['str_attr'];
            }
        }
        $result['data']['order_list'] = $res['list'];
        $result['data']['total'] = $res['total'];
        $result['data']['pages'] = $res['pages'];
        return ['code'=>1,'data'=>$result];
    }
    /**
     * 发票 详细
     * @param $order_sn
     * @param $user_id
     * @return array
     */
    public function invoiceDetail($params)
    {
        $result['invoice_info'] = OrderInfo::invoiceDetail($params);
        return ['code'=>1,'data'=>$result];
    }
}

