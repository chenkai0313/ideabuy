<?php

namespace Modules\User\Models;

//use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    //use Notifiable;
    protected $table      = 'users';
    protected $primaryKey = 'user_id';
    #支持字段批量赋值

    protected $fillable = ['user_mobile','user_password','pay_password','real_name',
        'user_idcard','address_id','user_portrait','card_id','credit_point','white_amount','credit_code',
        'activate_date','first_bill_date','first_pay_date','client_device','client_version'];
    #不支持字段批量赋值
    protected $guarded = ['user_id'];
    #开启软删除
    use SoftDeletes;
    protected $dates = ['deleted_at'];


    /**
     *  用户列表
     * @param $params['page']   int     页码
     * @param $params['limit']   int     页数
     * @param $params['keyword']   string     搜索关键词
     * @return \Illuminate\Database\Eloquent\CollectionCollection|static[]
     */
    public static function userList($params){
        $offset=($params['page']-1)*$params['limit'];
        return User::select('user_id','user_mobile','real_name','user_idcard','created_at','updated_at')->
        Search($params)->orderBy('user_id', 'desc')->skip($offset)->take($params['limit'])->get()->toArray();
    }

    /**
     * 用户列表的搜索条件构造器
     * @param $query
     * @param $params['keyword']        string      user_mobile的关键词
     * @return mixed
     */
    public  function scopeSearch($query,$params)
    {
        if(isset($params['keyword'])){
            return $query->where('user_mobile','like','%'.$params['keyword'].'%')
                ->orwhere('real_name','like','%'.$params['keyword'].'%')
                ->orwhere('user_idcard','like','%'.$params['keyword'].'%');
        }
    }

    /**
     * 用户列表的总数
     * @param $params['keyword']        string      搜索关键词
     * @return int
     */
    public static function userCount($params)
    {
        return User::Search($params)->count();
    }
    /**
     * 用户注册
     * @param $params
     * @return $this|Model
     */
    public static function userAdd($params) {
        return User::create($params);
    }
    /**
     * 用户修改
     *
     * @param $params['user_id']        int     用户ID
     * $fillable数组里的参数
     * @return bool
     */
    public static function userEdit($params)
    {
        $user=User::find($params['user_id']);
        return $user->update($params);
    }

    /**
     * 用户详情
     * @param $user_mobile
     * @return mixed
     */
     public static function userInfoDetail($user_mobile) {
         return User::where(['user_mobile'=>$user_mobile])->select('user_id','user_mobile','user_password','user_portrait', 'is_black')->first();
     }

    public static function userInfoDetailById($user_id) {
        return User::where(['user_id'=>$user_id])->select('user_id','user_mobile','user_password','user_portrait', 'is_black')->first();
    }

    /**
     * 查询用户所以信息
     * @param $params
     * @return \Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
     */
    public static function userFind($params)
    {
        return User::find($params['user_id']);
    }

    /**
     * 连表查询用户信息+ user_info
     * @param $params
     */
    public static function userDetail($user_id) {
        $result = User::leftJoin('user_info','user_info.user_id','=','users.user_id')
            ->where('users.user_id',$user_id)
            ->select('users.real_name','users.user_idcard','users.user_mobile','users.user_portrait','users.white_amount','user_info.user_education','user_info.user_profession','user_info.user_company'
                ,'user_info.user_income','user_info.user_qq','user_info.user_email','user_info.link_man','user_info.link_mobile','user_info.link_relation','user_info.address','user_info.province','user_info.city','user_info.district')->first();
        return $result;
    }

    public static function userValidateCreditCode($params) {
        $result = User::where([
            "credit_code"=>$params['credit_code'],
            ])->count();
        return $result;
    }

    public static function userCountByStatus($params = []) {
        return User::leftJoin('user_status', 'user_status.user_id', '=', 'users.user_id')
            ->where(function ($query) use ($params){
            if (isset($params['is_activate'])) {
                $query->where('user_status.is_activate', $params['is_activate']);
            }
            if (isset($params['status'])) {
                $query->where('user_status.status', $params['status']);
            }
        })->count();
    }

    public static function userCountByClient($params = []) {
        return User::where(function ($query) use ($params){
                if (isset($params['client_device'])) {
                    $query->where('client_device', $params['client_device']);
                }
                if (isset($params['client_version'])) {
                    $query->where('client_version', $params['client_version']);
                }
            })->count();
    }

    /**
     * 批量修改黑名单状态
     * @param $params [user_id]
     * @param $params [status]  0：不是  1：是黑名单
     * @return bool
     */
    public static function userBlackStatus($params)
    {
        $user_ids = explode(',', $params['user_id']);
        $user = User::whereIn('user_id', $user_ids);
        return $user->update(['is_black' => $params['status']]);
    }
    /**
     * 判断用户黑名单状态
     * @param $params
     * @param int $is_black
     * @return int
     */
    public static function userIsBlack($params, $is_black = 1)
    {
        return User::where('user_mobile', $params['user_mobile'])->where('is_black', $is_black)->count();
    }
    /**
     * 会员 重置
     * @param $user_id int  会员ID
     * @return mixed
     */
    public static function userClear($params) {
        return User::where('user_id','=',$params['user_id'])->update(['is_black'=>0]);
    }

    /**
     * 获取用户最后使用的设备
     * @param $params
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function userDeviceFind($params)
    {
        return User::whereIn('user_id',$params['user_id'])->select('user_id','client_device')->get();
    }
}
