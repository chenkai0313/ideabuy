<?php
/**
 * User: caohan
 * Date: 2017/9/19
 * Goods货物属性
 */
namespace Modules\Goods\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsAttribute extends Model {
    use SoftDeletes;

    protected $table = 'goods_attribute';
    protected $primaryKey = 'attr_id';
    protected $fillable = ['attr_name', 'sort','type_id'];

    public $timestamps = false;//关闭自动维护

    public static function boot() {
        parent::boot();
        #只添加created_at不添加updated_at
        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public static function attributeAdd($params) {
        $res = GoodsAttribute::create($params);
        return $res;
    }

    public static function attributeEdit($params) {
        $res = GoodsAttribute::find($params['attr_id'])->update($params);
        return $res;
    }

    public static function attributeDel($attr_id_arr) {
        $res = GoodsAttribute::destroy($attr_id_arr);
        return $res;
    }

    public static function attributeFindSame($params) {
        $res = GoodsAttribute::where(['type_id'=>$params['type_id'],'attr_id'=>$params['attr_id'],'attr_name'=>$params['attr_name']])->first();
        return $res;
    }

    public static function attributeFindSame2($params) {
        $res = GoodsAttribute::where(['type_id'=>$params['type_id'],'attr_name'=>$params['attr_name']])->first();
        return $res;
    }

    public static function attributeDetail($params) {
        $res = GoodsAttribute::select(['type_id', 'attr_id', 'sort','attr_name'])
            ->where('attr_id', $params['attr_id'])
            ->first();
        return $res;
    }

    public static function attributeList($params) {
        $res = GoodsAttribute::Search($params)
            ->select('attr_id', 'attr_name', 'sort', 'type_id')
            ->orderBy('sort', 'desc')->orderBy('attr_id', 'desc')
            ->get()->toArray();
        return $res;
    }

    public static function attributeListByTypeId($type_id) {
        $res = GoodsAttribute::select('attr_id','attr_name','sort','type_id')
            ->where('type_id',$type_id)
            ->orderBy('sort','desc')->orderBy('attr_id','desc')
            ->get()->toArray();
        return $res;
    }

    #查询构造器 Like
    public function scopeSearch($query,$params)
    {
        if (isset($params['keyword'])){
            return $query->where('attr_name', 'like', '%'.$params['keyword'].'%');
        }
    }
}