<?php
/**
 * 文章模块
 * Author: 曹晗
 */
namespace Modules\Pc\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ArticleController extends Controller
{
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

}
