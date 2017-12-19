<?php
/**
 * 消息模板模块
 * Author: CK
 * Date: 2017/8/12
 */
namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class  MsgTemplateController extends Controller
{
    /**
     * 添加消息模板关键字
     */
    public function msgTemplateKeywordAdd(Request $request)
    {
        $params = $request->input();
        $result = \MsgTemplateService::msgTemplateKeywordAdd($params);
        return $result;
    }

    /**
     * 查看所有消息模板关键字
     */
    public function msgTemplateKeywordList(Request $request)
    {
        $params = $request->input();
        $result = \MsgTemplateService::msgTemplateKeywordList($params);
        return $result;
    }

    /**
     * 查看单个消息模板关键字详情
     */
    public function msgTemplateKeywordDetail(Request $request)
    {
        $params = $request->input();
        $result = \MsgTemplateService::msgTemplateKeywordDetail($params);
        return $result;
    }

    /**
     * 编辑关键字
     */
    public function msgTemplateKeywordEdit(Request $request)
    {
        $params = $request->input();
        $result = \MsgTemplateService::msgTemplateKeywordEdit($params);
        return $result;
    }

    /**
     * 删除关键字
     */
    public function msgTemplateKeywordDelete(Request $request)
    {
        $params = $request->input();
        $result = \MsgTemplateService::msgTemplateKeywordDelete($params);
        return $result;
    }

    /**
     * 添加消息模板
     */
    public function msgTemplateAdd(Request $request)
    {
        $params = $request->input();
        $result = \MsgTemplateService::msgTemplateAdd($params);
        return $result;
    }

    /**
     * 查看单个消息模板关键字详情
     */
    public function msgTemplateDetail(Request $request)
    {
        $params = $request->input();
        $result = \MsgTemplateService::msgTemplateDetail($params);
        return $result;
    }

    /**
     * 修改单个关键字
     */
    public function msgTemplateEdit(Request $request)
    {
        $params = $request->input();
        $result = \MsgTemplateService::msgTemplateEdit($params);
        return $result;
    }

    /**
     * 查看所有消息模板
     */
    public function msgTemplateList(Request $request)
    {
        $params = $request->input();
        $result = \MsgTemplateService::msgTemplateList($params);
        return $result;
    }

    /**
     * 删除消息模板
     */
    public function msgTemplateDelete(Request $request)
    {
        $params = $request->input();
        $result = \MsgTemplateService::msgTemplateDelete($params);
        return $result;
    }

}