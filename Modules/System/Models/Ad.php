<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/7/25
 * Time: 13:02
 */

namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{
    #表名
    protected $table = 'system_ad';

    protected $primaryKey = 'ad_id';
    #支持字段批量插入
    protected $fillable = ['type_id', 'ad_img','is_show','location_href','goods_id','cat_id','brand_id'];
    #不支持字段批量插入
    protected $guarded = ['ad_id'];
    #开启软删除
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    /**
     * 广告的删除
     * @param $params ['ad_id']    int     广告id
     * @return bool|null
     */
    public static function adDelete($params)
    {
        return Ad::destroy($params['ad_id']);
    }
    /**
     * 广告的搜索条件构造器
     *
     * @param $query
     * @param $params['keyword']        string      type_name的关键词
     * @return mixed
     */
    public  function scopeSearch($query,$params)
    {
        if(!empty($params['keyword'])){
            return $query->where('system_ad_type.type_name','like','%'.$params['keyword'].'%');
        }
    }
    /**
     *  广告列表
     * @param $params ['page']   int     页码
     * @param $params ['limit']   int     页数
     * @param $params ['keyword']   string     搜索关键词
     *
     * @return \Illuminate\Database\Eloquent\CollectionCollection|static[]
     */
    public static function adList($params)
    {
        $offset = ($params['page'] - 1) * $params['limit'];
        return Ad::leftjoin('system_ad_type', 'system_ad.type_id', '=', 'system_ad_type.type_id')->Search($params)->
        select('ad_id', 'ad_img', 'is_show','system_ad.created_at','system_ad.updated_at','system_ad_type.type_name','system_ad_type.img_size','system_ad.location_href','goods_id','cat_id','brand_id')->
        skip($offset)->take($params['limit'])->get()->toArray();
    }

    /**
     * 广告的总数
     *
     * @param $params ['keyword']        string      搜索关键词
     *
     * @return int
     */
    public static function adCount($params)
    {
        return Ad::leftjoin('system_ad_type', 'system_ad.type_id', '=', 'system_ad_type.type_id')->Search($params)->count();
    }

    /**
     * 广告的新增
     *
     * @param $params['type_id']       int     广告分类ID
     * @param $params['ad_img']       int     广告图片oss的路径
     * @param $params['is_show']       int     是否显示
     *
     * @return $this|Model
     */
    public static function adAdd($params)
    {
        return Ad::create($params);
    }

    /**
     * 广告的详情
     * @param $params['ad_id']      int
     * @return \Illuminate\Support\Collection
     */
    public static function adDetail($params)
    {
        return Ad::leftjoin('system_ad_type', 'system_ad.type_id', '=', 'system_ad_type.type_id')
            ->select('system_ad.ad_id','system_ad.ad_img','system_ad.is_show','system_ad_type.type_name','system_ad_type.img_size','system_ad.type_id','system_ad.location_href')
            ->where('system_ad.ad_id',$params['ad_id'])->first();
    }

    /**
     * 广告的编辑
     *
     * @param $params['ad_id']      int     广告ID
     * @param $params['ad_img']      string     广告图片oss上的路径
     * @param $params['is_show']      int     是否显示
     *
     * @return bool
     */
    public static function adEdit($params)
    {
        $ad=Ad::find($params['ad_id']);
        return $ad->update($params);
    }

    /**
     * api广告的获取
     * @param $params['type_id']    int     广告的分类ID
     * @return \Illuminate\Support\Collection
     */
    public static function adObtain($params)
    {
        return Ad::where('type_id',$params['type_id'])->where('is_show',0)->pluck('ad_img');
    }

    /**
     * api广告的获取  get方法
     * @param $params['type_id']    int     广告的分类ID
     * @return \Illuminate\Support\Collection
     */
    public static function adObtainGet($params)
    {
        return Ad::where('type_id',$params['type_id'])->where('is_show',0)->select('ad_img','location_href')->get();
    }
}