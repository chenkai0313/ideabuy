<?php
/**
 * 评论模块
 * Author: CK
 */
namespace Modules\Pc\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CommentController extends Controller
{
    /**
     * 评论添加
     * @author CK
     * @param Request $request
     * @return array
     */
    public function commentAdd(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::commentAdd($params);
        return $result;
    }

    /**
     * 追加评论
     * @author CK
     * @param Request $request
     * @return array
     */
    public function commentEdit(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::commentEdit($params);
        return $result;
    }

    /**
     * 查看当前商品的所有评论
     * @author CK
     * @param Request $request
     * @return array
     */
    public function commentListProduct(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::commentListProduct($params);
        return $result;
    }

}
