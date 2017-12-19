<?php
/**
 * Created by PhpStorm.
 * User: pc08
 * Date: 2017/8/23
 * Time: 18:55
 */

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model {
    protected $table = 'user_wallet';
    protected $primaryKey = 'wallet_id';
    protected $fillable = ['user_id', 'user_money', 'frozen_money', 'white_money'];
    #不支持字段批量赋值
    protected $guarded = ['wallet_id'];

    public static function userWalletAdd($params) {
        return UserWallet::create($params);
    }

    public static function userWalletEdit($params) {
        return UserWallet::where('user_id',$params['user_id'])->update($params);
    }

    public static function userWalletInfo($params) {
        return UserWallet::where('user_id',$params['user_id'])->first();
    }
    /**
     * 会员余额 重置
     * @param $user_id int  会员ID
     * @return mixed
     */
    public static function UserWalletClear($params) {
        return UserWallet::where('user_id','=',$params['user_id'])->update(['white_money'=>5600]);
    }
}