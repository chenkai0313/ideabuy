<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4 0004
 * Time: 下午 13:26
 */

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    #表名
    protected $table = 'system_version';

    protected $primaryKey = 'id';
    #支持字段批量插入
    protected $fillable = ['device', 'version', 'version_url', 'version_content','update_type','update_mode','md5'];
    #不支持字段批量插入
    protected $guarded = ['id'];

    /**
     * 获取版本数据
     * @param $params
     * @return mixed
     *
     * @author  liyongchuan
     */
    public static function versionGet($params)
    {
        return Version::where('device', $params['device'])->first();
    }

    /**
     * version搜索条件
     * @param $query
     * @param $params
     * @return mixed
     *
     * @author  liyongchuan
     */
    public function scopeSearch($query, $params)
    {
        if(!empty($params['keyword'])){
            return $query->where('version','like','%'.$params['keyword'].'%')->orwhere('device','like','%'.$params['keyword'].'%');
        }
    }

    /**
     * version列表
     * @param $params
     * @return mixed
     *
     * @author  liyongchuan
     */
    public static function versionList($params)
    {
        $offset = ($params['page'] - 1) * $params['limit'];
        return Version::Search($params)->skip($offset)->take($params['limit'])->get();
    }

    /**
     * version总数
     * @param $params
     * @return mixed
     *
     * @author  liyongchuan
     */
    public static function versionCount($params)
    {
        return Version::Search($params)->count();
    }

    /**
     * version删除
     * @param $params
     * @return bool|null
     *
     * @author  liyongchuan
     */
    public static function versionDelete($params)
    {
        return Version::whereIn('id',$params['id'])->delete();
    }

    /**
     * version 新增
     * @param $params
     * @return bool
     *
     * @author  liyongchuan
     */
    public static function versionAdd($params)
    {
        return Version::insert($params);
    }

    /**
     * 查询该设备的version（groupby）
     * @param $params
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function versionGroup($params)
    {
        return Version::select('version')->where('device',$params['device'])->where('update_type',$params['update_type'])->groupBy('version')->get();
    }

    /**
     * 查询该版本的详情
     * @param $device
     * @param $version
     * @return mixed
     */
    public static function versionDetail($device,$version)
    {
        return Version::where('device',$device)->where('version',$version)->orderBy('created_at','desc')->first();
    }

    /**
     * 查询多版本的最新版本的详情
     * @param $device
     * @param $version
     * @return mixed
     */
    public static function versionDetailArr($device,$version)
    {
        return Version::where('device',$device)->whereIn('version',$version)->orderBy('created_at','desc')->first();
    }

    /**
     * 前端版本查询
     * @param $device
     * @param $version
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function versionFront($device,$version)
    {
        return Version::where('device',$device)->whereIn('version',$version)->orderBy('created_at','desc')->get();
    }

}