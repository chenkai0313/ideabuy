<?php
/**
 * Created by PhpStorm.
 * User: pc08
 * Date: 2017/9/27
 * Time: 11:07
 */

namespace Modules\System\Models;
use Illuminate\Database\Eloquent\Model;

class Qruuid extends Model
{
    protected $table = 'system_qruuid';
    protected $primaryKey = 'id';
    protected $fillable = ['qruuid', 'user_uuid','status','url','token'];

    public $timestamps = false;//关闭自动维护

    public static function boot() {
        parent::boot();
        #只添加created_at不添加updated_at
        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
            //$model->updated_at = $model->freshTimeStamp();
        });
    }

    //获取器
    public function getCreatedAtAttribute($value)
    {
        return strtotime($value);
    }

    public static function qruuidAdd($params) {
        return Qruuid::create($params);
    }

    public static function qruuidFirst($qruuid) {
        return Qruuid::where('qruuid',$qruuid)->first();
    }

    public static function qruuidDetail($qruuid) {
        return Qruuid::where('qruuid',$qruuid)->first();
    }

    /**
     * qruuid过期用
     */
    public static function qruuidEdit($qruuid) {
        return Qruuid::where('qruuid',$qruuid)->update(['status'=>1]);
    }

    /**
     * qruuid废弃
     */
    public static function qruuidDel($qruuid) {
        return Qruuid::where('qruuid',$qruuid)->update(['status'=>3]);
    }

    /**
     * app端绑定用
     */
    public static function qruuidBind($params) {
        return Qruuid::where('qruuid',$params['qruuid'])->update($params);
    }
}