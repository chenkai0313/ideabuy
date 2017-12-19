<?php
/**
 * Created by PhpStorm.
 * User: 曹晗
 * Date: 2017/7/31
 * 用户登录等操作 控制器
 */

namespace Modules\Pc\Http\Controllers\V1;


use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

use Modules\Pc\Http\Requests\User\AddIdCardRequest;
use Modules\Pc\Http\Requests\User\AddUserCardRequest;
use Modules\Pc\Http\Requests\User\EditPayPwdRequest;
use Modules\Pc\Http\Requests\User\SetPayPwdRequest;

use Modules\System\Services\QruuidService;


class UserController extends Controller
{

    /**
     * 发送短信 登录后修改密码等功能 mobile从jwt获取
     */
    public function addSMS(Request $request) {
        $params = $request->input();
        $params['mobile'] = get_jwt('user_mobile');
        $result = \SMSService::addSMS($params);
        return $result;
    }

    /**
     * 用户注册
     * 不需要jwt
     */
    public function userAdd(Request $request)
    {
        $params = $request->input();
        $params['client_version'] = is_null($request->header('version'))?"":$request->header('version');
        $params['client_device'] = is_null($request->header('device'))?"":$request->header('device');
        $result = \PcUserService::userAdd($params);
        return $result;
    }

    /**
     * 用户登录
     * 不需要jwt
     */
    public function userLogin(Request $request)
    {
        $params = $request->input();
        #暂时关闭这个功能
        $params['client_version'] = is_null($request->header('version'))?"":$request->header('version');
        $params['client_device'] = is_null($request->header('device'))?"":$request->header('device');
        $result = \PcUserService::userLogin($params);
        return $result;
    }

    /**
     * 用户获取验证码后(Common)-》修改密码  找回密码
     * 不需要jwt
     */
    public function userEdit(Request $request)
    {
        $params = $request->input();
        $result = \UserService::userEdit($params);
        return $result;
    }

    /**
     * 用户在登录状态修改password
     */
    public function userChangePassword(Request $request)
    {
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $params['user_mobile'] = get_jwt('user_mobile');
        $result = \PcUserService::userChangePassword($params);
        return $result;
    }

    /**
     * 添加常用联系人
     */
    public function linkManAdd(Request $request)
    {
        $params = $request->input();
        $params['user_mobile'] = get_jwt('user_mobile');
        $params['user_id'] = get_user_id();
        $result = \UserService::linkManAdd($params);
        return $result;
    }

    /**
     * PC身份证号码的添加
     * @param AddIdCardRequest $request
     * @return bool
     */
    public function userEditIdCard(AddIdCardRequest $request)
    {
        $params = $request->input();
        $params['user_id']=get_user_id();
        return \UserService::userEditIdCard($params);
    }

    /**
     * pc身份证图片的添加
     * @param Request $request
     * @return array
     */
    public function userEditIdImg(Request $request)
    {
        $params = $request->input();
        $params['user_id']=get_user_id();
        $img=\UserService::userEditIdImg($params);
        \BackendUserService::userReviewOperatio(['user_id'=>$params['user_id'],'status'=>2]);
        return $img;
    }

    /**
     * 添加银行卡
     * @param AddUserCardRequest $request
     * @return array
     */
    public function userBankAdd(AddUserCardRequest $request)
    {
        $params=$request->input();
        $params['user_id']=get_user_id();
        $params['type'] = 4;//sms类型
        return \UserService::userCardAdd($params);
    }

    /**
     * 添加银行卡显示真实姓名和支持银行
     * @return array
     */
    public function userRealName()
    {
        $params['user_id']=get_user_id();
        return \UserService::userCard($params);
    }

    /**
     * 银行列表
     * @param Request $request
     * @return array
     */
    public function userBankList(Request $request)
    {
        $params = $request->input();
        $params['user_id']=get_user_id();
        return \UserService::userCardList($params);
    }

    /**
     * 银行卡的删除
     * @param Request $request
     * @return mixed
     */
    public function userBankDelete(Request $request)
    {
        $params=$request->input();
        $params['user_id']=get_user_id();
        return \UserService::userCartDelete($params);
    }

    /**
     * 设置交易密码
     * @param SetPayPwdRequest $request
     * @return array
     */
    public function userSetPayPwd(SetPayPwdRequest $request)
    {
        $params=$request->input();
        $params['user_id']=get_user_id();
        return \UserService::userSetPayPwd($params);
    }

    /**
     * 充值交易密码
     * @param EditPayPwdRequest $request
     * @return array
     */
    public function userEditPayPwd(EditPayPwdRequest $request)
    {
        $params=$request->input();
        $params['user_id']=get_user_id();
        $params['mobile']=get_jwt('user_mobile');
        $params['type'] = 3;//sms类型
        return \UserService::userEditPayPwd($params);
    }


