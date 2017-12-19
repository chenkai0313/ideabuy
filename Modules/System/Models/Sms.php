<?php

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    protected $table      = 'system_sms';
    protected $primaryKey = 'sms_id';
    protected $fillable = array('mobile','code','status','type');
    public $timestamps = false;

    //获取器
    public function getCreatedAtAttribute($value)
    {
        return strtotime($value);
    }

    /**
     * 添加短信验证码
     * @param $params
     */
    public static function smsAdd($params) {
        $sms['mobile'] = $params['mobile'];
        $sms['code'] = $params['code'];
        $sms['type'] = $params['type'];
        return Sms::create($sms);
    }

    /**
     * 查询是否存在验证码
     * @return mixed
     */
    public static function searchSMS($params) {
        return Sms::where(['mobile'=>$params['mobile'],'type'=>$params['type'],'status'=>0])
            ->select('code','created_at')->first();
    }

    /**
     * 用户注册成功后弃用本条验证码
     */
    public static function smsStatusEdit($params) {
        return Sms::where(['mobile'=>$params['mobile'],'type'=>$params['type']])
            ->update(['status' => 1]);
    }

    /**
     * 注册用，对比验证码
     */
    public static function compareSMS($params) {
        return Sms::where(['mobile'=>$params['mobile'],'type'=>$params['type'],'status'=>0])
            ->select('code')->first();
    }

    public static function boot()
    {
        parent::boot();
        #只添加created_at不添加updated_at
        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }




}
