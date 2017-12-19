<?php
/**
 * 广告模块
 * Author: 李永传
 * Date: 2017/7/25
 */
namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdController extends Controller
{
    /**
     * 广告分类的新增
     *
     * @param Request $request
     * @return mixed
     */
    public function adTypeAdd(Request $request)
    {
        $params=$request->input();
        return \BackendAdService::adTypeAdd($params);
    }

    /**
     * 广告分类列表
     *
     * @param Request $request
     * @return mixed
     */
    public function adTypeList(Request $request)
    {
        $params=$request->input();
        return \BackendAdService::adTypeList($params);
    }
    /**
     * 广告分类详情
     * @param Request $request
     * @return mixed
     */
    public function adTypeDetail(Request $request)
    {
        $params=$request->input();
        return \BackendAdService::adTypeDetail($params);
    }

    /**
     * 广告分类编辑
     *
     * @param Request $request
     * @return mixed
     */
    public function adTypeEdit(Request $request)
    {
        $params=$request->input();
        return \BackendAdService::adTypeEdit($params);
    }
    /**
     * 广告分类的所有type_id,type_name
     *
     * @return mixed
     */
    public function adTypeSpinner()
    {
        return \BackendAdService::adTypeSpinner();
    }
    /**
     * 广告列表
     *
     * @param Request $request
     * @return array
     */
    public function adList(Request $request)
    {
        $params=$request->input();
        return \BackendAdService::adList($params);
    }

    /**
     * 广告删除
     * @param Request $request
     * @return mixed
     */
    public function adDelete(Request $request)
    {
        $params=$request->input();
        return \BackendAdService::adDelete($params);
    }

    /**
     * 广告的新增
     *
     * @param Request $request
     * @return mixed
     */
    public function adAdd(Request $request)
    {
        $params=$request->input();
        return \BackendAdService::adAdd($params);
    }

    /**
     * 广告的详情
     * @param Request $request
     * @return mixed
     */
    public function adDetail(Request $request)
    {
        $params=$request->input();
        return \BackendAdService::adDetail($params);
    }

    /**
     * 广告的编辑
     *
     * @param Request $request
     * @return mixed
     */
    public function adEdit(Request $request)
    {
        $params=$request->input();
        return \BackendAdService::adEdit($params);
    }
}