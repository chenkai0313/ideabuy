<?php
/**
 * 文章模块
 * Author: 曹晗
 * Date: 2017/7/25
 */
namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ArticleController extends Controller
{
    /**
     * 添加文章
     */
    public function articleAdd(Request $request) {
        $param = $request->input();
        $result = \BackendArticleService::articleAdd($param);
        return $result;
    }

    /**
     * 删除文章
     */
    public function articleDelete(Request $request) {
        $param = $request->input();
        $result = \BackendArticleService::articleDelete($param);
        return $result;
    }

    /**
     * 编辑文章
     */
    public function articleEdit(Request $request) {
        $param = $request->input();
        $result = \BackendArticleService::articleEdit($param);
        return $result;
    }

    /**
     * 单条文章详情
     */
    public function articleDetail(Request $request) {
        $param = $request->input();
        $result = \BackendArticleService::articleDetail($param);
        return $result;
    }

    /**
     * 文章列表
     */
    public function articleList(Request $request) {
        $param = $request->input();
        $result = \BackendArticleService::articleList($param);
        return $result;
    }

    /**
     * 添加分类
     * @auth 曹晗
     */
    public function articleTypeAdd(Request $request) {

        $param = $request->input();
        $result = \BackendArticleService::articleTypeAdd($param);
        return $result;
    }

    /**
     * 删除分类
     */
    public function articleTypeDelete(Request $request) {
        $param = $request->input();
        $result = \BackendArticleService::articleTypeDelete($param);
        return $result;
    }

    /**
     * 编辑分类
     */
    public function articleTypeEdit(Request $request) {
        $param = $request->input();
        $result = \BackendArticleService::articleTypeEdit($param);
        return $result;
    }

    /**
     * 查询所有分类
     */
    public function articleTypeList(Request $request) {
        $params = $request->input();
        $result = \BackendArticleService::articleTypeList($params);
        return $result;
    }

    /**
     * 查询单条分类
     */
    public function articleTypeDetail(Request $request) {
        $param = $request->input();
        $result = \BackendArticleService::articleTypeDetail($param);
        return $result;
    }

    /**
     * 下拉框返回所有数据
     */
    public function articleTypeSelect(Request $request) {
        $param = $request->input();
        $result = \BackendArticleService::articleTypeSelect();
        return $result;
    }

    /**
     * 查询类型是否可以删除
     * @param Request $request
     * @return mixed
     */
    public function articleTypeCanDelete(Request $request) {
        $param = $request->input();
        $result = \BackendArticleService::articleTypeCanDelete($param);
        return $result;
    }
}
