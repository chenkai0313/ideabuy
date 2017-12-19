<?php
/**
 * 管理员模块
 * Author: 葛宏华
 * Date: 2017/7/25
 */
namespace Modules\Backend\Services;

use Modules\Backend\Models\Admin;
use Modules\Backend\Models\RoleAdmin;
use Illuminate\Support\Facades\DB;
use Modules\Backend\Models\Role;
use JWTAuth;

class AdminService
{
    /**
     * 管理员 列表
     * @param int $limit 每页显示数量
     * @param int $page 当前页数
     * @return array
     */
    public function adminList($params){
        $res = Admin::adminList($params);
        $result['data']['admin_list'] = $res['list'];
        $result['data']['total'] = $res['total'];
        $result['data']['pages'] = $res['pages'];
        $result['code'] = 1;
        return $result;
    }
    /**
     * 管理员  添加
     * @param string $admin_name 账号
     * @param string $admin_password 密码
     * @param string $province 省（供应商）
     * @param string $city 市（供应商）
     * @param string $district 区（供应商）
     * @return array
     */
    public function adminAdd($params){
        if($params['role_name'] == '供应商'){
            $validator = \Validator::make($params, [
                'admin_name' => 'required|unique:admins|min:5|max:20',
                'province' => 'required',
                'city' => 'required',
                'district' => 'required',
                //'admin_password' => 'required|min:6',
            ], [
                'required' => ':attribute为必填项',
                'min' => ':attribute长度不符合要求',
                'unique' => ':attribute必须唯一'
            ],[
                'admin_name' => '管理员账号',
                'province' => '省',
                'city' => '市',
                'district' => '区',
                //'admin_password' => '管理员密码',
            ]);
        }else{
            $validator = \Validator::make($params, [
                'admin_name' => 'required|unique:admins|min:5|max:20',
                //'admin_password' => 'required|min:6',
            ], [
                'required' => ':attribute为必填项',
                'min' => ':attribute长度不符合要求',
                'unique' => ':attribute必须唯一'
            ],[
                'admin_name' => '管理员账号',
                //'admin_password' => '管理员密码',
            ]);
        }
        if($validator->passes()){
            if(!Admin::adminExist($params['admin_name'])){
                if($params['role_id']){
                    DB::beginTransaction();
                    $res1 = Admin::adminAdd($params);
                    #用户权限
                    $params2['admin_id'] = $res1;
                    $params2['role_id'] = $params['role_id'];
                    $res2 = RoleAdmin::roleAdminAdd($params2);
                    if($res1 && $res2){
                        DB::commit();
                        $result['code'] = 1;
                        $result['msg'] = '添加成功';
                    }else{
                        DB::rollback();
                        $result['code'] = 10001;
                        $result['msg'] = '添加失败';
                    }
                }else {
                    $result['code'] = 90001;
                    $result['msg'] = '请添加用户权限';
                }
            }else{
                $result['code'] = 10004;
                $result['msg'] = '该管理账号已存在';
            }
        }else{
            $result['code'] = 90002;
            $result['msg'] = $validator->messages()->first();
        }

        return $result;
    }
    /**
     * 管理员  编辑
     * @param int $admin_id 管理员ID
     * @param string $admin_password 密码
     * @return array
     */
    public function adminEdit($params){
        if($params['role_id']){
            DB::beginTransaction();
            $res1 = Admin::adminEdit($params);
            #用户权限  先删除再插入
            $res2 = RoleAdmin::roleAdminDelete($params['admin_id']);
            $params3['admin_id'] = $params['admin_id'];
            $params3['role_id'] = $params['role_id'];
            $res3 = RoleAdmin::roleAdminAdd($params3);
            if($res1!=false && $res2!==false && $res3){
                DB::commit();
                $result['code'] = 1;
                $result['msg'] = '编辑成功';
            }else{
                DB::rollback();
                $result['code'] = 10002;
                $result['msg'] = '编辑失败';
            }
        }else{
            $result['code'] = 90001;
            $result['msg'] = '请添加用户权限';
        }
        return $result;
    }
    /**
     * 管理员  详情
     * @param int $admin_id 管理员ID
     * @return array
     */
    public function adminDetail($admin_id){
        #已有角色
        $has_role_list = RoleAdmin::adminRoleID($admin_id);
        #所有角色
        $role_list = Role::roleListAll();
        #该管理员的信息
        $res = Admin::adminDetail($admin_id);
        $res['role_list'] = $role_list;
        $res['has_role_list'] = $has_role_list;
        $result['data']['admin_info'] = $res;
        $result['code'] = 1;
        return $result;
    }
    /**
     * 管理员  删除
     * @param int $admin_id 管理员ID
     * @return array
     */
    public function adminDelete($params){
        $res = Admin::adminDelete($params['admin_id']);
        if($res){
            $result['code'] = 1;
            $result['msg'] = '删除成功';
        }else{
            $result['code'] = 10003;
            $result['msg'] = '删除失败';
        }
        return $result;
    }
    /**
     * 管理员  登录
     * @param string $admin_name 管理员账号
     * @param string $admin_password 管理员密码
     * @return array
     */
    public  function adminLogin($params){
        $admin_info = Admin::adminInfo($params['admin_name']);
        if($admin_info){
            if(password_verify($params['admin_password'],$admin_info['admin_password'])){
                $result['code'] = 1;
                $result['msg'] = '登录成功';
                $customClaim = ['from' => 'admin','admin_id'=>$admin_info['admin_id'],'admin_nick'=>$admin_info['admin_nick']];
                $token = JWTAuth::fromUser($admin_info,$customClaim);
                $result['data']['token'] = $token;
                $result['data']['admin_nick'] = $admin_info['admin_nick'];
            }else{
                $result['code'] = 10005;
                $result['msg'] = '账号密码不正确';
            }

        }else {
            $result['code'] = 10006;
            $result['msg'] = '该账号不存在或已删除';
        }
        return $result;
    }
}
