<?php
/**
 * Created by PhpStorm.
 * User: pc06
 * Date: 2017/8/19
 * Time: 11:00
 */

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserThird extends Model
{
    protected $table = 'user_third';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'openid', 'type', 'access_token', 'refresh_token', 'jpush_token', 'code', 'created_at', 'updated_at'];
    #不支持字段批量赋值
    protected $guarded = ['id'];

    #查询第三方表userid
    public static function userThirdDetail($params)
    {
        return UserThird::where('user_id', $params['user_id'])->first();

    }

    #添加第三方表信息
    public static function userThirdAdd($params)
    {
        if( isset($params['registration_id']) ){
            $params['jpush_token'] = $params['registration_id'];
            unset($params['registration_id']);
        }
        return UserThird::create($params);
    }

    #修改极光注册id
    public static function userThirdEdit($params)
    {
        if( isset($params['registration_id']) ){
            $params['jpush_token'] = $params['registration_id'];
            unset($params['registration_id']);
        }
        $user_third = UserThird::where('user_id',$params['user_id']);
        return $user_third->update($params);
    }

    /**
     * 获取推送第三方token
     * @param $params
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function userThirdFind($params)
    {
        return UserThird::whereIn('user_id',$params['user_id'])->select('user_id','jpush_token')->get();
    }

}