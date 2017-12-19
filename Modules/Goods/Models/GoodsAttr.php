<?php
/**
 * Created by PhpStorm.
 * User: fuyuehua
 * Date: 2017/9/19
 * Time: 13:28
 */
namespace Modules\Goods\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsAttr extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'goods_attr';
    protected $primaryKey = 'attr_id';
    public $fillable = ['goods_id', 'product_id', 'attr_name', 'attr_value', 'sort'];
    public $timestamps = false;

    public static function goodsAttrAdd($params)
    {
        return GoodsAttr::insert($params);
    }

    public static function goodsAttrList($params)
    {
        $result = GoodsAttr::select(['attr_id', 'product_id', 'attr_name', 'attr_value'])
            ->whereIn('product_id', $params)
            ->get()->toArray();
        return $result;
    }

    public static function goodsAttrDelete($params)
    {
        $result = GoodsAttr::where(function ($query) use ($params) {
            if (isset($params['product_id']) && $params['product_id']) {
                $query->whereIn('product_id', $params['product_id']);
            }
            if (isset($params['goods_id']) && $params['goods_id']) {
                $query->whereIn('goods_id', $params['goods_id']);
            }
            if (isset($params['attr_id']) && $params['attr_id']) {
                $query->whereIn('attr_id', $params['attr_id']);
            }
        });
        if (!empty($result)) {
            $result->delete();
        }
        return $result;
    }

    public static function goodsAttrEdit($params)
    {
        $result = GoodsAttr::find($params['attr_id']);
        $result->attr_name = $params['attr_name'];
        $result->attr_value = $params['attr_value'];
        $bool = $result->save();
        return $bool;
    }

    /**
     * 通过goods_id 查询attr
     * @author 曹晗
     */
    public static function goodsAttrByGoodsId($params) {
        $result = GoodsAttr::where($params)
            ->get()->toArray();
        return $result;
    }

    public static function getProductByAttrId($params)
    {
        $result = GoodsAttr::select(['attr_name', 'attr_value'])
            ->where('goods_id', $params['goods_id'])
            ->whereIn('attr_id', $params['attr_id'])
            ->get()->toArray();
        return $result;
    }

    /**
     * 根据goods_id，attr筛选出确定的货品id
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public static function getProductByAttrValue($params)
    {
        $product = GoodsAttr::where(function ($query) use ($params) {
            $query->where('goods_id', $params['goods_id']);
            $status = true;
            foreach ($params['where'] as $key => $param) {
                if ($status) {
                    $query->where($param);
                    $status = false;
                } else {
                    $query->orWhere($param);
                }
            }
        })->pluck('product_id')->toArray();

        return $product;
    }

    /**
     * 根据现有的货品id，属性名确定可以选定的属性名和属性值
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public static function getOtherAttr($params)
    {
        $query = GoodsAttr::select('attr_name', 'attr_value');
        $query->distinct();
        $query->where('goods_id', $params['goods_id']);
        $query->whereIn('product_id', $params['product_id']);
        if (count($params['attr_name']) == 1) {
            $query->whereNotIn('attr_name', $params['attr_name']);
        }
        $result = $query->get()->toArray();
        return $result;
    }
}