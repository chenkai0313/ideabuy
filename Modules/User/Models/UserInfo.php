<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $table      = 'user_info';
    protected $primaryKey = 'info_id';
    #支持字段批量赋值
    protected $fillable = ['user_id', 'user_education', 'user_profession', 'user_income','user_company',
        'user_qq', 'user_email', 'link_man', 'link_mobile', 'link_relation','province', 'city', 'district', 'address'];
    #不支持字段批量赋值
    protected $guarded = ['info_id'];

    /**
     * 用户详细信息修改
     *
     * @param $params['user_id']        int     用户ID
     * $fillable数组里的参数
     * @return bool
     */
    public static function userInfoEdit($params)
    {
        $user_info=UserInfo::find($params['info_id']);
        return $user_info->update($params);
    }

    /**
     * 添加用户信息
     * @param $params
     * @return $this|Model
     */
    public static function userInfoAdd($params) {
        return UserInfo::create($params);
    }

    /**
     * 通过user_id返回info_id
     */
    public static function userInfoId($params) {
        return  UserInfo::where('user_id',$params['user_id'])
            ->value('info_id');
    }

    /**
     * 通过user_id查询所有信息
     */
    public static function userInfoDetail($user_id) {
        return UserInfo::where('user_id',$user_id)->first();
    }

    /**
     * 用户基本详情
     * @param $params['info_id']    int     详细ID
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function UserBasicInfo($params)
    {
        return UserInfo::leftjoin('users', 'user_info.user_id', '=', 'users.user_id')
            ->select('user_info.info_id','user_info.user_education','user_info.user_profession','user_info.user_company','user_info.user_income','user_info.user_qq','user_info.user_email','user_info.link_man','user_info.link_mobile','user_info.link_relation','users.card_id','users.address_id','users.real_name','users.user_mobile','users.user_idcard','users.user_portrait','users.white_amount')
            ->where('users.user_id',$params['user_id'])->first();
    }
}