    /**
     * 完善用户信息
     */
    public function userInfoAdd(Request $request)
    {
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $result = \UserService::userInfoAdd($params);
        return $result;
    }

    /**
     * 用户详情接口
     */
    public function userInfoDetail(Request $request)
    {
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $params['user_mobile']=get_jwt('user_mobile');
        $result = \UserService::userInfoDetail($params);
        return $result;
    }

    /**
     * 头像上传接口
     */
    public function userHeadImgUpload(Request $request)
    {
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $result = \UserService::userHeadImgUpload($params);
        return $result;
    }

    /**
     * PC端 验证短信验证码是否通过
     * @param params 加密参数
     * @param mobile 手机
     * @param code 验证码
     * @param type 类型
     * @return mixed
     */
    public function validateSMSPC(Request $request)
    {
        $params = $request->input();
        $result = \PcUserService::validateSMSPC($params);
        return $result;
    }

    /**
     * pc端 生成图形验证码
     */
    public function addVerifyCode(Request $request)
    {
        $params = $request->input();
        $result = \VerifyCodeService::addVerifyCode($params);
        return $result;
    }

    /**
     * pc端 验证图形验证码
     */
    public function checkVerifyCode(Request $request)
    {
        $params = $request->input();
        $result = \VerifyCodeService::CheckVerifyCode($params);
        return $result;
    }

    /**
     * 找回密码时检查短信验证码和图形验证码
     */
    public function checkAllCode(Request $request)
    {
        $params = $request->input();
        $result = \PcUserService::checkAllCode($params);
        return $result;
    }


    /**
     * pc 网页 获取 二维码uuid
     */
    public function getQruuid(Request $request)
    {
        $params = $request->input();
        $result = \QruuidService::QruuidAddAndReturn($params);
        return $result;
    }


    /**
     * 检查二维码是否被绑定
     */
    public function QruuidFirst(Request $request)
    {
        $params = $request->input();
        $result = \QruuidService::QruuidFirst($params);
        return $result;
    }

    /**
     * PC端 首页右侧小标签 白条首页(还款首页)
     */
    public function whiteIndex() {
        $params['user_id']=get_user_id();
        $result = \PcUserService::whiteIndex($params);
        return $result;
    }

    /**
     * 查询状态接口
     */
    public function getLoginFlag() {
        $user_id = get_user_id();
        $result = \UserService::getLoginFlagData($user_id);
        return $result;
    }

    /**
     * 添加地址
     */
    public function userAddressAdd(Request $request) {
        $params = $request->input();
        $params['user_id']=get_user_id();
        $result = \UserService::userAddressAdd($params);
        return $result;
    }

    /**
     * 删除地址
     */
    public function  userAddressDelete(Request $request){
        $params = $request->input();
        $params['user_id']=get_user_id();
        $result = \UserService::userAddressDelete($params);
        return $result;
    }

    /**
     * 查看详细地址
     */
    public function userAddressDetail(Request $request){
        $params = $request->input();
        $params['user_id']=get_user_id();
        $result = \UserService::userAddressDetail($params);
        return $result;
    }

    /**
     * 编辑地址
     */
    public function userAddressEdit(Request $request) {
        $params = $request->input();
        $params['user_id']=get_user_id();
        $result = \UserService::userAddressEdit($params);
        return $result;
    }
    /**
     * 地址列表
     */
    public function userAddressList(Request $request) {
        $params = $request->input();
        $params['user_id']=get_user_id();
        $result = \UserService::userAddressList($params);
        return $result;
    }
    /**
     * 设为默认地址
     */
    public function userAddressDefault(Request $request) {
        $params = $request->input();
        $params['user_id']=get_user_id();
        $result = \UserService::userAddressDefault($params);
        return $result;
    }
    /**
     * 发票 列表
     */
    public function invoiceList(Request $request) {
        $params = $request->input();
        $params['user_id']=get_user_id();
        $result = \UserService::invoiceList($params);
        return $result;
    }
    /**
     * 发票 详细
     */
    public function invoiceDetail(Request $request) {
        $params = $request->input();
        $params['user_id']=get_user_id();
        $result = \UserService::invoiceDetail($params);
        return $result;
    }
    /**
     * 公告
     */
    public function announceList(Request $request){
        $params = $request->input();
        $result = \MessageService::pcAnnounce($params);
        return $result;
    }
    /**
     * 通知
     */
    public function noticeList(Request $request){
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $result = \MessageService::pcAnnounce($params);
        return $result;
    }
    /**
     * 我的白条
     */
    public function writeIndex(Request $request) {
        $params['user_id']=get_user_id();
        $result = \PcUserService::whiteIndex($params);
        return $result;
    }
}
