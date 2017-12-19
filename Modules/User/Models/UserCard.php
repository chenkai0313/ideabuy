<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/8/2
 * Time: 13:39
 */

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserCard extends Model
{

    #表名
    protected $table = 'user_card';
    #主键名
    protected $primaryKey = 'card_id';

    #支持字段批量赋值

    protected $fillable = ['card_mobile', 'card_number', 'user_id','bank_id','jl_bind_id'];
    #不支持字段批量赋值
    protected $guarded = ['card_id'];

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
     * 用户银行卡的新增
     * @param $params
     * @return $this|Model
     */
    public static function userCardAdd($params)
    {
        return UserCard::create($params);
    }
    /**
     * 用户银行卡列表
     *
     * @param $params
     * @return array
     */
    public static function userCardList($params)
    {
        $offset=($params['page']-1)*$params['limit'];
        return UserCard::leftJoin('system_bank_info', 'user_card.bank_id', '=', 'system_bank_info.bank_id')
            ->select('user_card.card_id','user_card.card_number','system_bank_info.bank_logo','system_bank_info.bank_name','system_bank_info.color_start','system_bank_info.color_stop')
            ->where('user_id', $params['user_id'])->orderBy('card_id','DESC')->skip($offset)->take($params['limit'])->get()->toArray();
    }

    /**
     * 用户银行卡列床总数
     * @param $params
     * @return int
     */
    public static function userCardListCount($params)
    {
        return UserCard::leftJoin('system_bank_info', 'user_card.bank_id', '=', 'system_bank_info.bank_id')->where('user_id', $params['user_id'])->count();
    }

    /**
     * 用户银行卡删除
     * @param $params['user_id']    int     用户ID
     * @param $params['card_id']    int     用户银行卡ID
     * @return bool|null
     */
    public static function userCardDelete($params)
    {
        return UserCard::where('user_id',$params['user_id'])->where('card_id',$params['card_id'])->delete();

    }

    /**
     * 获取银行卡ID
     * @param $params
     * @return \Illuminate\Support\Collection
     */
    public static function userCardGetId($params)
    {
        return UserCard::where('user_id', $params['user_id'])->pluck('card_id');
    }

    /**
     * 获取用户银行卡信息
     * @param $card_id
     * @return mixed
     */
    public static function userCardDetail($card_id)
    {
        return UserCard::where('card_id',$card_id)->first();
    }
}