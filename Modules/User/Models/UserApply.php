<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/8/1
 * Time: 19:46
 */

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserApply extends Model
{
    #表名
    protected $table = 'user_apply';
    #主键
    protected $primaryKey = 'apply_id';

    #支持字段批量赋值
    protected $fillable = ['real_name', 'user_idcard', 'user_id', 'apply_type'];
    #不支持字段批量赋值
    protected $guarded = ['apply_id'];

    /**
     * 查询用户的申请信息
     *
     * @param $params ['user_id']        int     用户ID
     * @return int
     */
    public static function userApplyGet($params)
    {
        return UserApply::where('user_id', $params['user_id'])->count();
    }

    /**
     * @param $params
     * @return mixed
     */
    public static function userIdCard($params)
    {
        return UserApply::where('user_idcard',$params['user_idcard'])->where('user_id','!=',$params['user_id'])->value('user_id');
    }
    /**
     * 身份证的添加
     *
     * @param $params
     * @return $this|Model
     */
    public static function userApplyAdd($params)
    {
        return UserApply::create($params);
    }

    /**
     * 用户申请表的修改
     * @param $params
     * @return mixed
     */
    public static function userApplyEditImg($params)
    {
        return UserApply::where('user_id', $params['user_id'])->where('apply_type', 1)->update(['id_img' => $params['id_img']]);
    }

    /**
     * 用户申请表查询
     * @param $params
     * @return mixed
     */
    public static function userApplyFind($params)
    {
        return UserApply::where('user_id', $params['user_id'])->where('apply_type', 1)->first();
    }

    /**
     * 用户申请表的修改身份证图片
     * @param $params
     * @return bool
     */
    public static function userApplyEditCard($params)
    {
        return UserApply::where('user_id', $params['user_id'])->where('apply_type', 1)->update([
            'real_name' => $params['real_name'],
            'user_idcard' => $params['user_idcard'],
            'status' => $params['status']
        ]);
    }

    /**
     *  用户申请表的查询
     * @param $params
     * @return mixed
     */
    public static function userApplyInfo($params)
    {
        return UserApply::leftJoin('users', 'user_apply.user_id', '=', 'users.user_id')
            ->select('user_apply.apply_id', 'user_apply.user_id','user_apply.real_name', 'user_apply.user_idcard', 'user_apply.id_img', 'user_apply.status', 'user_apply.reason')
            ->where('user_apply.apply_type',1)
            ->where('users.user_id', $params['user_id'])->first();
    }

    /**
     * 用户申请表审核状态 1未审核 2审核通过 3 审核不通过
     * @param $params
     * @return mixed
     */
    public static function userApplyUpdate($params)
    {
        return UserApply::where('user_id', $params['user_id'])->where('apply_type',1)->update(array('status' => $params['status'], 'reason' => $params['reason']));
    }

    /**
     * 查询用户申请的所有数据
     * @param $user_id
     * @return mixed
     */
    public static function userApplyDetail($params)
    {
        return UserApply::where('user_id', $params['user_id'])->first();
    }

    /**
     *   用户审核列表
     * @param $params['page']   int     页码
     * @param $params['limit']   int     页数
     * @param $params['keyword']   string     搜索关键词
     * @return \Illuminate\Database\Eloquent\CollectionCollection|static[]
     */
    public static function userApplyReviewList($params){
        $offset=($params['page']-1)*$params['limit'];
        return UserApply::leftJoin('users', 'user_apply.user_id', '=', 'users.user_id')
            ->where('user_apply.apply_type',1)
            ->select('user_apply.apply_id','user_apply.user_id', 'user_apply.real_name', 'user_apply.user_idcard', 'user_apply.id_img', 'user_apply.status','user_apply.apply_type', 'user_apply.reason','user_apply.created_at','user_apply.updated_at')
            ->Search($params)->orderBy('apply_id', 'desc')->skip($offset)->take($params['limit'])->get()->toArray();
    }

    /**
     * 用户审核列表的搜索条件构造器
     * @param $query
     * @param $params['keyword']        string      user_mobile的关键词
     * @return mixed
     */
    public  function scopeSearch($query,$params)
    {
        if(isset($params['keyword'])){
            if(trim($params['keyword'])=='未审核'){
                $params['keyword']=1;
            }
            if(trim($params['keyword'])=='审核失败'){
                $params['keyword']=3;
            }
            if(trim($params['keyword'])=='审核成功'){
                $params['keyword']=2;
            }
            return $query->where('user_apply.status',$params['keyword'])
                ->orwhere('user_apply.real_name',$params['keyword'])
                ->orwhere('user_apply.user_idcard',$params['keyword']);
        }
    }

    /**
     * 用户审核列表的总数
     * @param $params['keyword']        string      搜索关键词
     * @return int
     */
    public static function userApplyCount($params)
    {
        return UserApply::Search($params)->count();
    }




}