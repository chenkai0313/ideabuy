<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/7/25
 * Time: 13:07
 */

namespace Modules\System\Models;


use Illuminate\Database\Eloquent\Model;

class AdType extends Model
{
    #表名
    protected $table = 'system_ad_type';
    #主键
    protected $primaryKey = 'type_id';
    #支持字段批量赋值
    protected $fillable = ['type_name', 'img_size'];
    #不支持字段批量赋值
    protected $guarded = ['type_id'];

    /**
     * 广告分类的新增
     * @param $params['type_name']  string   分类名
     * @param $params['img_size']  string  广告图片大小
     * @return $this|Model
     */
    public static function adTypeAdd($params)
    {
        return AdType::create($params);
    }

    /**
     * 广告分类的搜索条件构造器
     * @param $query
     * @param $params['keyword']        string      type_name的关键词
     * @return mixed
     */
    public  function scopeSearch($query,$params)
    {
        if(!empty($params['keyword'])){
            return $query->where('type_name','like','%'.$params['keyword'].'%');
        }
    }
    /**
     *  广告分类列表
     * @param $params['page']   int     页码
     * @param $params['limit']   int     页数
     * @param $params['keyword']   string     搜索关键词
     * @return \Illuminate\Database\Eloquent\CollectionCollection|static[]
     */
    public static function adTypeList($params)
    {
        $offset=($params['page']-1)*$params['limit'];
        return AdType::select('type_id','type_name','img_size','created_at','updated_at')->
        Search($params)->orderBy('type_id', 'desc')->skip($offset)->take($params['limit'])->get()->toArray();
    }

    /**
     * 广告分类的总数
     * @param $params['keyword']        string      搜索关键词
     * @return int
     */
    public static function adTypeCount($params)
    {
        return AdType::Search($params)->count();
    }
    /**
     * 广告分类详情
     * @param $params['type_id']    int     广告分类ID
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function adTypeDetail($params)
    {
        return AdType::find($params['type_id']);
    }

    /**
     * 广告分类的修改
     * @param $params['type_id']    int     广告分类ID
     * @param $params['type_name']    string     广告分类名
     * @param $params['img_size']    string     广告图片大小
     * @return bool
     */
    public static function  adTypeEdit($params)
    {
        $adType=AdType::find($params['type_id']);
        return $adType->update($params);
    }

    /**
     * 广告分类查询全部
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function adTypeSpinner()
    {
        return AdType::select('type_id','type_name')->get();
    }
}