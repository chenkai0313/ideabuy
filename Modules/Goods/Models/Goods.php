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

class Goods extends Model
{
    use SoftDeletes;

    protected $table = 'goods';
    protected $primaryKey = 'goods_id';
    protected $dates = ['deleted_at'];
    public $fillable = ['goods_sn', 'goods_name', 'cat_id', 'brand_id', 'goods_number', 'market_price', 'goods_price', 'keywords', 'goods_thumb',
        'goods_img', 'shipping_range', 'is_index', 'is_real', 'is_shipping', 'sort', 'admin_id', 'type_id', 'goods_subname'];

    public static function goodsAdd($params)
    {
        $result = Goods::create($params);
        return $result->goods_id;
    }

    public static function goodsList($params)
    {
        $limit = isset($params['limit']) && is_numeric($params['limit']) ? $params['limit'] : 15;
        $data = Goods::select(['goods_id', 'goods_sn', 'goods_name', 'cat_id', 'brand_id', 'goods_number', 'market_price', 'goods_price', 'keywords', 'goods_thumb',
            'goods_img', 'shipping_range', 'is_index', 'is_real', 'is_on_sale', 'is_shipping', 'created_at', 'updated_at', 'admin_id'])
            ->where(function ($query) use ($params) {
                if (isset($params['keywords'])) {
                    $query->where('keywords', 'like', '%' . $params['keywords'] . '%');
                    $query->orWhere('goods_sn', 'like', '%' . $params['keywords'] . '%');
                    $query->orWhere('goods_name', 'like', '%' . $params['keywords'] . '%');
                }
                if (isset($params['is_real'])) {
                    $query->where('is_real', $params['is_real']);
                }
                if (isset($params['is_on_sale'])) {
                    $query->where('is_on_sale', $params['is_on_sale']);
                }
                if (isset($params['is_shipping'])) {
                    $query->where('is_shipping', $params['is_shipping']);
                }
                if (isset($params['is_index'])) {
                    $query->where('is_index', $params['is_index']);
                }
            })->orderByDesc('sort')->orderByDesc('updated_at')->paginate($limit);
        $result['total'] = $data->total();
        $result['pages'] = ceil($data->total() / $limit);
        $result['goods'] = $data->items();
        return $result;
    }

    public static function goodsDelete($params)
    {
        $result = Goods::whereIn('goods_id', $params['goods_id'])->delete();
        return $result;
    }

    public static function goodsDetail($goods_id)
    {
        $result = Goods::select(['goods_id', 'goods_sn', 'goods_subname', 'goods_name', 'type_id', 'cat_id', 'brand_id', 'goods_number',
            'market_price', 'goods_price', 'keywords', 'goods_thumb', 'goods_img', 'shipping_range', 'is_index',
            'is_real', 'is_on_sale', 'is_shipping', 'sort', 'admin_id', 'created_at', 'updated_at'])
        ->where('goods_id', $goods_id)
        ->first();
        return $result;
    }

    public static function goodsExist($params)
    {
        $result = Goods::where('goods_name', $params['goods_name'])->count();
        return $result;
    }

    public static function goodsEdit($params)
    {
        $goods = Goods::find($params['goods_id']);
//        $goods->goods_name = $params['goods_name'];
//        $goods->cat_id = $params['cat_id'];
//        $goods->brand_id = $params['brand_id'];
//        $goods->type_id = $params['type_id'];
//        $goods->goods_number = $params['goods_number'];
//        $goods->market_price = $params['market_price'];
//        $goods->goods_price = $params['goods_price'];
//        $goods->keywords = $params['keywords'];
//        $goods->goods_thumb = $params['goods_thumb'];
//        $goods->goods_img = $params['goods_img'];
//        $goods->shipping_range = $params['shipping_range'];
//        $goods->is_index = $params['is_index'];
//        $goods->is_real = $params['is_real'];
//        $goods->is_on_sale = $params['is_on_sale'];
//        $goods->is_shipping = $params['is_shipping'];
//        $goods->sort = $params['sort'];
        $result = $goods->update($params);

        return $result;
    }
    public static function goodsStatusChange($params)
    {
        $goods = Goods::whereIn('goods_id', $params['goods_id']);
        $update = [];
        if (isset($params['is_index'])) {
            $update['is_index'] = $params['is_index'];
        }
        if (isset($params['is_real'])) {
            $update['is_real'] = $params['is_real'];
        }
        if (isset($params['is_on_sale'])) {
            $update['is_on_sale'] = $params['is_on_sale'];
        }
        if (isset($params['is_shipping'])) {
            $update['is_shipping'] = $params['is_shipping'];
        }
        return $goods->update($update);
    }

    public static function pcGoodsList($params)
    {
        $limit = isset($params['limit']) && is_numeric($params['limit']) ? $params['limit'] : 15;
        $goods = Goods::leftJoin('admins', 'admins.admin_id', '=', 'goods.admin_id')
            ->select(['goods.goods_id', 'goods.goods_name', 'goods.goods_price', 'goods.sales_number', 'goods.comment_number',
                'goods.goods_thumb', 'goods.goods_img', 'admins.admin_nick'])
            ->where('goods.is_on_sale', '1')
            ->Search($params)
            ->orderByDesc('goods.sort')->paginate($limit);
        $result['data'] = $goods->items();
        $result['total'] = $goods->total();
        $result['pages'] = ceil($goods->total() / $limit);

        return $result;
    }

    public static function pcGoodsListLeft($params)
    {
        $limit = isset($params['limit']) && is_numeric($params['limit']) ? $params['limit'] : 5;
        $goods = Goods::select(['goods_id', 'goods_name', 'goods_price', 'goods_thumb', 'goods_img'])
            ->where(function ($query) use ($params) {
                if (isset($params['type_id']) && $params['type_id']) {
                    $query->where('type_id', $params['type_id']);
                }
                $query->where('is_on_sale', 1);
            })->orderByDesc('sort')->take($limit)->get()->toArray();

        return $goods;
    }

    public static function pcGoodsDetail($params)
    {
        $result = Goods::select(['goods_id', 'goods_sn', 'goods_name', 'goods_subname', 'comment_star', 'goods_number', 'type_id', 'comment_number', 'sales_number',
            'goods_price', 'goods_thumb', 'goods_img', 'shipping_range', 'is_shipping', 'admin_id'])
            ->where('goods_id', $params['goods_id'])
            ->where('is_on_sale', 1)
            ->first();
        return $result;
    }

    public function scopeSearch($query, $params)
    {
        #关键字搜索
        if (isset($params['keywords'])) {
            $query->where('keywords', 'like', '%' . $params['keywords'] . '%');
            $query->orWhere('goods_name', 'like', '%' . $params['keywords'] . '%');
        }
        #品牌过滤
        if (isset($params['brand_id']) && !empty($params['brand_id'])) {
            $brand_id = explode(',', $params['brand_id']);
            $query->whereIn('brand_id', $brand_id);
        }

        #排序
        if (isset($params['sort']) && !empty($params['sort'])) {
            $sort = explode(',', $params['sort']);
            $column = $sort[0];
            $direction = !isset($sort[1]) || empty($sort[1]) ? 'desc' : $sort[1];
            if (in_array($column, ['sales_number', 'comment_number', 'goods_price', 'created_at'])) {
                $query->orderBy('goods.' . $column, $direction);
            }
        }

        return $query;
    }
}