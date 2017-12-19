<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/8/2
 * Time: 9:10
 */

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    #表名
    protected $table = 'system_file';
    #主键
    protected $primaryKey = 'file_id';
    #支持字段批量赋值
    protected $fillable = ['file_path', 'file_type', 'user_id'];
    #不支持字段批量赋值
    protected $guarded = ['file_id'];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
        #只添加created_at不添加updated_at
        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    /**
     * 文件路径的添加
     * @param $params
     * @return $this|Model
     */
    public static function fileAdd($params)
    {
        return File::create($params);
    }

    /**
     * 文件的删除
     * @param $params
     * @return bool|null
     */
    public static function fileDelete($params)
    {
        return File::whereIn('user_id', $params)->delete();
    }
    /**
     * 身份证照片列表
     * @param $params
     * @return $this|Model
     */
    public static function UserIdPhoto($params)
    {
        $params=explode(',',$params);
        $aa = File::whereIn('file_id', $params)->where('file_type', 1)->get()->toArray();

        return $aa;
    }


}