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

class GoodsDesc extends Model
{
    use SoftDeletes;

    protected $table = 'goods_desc';
    protected $primaryKey = 'desc_id';
    protected $dates = ['deleted_at'];
    public $fillable = ['goods_id', 'goods_desc'];

    public static function goodsDescAdd($params)
    {
        return GoodsDesc::create($params);
    }

    public static function goodsDescDelete($params)
    {
        $result = GoodsDesc::whereIn('goods_id', $params)->delete();
        return $result;
    }

    public static function goodsDescDetail($params)
    {
        $result = GoodsDesc::where('goods_id', $params['goods_id'])->first();
        return $result;
    }

    public static function goodsDescEdit($params)
    {
        $result = GoodsDesc::where('goods_id', $params['goods_id'])->get()->first();
        $result->goods_desc = $params['goods_desc'];
        return $result->save();
    }
}