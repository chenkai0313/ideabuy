<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    protected $table      = 'user_status';
    protected $primaryKey = 'status_id';
    #支持字段批量赋值
    protected $fillable = ['user_id', 'is_linkman', 'is_idcard', 'is_idcard_img','is_activate',
        'status'];
    #不支持字段批量赋值
    protected $guarded = ['status_id'];
    public $timestamps = false;

    public static function userStatusUpdate($params) {
        return UserStatus::where('user_id',$params['user_id'])->update($params);
    }

    public static function userStatusAdd($params) {
        return UserStatus::create($params);
    }

    public static function userStatusFirst($user_id) {
        return UserStatus::where('user_id',$user_id)->first();
    }
}
