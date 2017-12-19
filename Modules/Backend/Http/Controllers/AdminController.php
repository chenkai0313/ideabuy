<?php
/**
 * 管理员模块
 * Author: 葛宏华
 * Date: 2017/7/25
 */
namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    /**
    * 管理员列表
    */
    public  function adminList(Request $request)
    {
        $params = $request->all();
        $result = \AdminService::adminList($params);
        return $result;
    }
    /**
     * 管理员添加
     */
    public function adminAdd(Request $request)
    {
        $params = $request->all();
        $result = \AdminService::adminAdd($params);
        return $result;
    }
    /**
     * 管理员编辑
     */
    public function adminEdit(Request $request)
    {
        $params = $request->all();
        $result = \AdminService::adminEdit($params);
        return $result;
    }
    /**
     * 管理员删除
     */
    public function adminDelete(Request $request)
    {
        $params = $request->all();
        $result = \AdminService::adminDelete($params);
        return $result;
    }
    /**                                                                                                 Í
     * 管理员详细
     */
    public function adminDetail(Request $request)
    {
        $params = $request->all();
        $result = \AdminService::adminDetail($params['admin_id']);
        return $result;
    }
    /**
     * 管理员登录
     */
    public function adminLogin(Request $request)
    {
        $params = $request->all();
        $result = \AdminService::adminLogin($params);
        return $result;
    }
}
