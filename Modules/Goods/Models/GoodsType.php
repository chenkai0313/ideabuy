<?php
/**
 * User: caohan
 * Date: 2017/9/19
 * 商品类型
 */
namespace Modules\Goods\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsType extends Model {
    use SoftDeletes;

    protected $table = 'goods_type';
    protected $primaryKey = 'type_id';
    protected $fillable = ['type_name', 'sort','type_id'];

    public $timestamps = false;//关闭自动维护

    public static function boot() {
        parent::boot();
        #只添加created_at不添加updated_at
        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public static function typeAdd($params) {
        $res = GoodsType::create($params);
        return $res;
    }

    public static function typeFindSame($params) {
        $res = GoodsType::where($params)->first();
        return $res;
    }

    public static function typeEdit($params) {
        $res = GoodsType::find($params['type_id'])->update($params);
        return $res;
    }

    public static function typeDel($type_id_arr) {
        $res = GoodsType::destroy($type_id_arr);
        return $res;
    }

    public static function typeDetail($params) {
        $res = GoodsType::select(['type_id', 'type_name', 'sort'])
            ->where('type_id', $params['type_id'])
            ->first();
        return $res;
    }

    public static function typeList($params) {
        $res = GoodsType:: Search($params)
            ->select('type_id', 'type_name', 'sort')
            ->orderBy('sort', 'desc')->orderBy('type_id', 'desc')
            ->get()->toArray();
        return $res;
    }

    #查询构造器 Like
    public function scopeSearch($query,$params)
    {
        if (isset($params['keyword'])){
            return $query->where('type_name', 'like', '%'.$params['keyword'].'%');
        }
    }
}