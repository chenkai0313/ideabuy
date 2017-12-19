<?php
/**
 * 商品品牌表
 * Author: 陈凯
 * Date: 2017/9/19
 */
namespace Modules\Goods\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsCategory extends Model
{
    protected $table = 'goods_category';

    protected $primaryKey = 'cat_id';

    protected $fillable = ['cat_id', 'pid', 'sort_order', 'cat_name', 'cat_desc','cat_thumb','keywords','is_show','is_show_nav'];

    /**
     * 商品种类添加
     * @param string pid            父级ID
     * @param string sort_order     排序ID
     * @param string cat_name       分类名称
     * @param string cat_desc       分类描述
     * @param string cat_thumb      分类缩略图
     * @param string keywords       分类关键字
     * @param   init is_show        是否显示（0否 1是）
     * @param   init is_show_nav    是否导航显示（0否 1是）
     * @return array
     */
    public static function goodsCategoryAdd($params)
    {
        $res['pid'] = $params['pid'];
        $res['sort_order'] = $params['sort_order'];
        $res['cat_name'] = $params['cat_name'];
        $res['cat_thumb'] = $params['cat_thumb'];
        $res['cat_desc'] = $params['cat_desc'];
        $res['keywords'] = $params['keywords'];
        $res['is_show'] = $params['is_show'];
        $res['is_show_nav'] = $params['is_show_nav'];
        return GoodsCategory::create($res);
    }
    /**
     * 商品种类详情
     * @param $params ['brand_id'] 品牌ID
     * @return array
     */
    public static function goodsCategoryDetail($params){
        $data = GoodsCategory::select('cat_id', 'sort_order','pid', 'cat_name', 'cat_thumb', 'cat_desc','keywords','is_show','is_show_nav','created_at','updated_at')
            ->where('cat_id', '=', $params['cat_id'])
            ->first();
        return $data;
    }
    /**
     * 商品种类列表（层级结构）
     * @return array
     */
    public static function goodsCategoryListAll($params){
        $data=GoodsCategory::select('cat_id', 'sort_order','pid', 'cat_name', 'cat_thumb', 'cat_desc','keywords','is_show','is_show_nav','created_at','updated_at')
            ->Search($params)
            ->where('is_show','=',1)
            ->orderBy('sort_order','desc','cat_id','asc')
            ->get()
            ->toArray();
        return $data;
    }
    /**
     * 商品种类列表（树状结构）
     * @return array
     */
    public static function goodsCategoryListTree($params){
        $data=GoodsCategory::select('cat_id', 'sort_order','cat_name','pid', 'cat_thumb','created_at')
            ->Search($params)
            ->orderBy('sort_order','desc','cat_id','asc')
            ->get()
            ->toArray();
        return $data;
    }

    public static function goodsCategoryCount($params)
    {
        return GoodsCategory::Search($params)->count();
    }

    public function scopeSearch($query, $params)
    {
        if (isset($params['keyword'])) {
            return $query->where('cat_name', 'like', '%' . $params['keyword'] . '%');
        }

        if(isset($params['is_show']) && $params['is_show']==1){
            return $query->where('is_show', 1);
        }
    }
    /**
     * 商品种类修改
     * @param string cat_id         种类ID
     * @param string pid            父级ID
     * @param string sort_order     排序ID
     * @param string cat_name       分类名称
     * @param string cat_desc       分类描述
     * @param string cat_thumb      分类缩略图
     * @param string keywords       分类关键字
     * @param   init is_show        是否显示（0否 1是）
     * @param   init is_show_nav    是否导航显示（0否 1是）
     * @return array
     */
    public static function goodsCategoryEdit($params)
    {
        $goodCategory = GoodsCategory::find($params['cat_id']);
        $goodCategory->pid = $params['pid'];
        $goodCategory->sort_order = $params['sort_order'];
        $goodCategory->cat_name = $params['cat_name'];
        $goodCategory->cat_thumb = $params['cat_thumb'];
        $goodCategory->cat_desc = $params['cat_desc'];
        $goodCategory->keywords = $params['keywords'];
        $goodCategory->is_show = $params['is_show'];
        $goodCategory->is_show_nav = $params['is_show_nav'];
        $result = $goodCategory->save();
        return $result;
    }
    /**
     * 商品种类删除
     * @param string cat_id     种类ID
     * @return array
     */
    public static function goodsCategoryDelete($params)
    {
       $delete = GoodsCategory::destroy($params);
        return $delete;
    }

}