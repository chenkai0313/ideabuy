<?php
/**
 * 商品品牌表
 * Author: 陈凯
 * Date: 2017/9/19
 */
namespace Modules\Goods\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class GoodsBrand extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'goods_brand';

    protected $primaryKey = 'brand_id';

    protected $fillable = ['brand_id', 'brand_name', 'brand_thumb', 'brand_desc', 'is_show'];


    /**
     * 品牌添加
     * @param string brand_name 品牌名称
     * @param string brand_thumb 品牌缩略图
     * @param string brand_desc 品牌描述
     * @param init is_show      是否展示（1是 0否）
     * @return array
     */
    public static function goodsBrandAdd($params)
    {
        $res['brand_name'] = $params['brand_name'];
        $res['brand_thumb'] = $params['brand_thumb'];
        $res['brand_desc'] = $params['brand_desc'];
        $res['is_show'] = $params['is_show'];
        return GoodsBrand::create($res);
    }

    /**
     * 品牌列表
     * @param $params ['limit'] 一页的数据
     * @param $params ['page'] 当前页
     * @param $params ['keyword'] 查询关键字 可为空
     * @return array
     */
    public static function goodsBrandList($params)
    {
        $offset = ($params['page'] - 1) * $params['limit'];
        $data = GoodsBrand::Search($params)
            ->select('brand_id', 'brand_name', 'brand_thumb', 'brand_desc', 'is_show','created_at','updated_at')
            ->orderBy('brand_id', 'desc')
            ->skip($offset)
            ->take($params['limit'])
            ->get();
        return $data;
    }

    public static function allgoodsBrandList($params)
    {
        $data = GoodsBrand::Search($params)
            ->select('brand_id', 'brand_name', 'brand_thumb', 'brand_desc')
            ->orderBy('brand_id', 'desc')
            ->get();
        return $data;
    }

    public static function goodsBrandCount($params)
    {
        return GoodsBrand::Search($params)->count();
    }

    public function scopeSearch($query, $params)
    {
      if (isset($params['keyword'])){
          return $query->where('brand_name', 'like', '%' . $params['keyword'] . '%');
      }
    }

    /**
     * 品牌详情
     * @param $params ['brand_id'] 品牌ID
     * @return array
     */
    public static function goodsBrandDetail($params)
    {
        $data = GoodsBrand::select('brand_id', 'brand_name', 'brand_thumb', 'brand_desc', 'is_show','created_at','updated_at')
            ->where('brand_id', '=', $params['brand_id'])
            ->first();
        return $data;
    }

    /**
     * 品牌删除
     * @param $params ['brand_id'] 品牌ID
     * @return array
     */
    public static function goodsBrandDelete($params)
    {
       $delete = GoodsBrand::destroy($params);
        return $delete;
    }

    /**
     * 品牌编辑
     * @param init   brand_id   品牌ID
     * @param string brand_name 品牌名称
     * @param string brand_thumb 品牌缩略图
     * @param string brand_desc 品牌描述
     * @param init   is_show    是否展示（1是 0否）
     * @return array
     * @return array
     */
    public static function goodsBrandEdit($params)
    {
        $goodBrand = GoodsBrand::find($params['brand_id']);
        $goodBrand->brand_name = $params['brand_name'];
        $goodBrand->brand_thumb = $params['brand_thumb'];
        $goodBrand->brand_desc = $params['brand_desc'];
        $goodBrand->is_show = $params['is_show'];
        $result = $goodBrand->save();
        return $result;
    }


}