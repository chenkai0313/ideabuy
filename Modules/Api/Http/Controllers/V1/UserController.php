<?php
/**
 * Created by PhpStorm.
 * User: 曹晗
 * Date: 2017/7/31
 * 用户登录等操作 控制器
 */

namespace Modules\Api\Http\Controllers\V1;


use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\Order\Models\OrderGoods;

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
    public function userAdd(Request $request) {
        $params = $request->input();
        $params['client_version'] = is_null($request->header('version'))?"":$request->header('version');
        $params['client_device'] = is_null($request->header('device'))?"":$request->header('device');
        $result = \UserService::userAdd($params);
        return $result;
    }

    /**
     * 用户获取验证码后(Common)-》修改密码
     * 不需要jwt
     */
    public function userEdit(Request $request) {
        $params = $request->input();
        $result = \UserService::userEdit($params);
        return $result;
    }

    /**
     * 用户登录
     * 不需要jwt
     */
    public function userLogin(Request $request) {
        $params = $request->input();
        #暂时关闭这个功能
        $params['client_version'] = is_null($request->header('version'))?"":$request->header('version');
        $params['client_device'] = is_null($request->header('device'))?"":$request->header('device');
        $result = \UserService::userLogin($params);
        return $result;
    }

    /**
     * 设置交易密码
     * @param Request $request
     * @return array
     */
    public function userSetPayPwd(Request $request)
    {
        $params = $request->input();
        $params['user_id']=get_user_id();
        return \UserService::userSetPayPwd($params);
    }

    /**
     * 修改交易密码
     * @param Request $request
     * @return mixed
     */
    public function userEditPayPwd(Request $request)
    {
        $params = $request->input();
        $params['user_id']=get_user_id();
        $params['mobile']=get_jwt('user_mobile');
        $params['type'] = 3;//sms类型
        return \UserService::userEditPayPwd($params);
    }

    /**
     * 用户身份证号码添加
     *
     * @param Request $request
     * @return bool
     */
    public function userEditIdCard(Request $request)
    {
        $params = $request->input();
        $params['user_id']=get_user_id();
        return \UserService::userEditIdCard($params);
    }

    /**
     * 用户身份证图片上传到
     * @param Request $request
     * @return array
     */
    public function userEditIdImg(Request $request)
    {
        $params = $request->input();
        $params['user_id']=get_user_id();
        return \UserService::userEditIdImg($params);
    }

    /**
     * 添加常用联系人
     */
   public function linkManAdd(Request $request) {
       $params = $request->input();
       $params['user_mobile'] = get_jwt('user_mobile');
       $params['user_id'] = get_user_id();
       $result = \UserService::linkManAdd($params);
       return $result;
   }

    /**
     * 完善用户信息
     */
   public function userInfoAdd(Request $request) {
       $params = $request->input();
       $params['user_id'] = get_user_id();
       $result = \UserService::userInfoAdd($params);
       return $result;
   }

    /**
     * 头像上传接口
     */
   public function userHeadImgUpload(Request $request) {
       $params = $request->input();
       $params['user_id'] = get_user_id();
       $result = \UserService::userHeadImgUpload($params);
       return $result;
   }


    /**
     * 用户绑定新的手机号
     */
   public function userChangeMobile(Request $request) {
       $params = $request->input();
       $params['user_id'] = get_user_id();
       $params['type'] = 5;
       $result = \UserService::userChangeMobile($params);
       return $result;
   }

   /**
    * 用户详情接口
    */
   public function userInfoDetail(Request $request) {
       $params = $request->input();
       $params['user_id'] = get_user_id();
       $result = \UserService::userInfoDetail($params); 
       return $result;
   }

    /**
     * 添加银行卡
     * @param Request $request
     * @return mixed
     */
    public function userCardAdd(Request $request)
    {
        $params = $request->input();
        $params['user_id']=get_user_id();
        //$params['mobile']=get_jwt('user_mobile');
        $params['type'] = 4;//sms类型
        return \UserService::userCardAdd($params);
    }

    /**
     * 用户银行卡的列表
     * @param Request $request
     * @return mixed
     */
    public function userCardList(Request $request)
    {
        $params = $request->input();
        $params['user_id']=get_user_id();
        return \UserService::userCardList($params);
    }

    /**
     * 用户银行卡的删除
     * @param Request $request
     * @return mixed
     */
    public function userCartDelete(Request $request)
    {
        $params=$request->input();
        $params['user_id']=get_user_id();
        return \UserService::userCartDelete($params);
    }

    /**
     * 用户添加银行卡
     *
     * @return mixed
     */
    public function userCard()
    {
        $params['user_id'] = get_user_id();
        return \UserService::userCard($params);
    }

    /**
     * 用户在登录状态修改password
     */
    public function userChangePassword(Request $request) {
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $params['user_mobile'] = get_jwt('user_mobile');
        $result = \UserService::userChangePassword($params);
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
     * 激活白条功能
     */
    public function userActiveWhite() {
        $params['user_id']=get_user_id();
        $result = \UserService::userActiveWhite($params);
        return $result;
    }

    /**
     * TODO 白条首页(还款首页)
     */
    public function whiteIndex() {
        $params['user_id']=get_user_id();
        $result = \UserService::whiteIndex($params);
        return $result;
    }

    /**
     * TODO 我的首页
     */
    public function myIndex() {
        $params['user_id']=get_user_id();
        $result = \UserService::myIndex($params);
        return $result;
    }

    /**
     * TODO 我的账单
     * $params['user_id'] jwt
     */
    public function repaymentsIndex(Request $request) {
        $params = $request->input();
        $params['page']=1;$params['limit']=50;
        $params['user_id']=get_user_id();
        $result = \OrderService::userRepaymentsOrder($params);

        return $result;
    }

    /**
     * 全部账单
     */
    public function userAllBill() {
        $params['user_id']=get_user_id();
        $result = \UserService::userAllBill($params);
        return $result;
    }

    /**
     * 分期明细(该分期的大概信息 + 该分期的所有期数的信息)
     * $params['user_id'] jwt
     * $params['contract_sn'] 分期ID
     */
    public function userInstalmentInfo(Request $request) {
        $params = $request->input();
        $params['user_id']=get_user_id();
        $result = \UserService::userInstalmentInfo($params);
        return $result;
    }

    /**
     * 返回各个分期方式的金额等信息
     * @param Request $request
     * @return array
     */
    public function getInstallTypePlan(Request $request) {
        $params = $request->input();
        $params_send = ['amount' => $params['amount']];
        $vpost_res = vpost(\Config::get('interactive.riskcontrol.install_getinstalltypeplan'), $params_send);
        $result = ['code' =>1 , 'msg'=>'查询成功'];
        $vpost_res = json_decode($vpost_res);
        $result['data'] = $vpost_res->data->list;
        return $result;
    }


    /**
     * 返回授信二维码JSON
     */
    public function userCreditCodeJson() {
        $params['user_id']=get_user_id();
        $result = \UserService::userCreditCodeJson($params);
        return $result;
    }

    /**
     * 验证授权码JSON
     */
    public function userValidateCreditCode(Request $request) {
        $params = $request->input();
        $result = \UserService::userValidateCreditCode($params);
        return $result;
    }

    /**
     * 畅想购首页
     */
    public function ideabuyIndex() {
        $result = \UserService::ideabuyIndex();
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
     * 确认分期按钮
     */
    public function confirmInstall(Request $request) {
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $params['loan_product_id'] = 1;
        $result =\OrderService::confirmInstall($params);
        return $result;
    }

    /**
     * 立即还款按钮
     */
    public function immediateRepayment(Request $request) {
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $result =\UserService::immediateRepayment($params);
        return $result;
    }

    /**
     * 用户 逾期明细
     */
    public function userOverdueInfo(Request $request) {
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $result =\UserService::userOverdueInfo($params);
        return $result;
    }

    /**
     * 通过jwt获取 真实姓名和身份证
     */
    public function userRealNameIDCard() {
        $params['user_id'] = get_user_id();
        $result =\UserService::userRealNameIDCard($params);
        return $result;
    }


    public function userBlackStatus(Request $request)
    {
        $params = $request->all();
        $result = \UserService::userBlackStatus($params);
        return $result;
    }

    /**
     * 通过用户id查WhiteAmount
     */
    public function userWhiteAmount(Request $request) {
        $params = $request->input();
        $result =\UserService::userWhiteAmount($params);
        return $result;
    }

    /**
     * app扫码登录 绑定用户
     */
    public function bindQruuid(Request $request) {
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $result = \QruuidService::bindQruuid($params);
        return $result;
    }
}
