<?php
/**
 * 管理员表
 * Author: 葛宏华
 * Date: 2017/7/25
 */
namespace Modules\Backend\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class Admin extends Authenticatable
{
    use EntrustUserTrait { restore as private restoreA; }
    use SoftDeletes { restore as private restoreB; }

    protected $table      = 'admins';

    protected $primaryKey = 'admin_id';

    protected $fillable = array('admin_name','admin_nick','admin_password','admin_sex','admin_birthday');

    protected $dates = ['deleted_at'];

    /**
     * 解决 EntrustUserTrait 和 SoftDeletes 冲突
     */
    public function restore()
    {
        $this->restoreA();
        $this->restoreB();
    }
    /**
     * 管理员 列表
     * @param int $limit 每页显示数量
     * @param int $page 当前页数
     * @return array
     */
    public static function adminList($params)
    {
        #参数
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 10;
        #获取数据
        $keyword = isset($params['keyword']) ? $params['keyword'] : '';
        $total = Admin::where(function($query) use($keyword) {
                        $query->where('admin_name', 'like', '%'.strip_tags($keyword).'%')->orWhere('admin_nick', 'like', '%'.strip_tags($keyword).'%');
                   })->count();
        $pages = ceil($total/$params['limit']);
        $list = Admin::select(['admin_id','admin_name','admin_nick','admin_sex','created_at','admin_birthday'])
                ->where(function($query) use($keyword) {
                        $query->where('admin_name', 'like', '%'.strip_tags($keyword).'%')->orWhere('admin_nick', 'like', '%'.strip_tags($keyword).'%');
                   })
                ->orderBy('created_at','DESC')->paginate($params['limit'])->toArray()['data'];
        #返回
        $result['list'] = $list;
        $result['total'] = $total;
        $result['pages'] = $pages;
        return $result;
    }
    /**
     * 管理员  添加
     * @param string $admin_name 账号
     * @param string $admin_password 密码
     * @return array
     */
    public static function adminAdd($params){
        $params['admin_password'] = $params['admin_password'] ? bcrypt($params['admin_password']) : bcrypt('111111');
        $res = Admin::create($params);
        return $res->admin_id;
    }
    /**
     * 管理员  编辑
     * @param int $admin_id 管理员ID
     * @param string $admin_password 密码
     * @return array
     */
    public static function adminEdit($params){
        $admin = Admin::find($params['admin_id']);
        if(!empty($params['admin_password'])){
            $admin->admin_password = bcrypt($params['admin_password']);
        }
        $admin->admin_sex = $params['admin_sex'];
        $admin->admin_birthday = $params['admin_birthday'] ? $params['admin_birthday'] : null;
        $admin->admin_nick = $params['admin_nick'];
        $result = $admin->save();
        return $result;
    }
    /**
     * 管理员  详情
     * @param int $admin_id 管理员ID
     * @return array
     */
    public static function adminDetail($admin_id){
        $result = Admin::select(['admin_id','admin_name','admin_sex','admin_birthday','admin_nick','created_at'])->where('admin_id',$admin_id)->first();
        #消除null值
        if($result){
            $result['admin_birthday'] = is_null($result['admin_birthday']) ? '' : $result['admin_birthday'];
        }
        return $result;
    }
    /**
     * 管理员  删除
     * @param int $admin_id 管理员ID
     * @return array
     */
    public static function adminDelete($admin_id){
        $admin_ids = explode(',',$admin_id);
        $result = Admin::whereIn('admin_id',$admin_ids)->where('admin_id','!=',1)->delete();
        return $result;
    }
    /**
     * 管理员  账号是否唯一
     * @param int $admin_id 管理员ID
     * @return array
     */
    public static function adminExist($admin_name,$admin_id=''){
        $result = Admin::where('admin_name',$admin_name)->where(function($query) use($admin_id) {
                        $query->where('admin_id', '!=',$admin_id);
                   })->count();
        return $result;
    }
    /**
     * 管理员  登录
     * @param string $admin_name 管理员账号
     * @param string $admin_password 管理员密码
     * @return bool
     */
    public static function adminInfo($admin_name){
        $result = Admin::where(['admin_name'=>$admin_name])->select('admin_id','admin_name','admin_nick','admin_password')->first();
        return $result;
    }

    public static function adminInfoById($admin_id)
    {
        $result = Admin::where('admin_id', $admin_id)
            ->select(['admin_nick','city', 'admin_mobile'])
            ->first();
        return $result;
    }
}