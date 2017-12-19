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

class GoodsProducts extends Model
{
    use SoftDeletes;
    protected $table = 'goods_products';
    protected $primaryKey = 'product_id';
    protected $dates = ['deleted_at'];
    public $fillable = ['goods_id', 'product_name', 'product_number', 'market_price', 'product_price', 'product_sn', 'goods_color_value',
        'goods_attr', 'brand_desc', 'is_show', 'sort', 'admin_id'];



    public static function goodsProductBatchAdd($params)
    {
        foreach ($params as $key => $param) {
            $product = GoodsProducts::create($param);
            $params[$key]['product_id'] = $product->product_id;
        }
        return $params;
    }

    public static function goodsProductAdd($params)
    {
        $result = GoodsProducts::create($params);
        return $result;
    }

    public static function goodsProductList($params)
    {
        $data = GoodsProducts::select(['goods_id', 'product_id', 'product_name', 'product_number', 'market_price', 'product_price',
                 'is_show', 'sort', 'admin_id'])
            ->where(function ($query) use ($params) {
                if (isset($params['goods_id']) && $params['goods_id']) {
                    $query->where('goods_id', '=', $params['goods_id']);
                }
                if (isset($params['product_name']) && $params['product_name']) {
                    $query->where('product_name', 'like', '%' . $params['product_name'] . '%');
                }
                if (isset($params['is_show']) && $params['is_show']) {
                    $query->where('is_show', $params['is_show']);
                }
                //TODO product_sn 搜索
//                if (isset($params['product_sn']) && $params['product_sn']) {
//                    $query->where('goods_products.product_sn', 'like', '%' . $params['product_sn'] . '%');
//                }
                if (isset($params['product_id']) && $params['product_id']) {
                    if (is_array($params['product_id'])) {
                        $query->whereIn('product_id', $params['product_id']);
                    } else {
                        $query->where('product_id', $params['product_id']);
                    }
                }

                return $query;
            });
        if (isset($params['all']) && $params['all']) {
            $result = $data->get()->toArray();
            return $result;
        } else {
            $limit = isset($params['limit']) && is_numeric($params['limit']) ? $params['limit'] : 15;
            $data = $data->orderByDesc('goods_products.sort')->orderByDesc('goods_products.updated_at')->paginate($limit);
            $result['pages'] = ceil($data->total() / $limit);
            $result['total'] = $data->total();
            $result['goods_product'] = $data->items();
        }

        return $result;
    }

    public static function goodsProductDelete($params)
    {
        $result = GoodsProducts::where(function ($query) use ($params) {
            if (isset($params['product_id']) && $params['product_id']) {
                $query->whereIn('product_id', $params['product_id']);
            }
            if (isset($params['goods_id']) && $params['goods_id']) {
                $query->whereIn('goods_id', $params['goods_id']);
            }
        });
        if (!empty($result)) {
            $result->delete();
        }
        return $result;
    }

    public static function goodsProductEdit($params)
    {
        $goods_product = GoodsProducts::find($params['product_id']);
//        $goods_product->product_name = $params['product_name'];
//        $goods_product->product_number = $params['product_number'];
//        $goods_product->market_price = $params['market_price'];
//        $goods_product->product_price = $params['product_price'];
//        $goods_product->goods_color_value = $params['goods_color_value'];
//        $goods_product->goods_attr = $params['goods_attr'];
//        $goods_product->brand_desc = $params['brand_desc'];
//        $goods_product->is_show = $params['is_show'];
//        $goods_product->sort = $params['sort'];

        $result = $goods_product->update($params);
        return $result;
    }

    public static function goodsProductStatusChange($params)
    {
        $goods = GoodsProducts::whereIn('product_id', $params['product_id']);
        return $goods->update(['is_show' => $params['is_show']]);
    }

    public static function goodsProductFirst($params) {
        $goods = GoodsProducts::where($params)->first();
        return $goods;
    }
    public static function goodsProductDetail($product_id) {
        return  GoodsProducts::where('product_id',$product_id)->first();
    }
    /**
     * 获取商品店铺ID
     * @param int $product_id 货品ID
     * @return int
     */
    public static function getAdminId($product_id)
    {
        $admin_id = GoodsProducts::where('product_id', $product_id)->pluck('admin_id')->toArray();
        return $admin_id[0];
    }
}