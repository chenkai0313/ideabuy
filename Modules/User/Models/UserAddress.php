<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{

    protected $table = 'user_address';
    protected $primaryKey = 'address_id';
    #支持字段批量赋值
    protected $fillable = ['user_id', 'province', 'city', 'district', 'street', 'address'];

    #不支持字段批量赋值
    protected $guarded = ['address_id'];


     /** 用户地址修改
     *
     * @param $params['user_id']        int     用户ID
     * $fillable数组里的参数
     * @return $user_address
     */
    public static function userAddressEdit($params)
    {
        $user_address=UserAddress::find($params['address_id']);
        return $user_address->update($params);
    }

    /**
     * 添加用户地址
     */
    public static function userAddressAdd($params) {
        return UserAddress::create($params);
    }

    /**
     * 通过user_id返回address_id
     */
    public static function userAddressId($params) {
        return  UserAddress::where('user_id',$params['user_id'])
            ->value('address_id');
    }

    /**
     * 通过user_id返回省市区和详细地址
     */
    public static function userAddressFind($params) {
        return  UserAddress::where('user_id',$params['user_id'])
            ->select('province','city','district','address')
            ->first();
    }

}

