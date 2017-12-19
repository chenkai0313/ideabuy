<?php
/**
 * RBAC  权限表
 * Author: 葛宏华
 * Date: 2017/7/27
 */
namespace Modules\Backend\Models;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    protected $table      = 'permissions';

    protected $primaryKey = 'id';

    /**
     * 权限 列表
     * @return array
     */
    public static function permissionList()
    {
        $list = Permission::select(['id','name','display_name','description','pid','level','show'])->where('level',1)->orderBy('id')->get();
        if($list){
            foreach($list as $k=>$v){
                $list[$k]['child'] = Permission::select(['id','name','display_name','description','pid','level','show'])->where('pid',$v['id'])->orderBy('id')->get();
                if($list[$k]['child']){
                    foreach($list[$k]['child'] as $m=>$n){
                        $list[$k]['child'][$m]['child'] = Permission::select(['id','name','display_name','description','pid','level','show'])->where('pid',$n['id'])->orderBy('id')->get();
                    }
                }
            }
        }
        return $list;
    }
    /**
     * 权限分类 列表，目前3个层次
     * @return array
     */
    public static function permissionType()
    {
        $list = Permission::select(['id','display_name','level'])->where('level',1)->orderBy('id')->get()->toArray();
        if($list){
            foreach($list as $k=>$v){
                $list[$k]['value'] = $v['id'];
                $list[$k]['label'] = $v['display_name'];
                $list[$k]['child'] = Permission::select(['id','display_name','level'])->where('pid',$v['id'])->orderBy('id')->get();
                if($list[$k]['child']){
                    foreach($list[$k]['child'] as $m=>$n){
                        $list[$k]['child'][$m]['value'] = $n['id'];
                        $list[$k]['child'][$m]['label'] = $n['display_name'];
                    }
                }
            }
        }
        $temp = [0=>['id'=>0,'display_name'=>'顶级分类','level'=>0,'child'=>[],'value'=>0,'label'=>'顶级分类']];
        $list = array_merge($temp,$list);
        return $list;
    }
    /**
     * 权限  添加  注意父级select框只需要显示一二级
     * @param string $name 名称
     * @param string $display_name 可读名称
     * @param string $description 描述
     * @param int $pid 父级ID
     * @param int $evel 等级
     * @return array
     */
    public static function permissionAdd($params){
        $permission = new Permission();
        $permission->name = $params['name'];
        $permission->display_name = $params['display_name'];
        $permission->description = $params['description'];
        $permission->pid = $params['pid'];
        $permission->level = $params['level'];
        $permission->path = $params['path'];
        $permission->show = $params['show'];
        $result = $permission->save();
        return $result;
    }
    /**
     * 权限  编辑  注意父级select框只需要显示一二级
     * @param int $permission_id 权限ID
     * @param string $name 名称
     * @param string $display_name 可读名称
     * @param string $description 描述
     * @param int $pid 父级ID
     * @param int $level 等级
     * @return array
     */
    public static function permissionEdit($params){
        $permission =  Permission::find($params['permission_id']);
        $permission->name = $params['name'];
        $permission->display_name = $params['display_name'];
        $permission->description = $params['description'];
        $permission->pid = $params['pid'];
        $permission->level = $params['level'];
        $permission->path = $params['path'];
        $permission->show = $params['show'];
        $result = $permission->save();
        return $result;
    }
    /**
     * 权限  详情
     * @param int $permisson_id 权限ID
     * @return array
     */
    public static function permissionDetail($permission_id){
        $result = Permission::where('id',$permission_id)->first();
        return $result;
    }
    /**
     * 权限  详情  name查询
     * @param string $name 权限name
     * @return array
     */
    public static function permissionDetailByName($name){
        $result = Permission::where('name',$name)->first();
        return $result;
    }
    /**
     * 权限  删除
     * @param int $permisson_id 权限ID
     * @return array
     */
    public static function permissionDelete($permission_id){
        $result = Permission::where('id',$permission_id)->delete();
        return $result;
    }
    /**
     * 权限  账号是否唯一
     * @param int $permisson_id 权限ID
     * @return array
     */
    public static function permissionExist($name,$id=''){
        $result = Permission::where('name',$name)->where(function($query) use($id) {  
                        $query->where('id', '!=',$id);
                   })->count();
        return $result;
    }

    /**
     * 菜单
     * @param int $permisson_id 权限ID
     * @return array
     */
    public static function permissionLeft($admin_id) {
        $roleId = RoleAdmin::roleAdminDetail($admin_id);
        $permission = PermissionRole::permissionRoleID($roleId[0]['role_id'])->toArray();
        $list = Permission::select(['id','display_name as name','path'])->where(['level'=>1,'show'=>1])->orderBy('id')->get();
        if($list){
            foreach($list as $k=>$v){
                if(in_array($list[$k]['id'],$permission)||$admin_id==1){
                    $res = Permission::select(['id','display_name as name','path'])->where(['pid'=>$v['id'],'show'=>1])->orderBy('id')->get();
                    if(!$res->isEmpty()){
                        foreach ($res as $k1=>$v1){
                            // 判断不存在
                            if(!in_array($res[$k1]['id'],$permission)&&$admin_id!=1){
                                unset($res[$k1]);
                                continue;
                            }
                        }
                        if(!empty($res)){
                            $list[$k]['children'] = $res;
                        }
                    }
                }else{
                    unset($list[$k]);
                }
            }
        }
        return $list;
    }
}