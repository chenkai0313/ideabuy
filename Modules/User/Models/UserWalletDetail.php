<?php
/**
 * Created by PhpStorm.
 * User: caohan
 * Date: 2017/8/23
 *
 * 收支明细表
 */

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWalletDetail extends Model {
    protected $table = 'user_wallet_detail';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'change_money', 'surplus_money', 'surplus_white_money', 'type', 'status'];
    #不支持字段批量赋值
    protected $guarded = ['id'];

    use SoftDeletes;

    public static function userWalletDetailAdd($params) {
        return UserWalletDetail::create($params);
    }

    public static function userWalletDetailEdit($params) {
        return UserWalletDetail::where('user_id',$params['user_id'])->update($params);
    }

    public static function userWalletDetailInfo($params) {
        return UserWalletDetail::where('user_id',$params['user_id'])->orderBy('id', 'desc')->first();
    }
    /**
     * 会员余额 重置
     * @param $user_id int  会员ID
     * @return mixed
     */
    public static function UserWalletDetailClear($params) {
        return  UserWalletDetail::where('user_id', $params['user_id'])->forceDelete();
    }
}