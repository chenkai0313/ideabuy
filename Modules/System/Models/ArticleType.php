<?php
/**
 * 类型Model
 * Author: 曹晗
 * Date: 2017/7/25
 */
namespace Modules\System\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleType extends Model
{
    protected $table = 'system_article_type';
    protected $primaryKey = 'type_id';
    protected $fillable = ['type_name','parent_id','level'];

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
     * 添加类型
     * @param string $param['type_name']   类型名称
     * @param int $param['parent_id']   父级ID
     * @param int $param['level']   层次
     * @return array
     *
     * @auth 曹晗
     */
    public static function articleTypeAdd($param) {
        return ArticleType::create($param);
    }

    /**
     * 删除类型
     * @param int $param['parent_id']   父级ID
     * @return array
     */
    public static function articleTypeDelete($param) {
        $articleType =  ArticleType::destroy($param['type_id']);
        return $articleType;
    }

    /**
     * 编辑类型
     * @param int $type_id 类型的ID
     * @param int $param['type_name']   类型名称 可选
     * @param int $param['parent_id']   父级ID 可选
     * @param int $param['level']   层级
     * @return array
     */
    public static function articleTypeEdit($type_id,$param) {
        $articleType = ArticleType::where('type_id',$type_id )->update($param);
        return $articleType;
    }

    /**
     * 查询所有类型 分页
     * @return array
     */
    public static function articleTypeList($params) {
        $offset=($params['page']-1)*$params['limit'];
        return ArticleType::Search($params)
            ->select('type_id','type_name','parent_id','level')->orderBy('type_id', 'desc')
            ->skip($offset)->take($params['limit'])
            ->get()->toArray();
    }

    /**
     * 查询不能删除的数据的parent_id
     */
    public static function articleTypeCanDelte() {
        return ArticleType::where('parent_id','>',0)->distinct('parent_id')->pluck('parent_id')->toArray();
    }

    public static function articleTypeCount($params)
    {
        return ArticleType::Search($params)->count();
    }

    /**
     * 查询单条
     * @param int $type_id 类型的ID
     * @return array
     */
    public static function articleTypeDetail($type_id) {
        $articleType = ArticleType::select('type_id','type_name','parent_id')->where('type_id',$type_id)->first();
        return $articleType;
    }

    /**
     * 查询所有类型信息 下拉框用
     */
    public static function articleTypeSelect() {
        $articleType = ArticleType::select('type_id','type_name','parent_id','level')->get()->toArray();;
        return $articleType;
    }

    /**
     * 删除数据先查询是否该数据有子数据
     */
    public static function articleTypeSonNum($type_id) {
        $articleType = ArticleType::where('parent_id',$type_id)->count();
        return $articleType;
    }

    #查询构造器 Like
    public function scopeSearch($query,$params)
    {
        if (isset($params['keyword'])){
            return $query->where('type_name', 'like', '%'.$params['keyword'].'%');
        }
    }

}
