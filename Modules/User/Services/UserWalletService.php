<?php
/**.
 * User: caohan
 * Date: 2017/8/23
 *
 * 收支明细 逻辑层
 */

namespace Modules\User\Services;

use Modules\User\Models\UserWallet;
use Modules\User\Models\UserWalletDetail;

class UserWalletService {

    /**
     * 收支明细添加
     * @param $params['user_id']
     * @param $params['change_money']  变动金额(正数表示收入,负数表示支出)
     * @param $params['type']   收支类型（1白条，2支付宝，3微信，4银行卡，5余额）
     * @param $params['status'] 状态（-1无效，1支出，2收入）
     * 需计算字段 surplus_white_money(剩余白条可用额度)
     *
     * @return mixed
     */
    public function UserWalletDetailAdd($params) {
        //1.查询用户剩余白条可用额度white_money 2.插入user_wallet_detail记录 3.更新user_wallet表 white_money
        $user_white_money = UserWallet::userWalletInfo($params);
        $condition = ['user_id'=>$params['user_id'],'change_money'=>$params['change_money'],'type'=>$params['type'],'status'=>$params['status']];
        $condition['surplus_white_money'] = $user_white_money['white_money'] + $params['change_money'];
        $add = UserWalletDetail::userWalletDetailAdd($condition);
        if (!empty($add)) {
            $result = ['code'=>1,'msg'=>'插入成功'];
            #更新user_wallet表 white_money
            $edit_user_wallet = ['user_id'=>$params['user_id'],'white_money'=>$condition['surplus_white_money']];
            UserWallet::userWalletEdit($edit_user_wallet);
        } else {
            $result = ['code'=>500,'msg'=>'插入失败'];
        }
        return $result;
    }
    /**
     * 会员余额，白条余额
     * @param $params['user_id'] 会员ID
     * @return mixed
     */
    public function UserWalletInfo($params) {
        $user_wallet_info = UserWallet::userWalletInfo($params);
        $result['data'] = $user_wallet_info;
        $result['code'] = 1;
        return $result;
    }
    /**
     * 会员余额 重置
     * @param $user_id int  会员ID
     * @return mixed
     */
    public function UserWalletClear($params) {
        UserWallet::UserWalletClear($params);
        return ['code'=>1];
    }
    /**
     * 会员余额记录 清除记录
     * @param $user_id int  会员ID
     * @return mixed
     */
    public function UserWalletDetailClear($params) {
        UserWalletDetail::UserWalletDetailClear($params);
        return ['code'=>1];
    }
}