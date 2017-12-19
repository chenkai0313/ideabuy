<?php
/**
 * 用户模块
 * Author: 吕成
 * Date: 2017/7/31
 */

namespace Modules\Backend\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\BaseApiController;

class UserController extends BaseApiController
{
    /**
     * 用户列表
     */
    public function userList(Request $request)
    {
        $params = $request->all();
        $result = \BackendUserService::userList($params);
        return $result;
    }

    /**
     * 用户审核列表
     */
    public function userApplyReviewList(Request $request)
    {
        $params = $request->all();
        $result = \BackendUserService::userApplyReviewList($params);
        return $result;
    }

    /**
     * 用户添加
     */
    public function userAdd(Request $request)
    {
        $params = $request->all();
        $result = \BackendUserService::userAddBackend($params);
        return $result;
    }

    /**
     * 用户审核操作
     */
    public function userReviewOperatio(Request $request)
    {
        $params = $request->all();
        $result = \BackendUserService::userReviewOperatio($params);
        return $result;
    }

    /**
     * 用户删除
     */
    public function userDelete(Request $request)
    {
        $params = $request->all();
        $user_id = $params['user_id'];
        $result = \BackendUserService::userDelete($user_id);
        return $result;
    }

    /**                                                                                                 Í
     * 用户详细
     */
    public function userInfoDetail(Request $request)
    {
        $params = $request->all();
        $result = \BackendUserService::userInfoDetail($params);
        return $result;
    }

    /**
     * 用户审核
     */
    public function userApplyReview(Request $request)
    {
        $params = $request->all();
        $result = \BackendUserService::userApplyReview($params);
        return $result;

    }


}
