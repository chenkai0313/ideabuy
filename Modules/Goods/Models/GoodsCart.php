<?php
/**
 * 用户购物车表
 * Author: caohan
 * Date: 2017/9/19
 */
namespace Modules\Goods\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsCart extends Model {
    use SoftDeletes;
    protected $table = 'goods_cart';
    protected $primaryKey = 'cart_id';
    protected $fillable = ['user_id', 'admin_id', 'goods_id', 'product_id', 'goods_sn', 'goods_name',
        'goods_number', 'goods_attr', 'market_price', 'product_price', 'goods_thumb', 'goods_img', 'sort'];

    public static function cartAdd($params) {
        $add = GoodsCart::create($params);
        return $add;
    }

    public static function cartFindSame($params) {
        $update = GoodsCart::where($params['condition'])
           ->first();
        return $update;
    }

    public static function cartUpdateNumber($params) {
        $update = GoodsCart::where($params['condition'])
            ->update(['goods_number'=>$params['goods_number']]);
        return $update;
    }

    public static function cartAdminIDListByUserId($params) {
        $list = GoodsCart::where($params['condition'])
            ->orderBy('sort')
            ->get()->toArray();
        return $list;
    }

    public static function cartAdminIdByUserId($user_id) {
        $list = GoodsCart::where('user_id',$user_id)
            ->leftJoin('admins','goods_cart.admin_id','admins.admin_id')
            ->select('goods_cart.admin_id','admins.admin_nick')
            ->distinct('admin_id')
            ->orderBy('sort')
            ->get()->toArray();
        return $list;
    }

    public static function cartDel($del_arr) {
        $del = GoodsCart::destroy($del_arr);
        return $del;
    }

    public static function cartCountByUserId($user_id) {
        $count = GoodsCart::where(['user_id'=>$user_id])->count();
        return $count;
    }
}