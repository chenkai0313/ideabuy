<?php

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Backend\Http\Requests\version\AddVersionRequest;
use Modules\Backend\Http\Requests\version\DeleteVersionRequest;

class VersionController extends Controller
{
    /**
     * version 列表
     * @param Request $request
     * @return array
     *
     * @author  liyongchuan
     */
    public function versionList(Request $request)
    {
        $params = $request->input();
        return \BackendVersionService::versionList($params);
    }

    /**
     * version 删除
     * @param DeleteVersionRequest $request
     * @return mixed
     *
     * @author  liyongchuan
     */
    public function versionDelete(DeleteVersionRequest $request)
    {
        $params=$request->input();
        return \BackendVersionService::versionDelete($params);
    }

    /**
     * version  新增方法
     * @param Request $request
     * @return mixed
     *
     * @author  liyongchuan
     */
    public function versionAdd(Request $request)
    {
        $params=$request->input();
        $params=json_decode($params['json'],true);
        unset($params['json']);
        return \BackendVersionService::versionAdd($params);
    }

    /**
     * version 新增显示
     * @return mixed
     *
     * @author  liyongchuan
     */
    public function versionAddDisplay()
    {
        return \BackendVersionService::versionAddDisplay();
    }
}
