<?php
/**
 * 后台日志管理模块
 * Author: 叶帆
 * Date: 2017/8/16
 */

namespace Modules\Backend\Services;

use Modules\Backend\Models\Permission;
use Modules\Backend\Models\AdminLog;
use Modules\Backend\Models\Admin;
use Illuminate\Support\Facades\DB;

use Modules\Backend\Models\UserThird;

// code：10200~10209

class AdminLogService
{
    /**
     * 日志 列表
     * @param int $limit 每页显示数量
     * @param int $page 当前页数
     * @return array
     */
    public function adminLogList($params){
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $res = AdminLog::adminLogList($params);
        foreach ($res['list'] as $key => $vo){
            $res['list'][$key]['operate_result'] = $vo['operate_status'] == 1 ? '操作成功' : '操作失败';
        }
        $result['data']['admin_list'] = $res['list'];
        $result['data']['total'] = $res['total'];
        $result['data']['pages'] = $res['pages'];
        $result['code'] = 1;
        return $result;
    }

    /**
     * 日志 列表
     * @param int $limit 每页显示数量
     * @param int $page 当前页数
     * @return array
     */
    public function adminLogDetail($params){
        if (empty($params['log_id'])) {
            return ['code' => 10200, 'msg' => 'log_id 必填'];
        }
        $res = AdminLog::adminLogDetail($params);
        if ($res) {
            $res['operate_result'] = $res['operate_status'] == 1 ? '操作成功' : '操作失败';
            $data = ['log_detail' => $res];
            return ['code' => 1, 'msg' => '查询成功', 'data' => $data];
        } else {
            return ['code' => 10201, 'msg' => '日志查询失败'];
        }
    }

    /**
     * 日志 添加
     * @param int $operate_type 数据操作类型(0未知 1增加 2删除 3修改 4查看 5登录)
     * @param string $name 需要添加日志的function名称
     * @param string $operate_ip 操作ip
     * @param string $operate_content 日志记录内容
     * @param int $operate_status 操作状态：1成功，2失败
     * @param string $remark 备注
     * @return array
     */
    public function adminLogAdd($params){
        // 操作ip地址
        if(!isset($params['operate_ip'])||$params['operate_ip']==""){
            $params['operate_ip'] = '127.0.0.1';
        }
        // 处理不储存于权限表的操作：登录
        if($params['name'] == 'adminLogin'){
            $params['operate_name'] = $params['name']; // 管理员登录
            $params['operate_module'] = 'admin';
        }else{
            $res1 = Permission::permissionDetailByName($params['name']);
            if($res1 && $res1['level'] == 3){
                $params['operate_name'] = $res1['display_name'];
                $res2 = Permission::permissionDetail($res1['pid']);
                if($res2){
                    $params['operate_module'] = $res2['display_name'];
                }
            }else{
                $params['operate_name'] = $params['name'];
                $params['operate_module'] = '无';
                $params['remark'] = '记录日志出错：'.$params['name'].'操作方法不存在于权限表中';
            }

            // 获取管理员id
            $params['admin_id'] = get_admin_id();
            // 管理员账号
            $adminDetail = Admin::adminDetail($params['admin_id']);
            $params['admin_name'] = $adminDetail['admin_name'];
        }

        // 操作时间
        $params['operate_time'] = date('Y-m-d H:i:s',time());
        // 操作类型
        if( !is_numeric($params['operate_type']) ){
            switch($params['operate_type']){
                case "增加":
                    $params['operate_type'] = 1; break;
                case "添加":
                    $params['operate_type'] = 1; break;
                case "删除":
                    $params['operate_type'] = 2; break;
                case "修改":
                    $params['operate_type'] = 3; break;
                case "编辑":
                    $params['operate_type'] = 3; break;
                case "查看":
                    $params['operate_type'] = 4; break;
                case "登录":
                    $params['operate_type'] = 5; break;
                default:
                    $params['operate_type'] = 0; break;
            }
        }
        unset($params['name']);
        $res = AdminLog::adminLogAdd($params);
        if($res){
            $result = ['code' => 1, 'msg' => '日志添加成功'];
        }else{
            $result = ['code' => 10200, 'msg' => '日志添加失败'];
        }
        return $result;
    }

    /**
     * 日志 添加 new
     * @param array $data 请求参数
     * @param string $routes 路由参数
     * @param string $ip 操作ip
     * @param array $result 操作结果
     *
     */
    public function LogAdd($params){
        $routes = explode('@',$params['routes']['controller']);       // 访问地址
        $controller = $routes[0];
        $model = $routes[1];
        // 处理特殊操作-登录
        $log_data['remark'] = isset($params['result']['msg']) ? $params['result']['msg']:"无";
        //取消日志列表记录  防止字段过多无法显示
        if (in_array($params['routes']['controller'], ['LogController@logList', 'LogController@logDetail'])) {
            return ;
        }
        if($model == 'adminLogin'){
            if (empty($params['data']['admin_name'])){
                return (11111);
                $log_data['admin_name'] = '未知';
                $log_data['admin_id'] = 0;
            } else {
                $log_data['admin_name'] = $params['data']['admin_name'];
                $adminDetail = Admin::adminInfo($params['data']['admin_name']);
                $log_data['admin_id'] = $adminDetail['admin_id'];
            }
        }else{
            $res1 = Permission::permissionDetailByName($model);
            if($res1 && $res1['level'] == 3){
                $model = $res1['display_name'];
                $res2 = Permission::permissionDetail($res1['pid']);
                if($res2){
                    $controller = $res2['display_name'];
                }
            }else{
                $log_data['remark'] = '记录日志出错：'.$model.'操作方法不存在于权限表中';
            }

            // 获取管理员id
            $log_data['admin_id'] = get_admin_id();
            // 管理员账号
            $adminDetail = Admin::adminDetail($log_data['admin_id']);
            $log_data['admin_name'] = $adminDetail['admin_name'];
        }
        // 操作ip地址
        $log_data['operate_ip'] = $params['ip'];
        $log_data['operate_time'] = date('Y-m-d H:i:s',time());
        $log_data['operate_target'] = $controller."/".$model;
        $log_data['operate_status'] = isset($params['result']['code']) && $params['result']['code'] == 1 ? 1 : 2;
        $log_data['operate_content'] = json_encode(['request'=>$params['data'], 'response'=>$params['result']]);
        $log_data['admin_id'] = is_null( $log_data['admin_id'])?0: $log_data['admin_id'];
        $log_data['admin_name']= is_null( $log_data['admin_name'])? $params['data']['admin_name']: $log_data['admin_name'];
        $operate_id = AdminLog::adminLogAdd($log_data);
        if(!$operate_id){
            \Log::error('后台日志');
        }
    }

}