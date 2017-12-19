<?php
/**
 * 商品模块
 * Author: fuyuehua
 * Date: 2017/7/20
 */
namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GoodsController extends Controller
{
    /**
     * 商品选择信息
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsSelect(Request $request)
    {
        $params = $request->all();
        $result = \BackendGoodsService::goodsSelect($params);
        return $result;
    }

    /**
     * 商品添加
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsAdd(Request $request)
    {
        $params = $request->input('goods');
        $result = \BackendGoodsService::goodsAdd($params);
        return $result;
    }

    /**
     * 商品修改
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsEdit(Request $request)
    {
        $params = $request->all();
        $result = \BackendGoodsService::goodsEdit($params);
        return $result;
    }

    /**
     * 商品详情
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsDetail(Request $request)
    {
        $params = $request->all();
        $result = \BackendGoodsService::goodsDetail($params);
        return $result;
    }

    /**
     * 商品删除
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsDelete(Request $request)
    {
        $params = $request->all();
        $result = \BackendGoodsService::goodsDelete($params);
        return $result;
    }

    /**
     * 商品列表
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsList(Request $request)
    {
        $params = $request->all();
        $result = \BackendGoodsService::goodsList($params);
        return $result;
    }

    /**
     * 商品状态修改
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsStatusChange(Request $request)
    {
        $params = $request->all();
        unset($params['s']);
        $result = \BackendGoodsService::goodsStatusChange($params);
        return $result;
    }

    /**
     * 货品添加
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsProductAdd(Request $request)
    {
        $params = $request->all();
        $result = \BackendGoodsService::goodsProductAdd($params);
        return $result;
    }

    /**
     * 货品修改
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsProductEdit(Request $request)
    {
        $params = $request->all();
        $result = \BackendGoodsService::goodsProductEdit($params);
        return $result;
    }

    /**
     * 货品列表
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsProductList(Request $request)
    {
        $params = $request->all();
        $result = \BackendGoodsService::goodsProductList($params);
        return $result;
    }

    /**
     * 货品删除
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsProductDelete(Request $request)
    {
        $params = $request->all();
        $result = \BackendGoodsService::goodsProductDelete($params);
        return $result;
    }

    /**
     * 货品状态修改
     * @author fuyuehua
     * @param Request $request
     * @return array
     */
    public function goodsProductStatusChange(Request $request)
    {
        $params = $request->all();
        unset($params['s']);
        $result = \BackendGoodsService::goodsProductStatusChange($params);
        return $result;
    }

    /**
     * 品牌添加
     * Author: CK
     */
    public function goodsBrandAdd(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::goodsBrandAdd($params);
        return $result;
    }

    /**
     * 品牌列表
     * Author: CK
     */
    public function goodsBrandList(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::goodsBrandList($params);
        return $result;
    }

    /**
     * 品牌详情
     * Author: CK
     */
    public function goodsBrandDetail(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::goodsBrandDetail($params);
        return $result;
    }

    /**
     * 品牌删除
     * Author: CK
     */
    public function goodsBrandDelete(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::goodsBrandDelete($params);
        return $result;
    }

    /**
     * 品牌编辑
     * Author: CK
     */
    public function goodsBrandEdit(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::goodsBrandEdit($params);
        return $result;
    }

    /**
     * 商品种类添加
     * Author: CK
     */
    public function goodsCategoryAdd(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::goodsCategoryAdd($params);
        return $result;
    }

    /**
     * 商品种类详情
     * Author: CK
     */
    public function goodsCategoryDetail(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::goodsCategoryDetail($params);
        return $result;
    }

    /**
     * 商品种类层级结构（下拉使用）列表
     * Author: CK
     */
    public function goodsCategoryListLevel(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::goodsCategoryListLevel($params);
        return $result;
    }

    /**
     * 商品种类树状结构列表
     * Author: CK
     */
    public function goodsCategoryListTree(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::goodsCategoryListTree($params);
        return $result;
    }

    /**
     * 商品种类修改
     * Author: CK
     */
    public function goodsCategoryEdit(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::goodsCategoryEdit($params);
        return $result;
    }

    /**
     * 商品种类删除
     * Author: CK
     */
    public function goodsCategoryDelete(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::goodsCategoryDelete($params);
        return $result;
    }

    /**
     * 商品属性添加
     * @author caohan
     */
    public function attributeAdd(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::attributeAdd($params);
        return $result;
    }

    /**
     * 商品属性编辑
     * @author caohan
     */
    public function attributeEdit(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::attributeEdit($params);
        return $result;
    }

    public function attributeDetail(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::attributeDetail($params);
        return $result;
    }

    /**
     * 商品属性删除
     * @author caohan
     */
    public function attributeDel(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::attributeDel($params);
        return $result;
    }

    /**
     * 商品属性列表
     * @author caohan
     */
    public function attributeList(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::attributeList($params);
        return $result;
    }

    /**
     * 类型添加
     * @author caohan
     */
    public function typeAdd(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::typeAdd($params);
        return $result;
    }

    /**
     * 类型编辑
     * @author caohan
     */
    public function typeEdit(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::typeEdit($params);
        return $result;
    }

    /**
     * 类型删除
     * @author caohan
     */
    public function typeDel(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::typeDel($params);
        return $result;
    }

    /**
     * 类型列表
     * @author caohan
     */
    public function typeList(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::typeList($params);
        return $result;
    }

    /**
     * 所有类型和属性列表
     * @author caohan
     */
    public function typeAllList(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::typeAllList($params);
        return $result;
    }

    public function typeDetail(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::typeDetail($params);
        return $result;
    }

    /**
     * 通过typeid查类型
     */
    public function attributeListByTypeId(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::attributeListByTypeId($params);
        return $result;
    }

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
     * 评论列表
     * @author CK
     * @param Request $request
     * @return array
     */
    public function commentListAll(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::commentListAll($params);
        return $result;
    }

    /**
     * 评论详情
     * @author CK
     * @param Request $request
     * @return array
     */
    public function commentDetail(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::commentDetail($params);
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
     * 评论回复
     * @author CK
     * @param Request $request
     * @return array
     */
    public function commentRepay(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::commentRepay($params);
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

    /**
     *删除评论（支持批量删除）
     * @author CK
     * @param Request $request
     * @return array
     */
    public function commentDelete(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::commentDelete($params);
        return $result;
    }

    /**
     *当前供应商下的商品所有评论
     * @author CK
     * @param Request $request
     * @return array
     */
    public function commentListAdmin(Request $request)
    {
        $params = $request->input();
        $result = \BackendGoodsService::commentListAdmin($params);
        return $result;
    }
}