<?php
/**
 * Created by PhpStorm.
 * User: fuyuehua
 * Date: 2017/9/19
 * Time: 14:55
 */

namespace Modules\Goods\Services;

use Modules\Backend\Models\Admin;
use Modules\Goods\Models\Goods;
use Modules\Goods\Models\GoodsAttr;
use Modules\Goods\Models\GoodsAttribute;
use Modules\Goods\Models\GoodsComment;
use Modules\Goods\Models\GoodsDesc;
use Modules\Goods\Models\GoodsProducts;
use Modules\Goods\Models\GoodsBrand;
use Modules\Goods\Models\GoodsCategory;
use Modules\Goods\Models\GoodsType;
use \DB;
use Modules\Order\Models\BackendOrder;
use Modules\Order\Models\OrderGoods;
use Modules\Order\Models\OrderInfo;
use Modules\Order\Services\BackendOrderService;
use Modules\User\Models\User;

class BackendGoodsService
{
    /**
     * 下拉框选择数据
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public function goodsSelect($params)
    {
        $params['level'] = 1;
        #品牌选择列表
        $brant_list = \BackendGoodsService::goodsBrandList($params);
        #分类选择列表
        $category_list = \BackendGoodsService::goodsCategoryListLevel($params);
        #属性选择列表
        $attr_list = \BackendGoodsService::attributeList($params);
        #类型选择列表
        $type_list = \BackendGoodsService::typeAllList($params);
        #地址选择列表
        $region_list = \RegionService::regionByLevel($params);
        if ($brant_list['code'] == 1 && $category_list['code'] == 1 && $attr_list['code'] == 1
            && $type_list['code'] == 1 && $region_list['code'] == 1
        ) {
            return ['code' => 1, 'msg' => '查询成功',
                'data' => [
                    'brant_list' => $brant_list['data']['list'],
                    'category_list' => $category_list['data']['goodsCategory_list_level'],
                    'attr_list' => $attr_list['data']['list'],
                    'type_list' => $type_list['data'],
                    'region_list' => $region_list['data'],
                ]
            ];
        } else {
            return ['code' => 10241, 'msg' => '选择数据查询失败'];
        }
    }

    /**
     * 商品添加
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public function goodsAdd($params)
    {
        $params = json_decode($params, true);
        #参数json验证
        if (empty($params)) {
            return ['code' => 90002, 'msg' => 'json格式错误'];
        }
        $validator = \Validator::make(
            $params,
            config('validator.system.goods.goods-add'),
            config('validator.system.goods.goods-key'),
            config('validator.system.goods.goods-val')
        );
        if ($validator->fails()) {
            return ['code' => 90002, 'msg' => $validator->messages()];
        }
        try {
            #开启事务
            DB::beginTransaction();
            #商品添加
            $goods_data = $params['goods_info'];
            if (Goods::goodsExist($goods_data)) {
                return ['code' => 10242, 'msg' => '商品已存在'];
            }
            $goods_data['goods_sn'] = get_sn('G');
            $goods_data['admin_id'] = get_admin_id();
            #若有货品信息则 遍历货品根据要求获取商品的参数
            if (!empty($params['product_info'])) {
                $market_price = [];
                $product_price = [];
                $product_number = [];
                foreach ($params['product_info'] as $product) {
                    $market_price[] = $product['market_price'];
                    $product_price[] = $product['product_price'];
                    $product_number[] = $product['product_number'];
                }
                $goods_data['market_price'] = min($market_price);
                $goods_data['goods_price'] = min($product_price);
                $goods_data['goods_number'] = array_sum($product_number);
            }
            #去掉图片前缀
            if (!empty($goods_data['goods_thumb'])) {
                foreach ($goods_data['goods_thumb'] as $k => $v) {
                    if (!empty($v)) {
                        $goods_data['goods_thumb'][$k] = str_replace(config('services.oss.host'), '', $v);
                    }
                }
                $goods_data['goods_thumb'] = implode('|', $goods_data['goods_thumb']);
            }
            if (!empty($goods_data['goods_img'])) {
                foreach ($goods_data['goods_img'] as $k => $v) {
                    if (!empty($v)) {
                        $goods_data['goods_img'][$k] = str_replace(config('services.oss.host'), '', $v);
                    }
                }
                $goods_data['goods_img'] = implode('|', $goods_data['goods_img']);
            }

            $goods_id = Goods::goodsAdd($goods_data);
            if ($goods_id) {
                #商品描述添加
                $good_desc_params['goods_id'] = $goods_id;
                $good_desc_params['goods_desc'] = isset($goods_data['goods_desc']) ? $goods_data['goods_desc'] : '';
                $good_desc = GoodsDesc::goodsDescAdd($good_desc_params);
                #商品详情添加
                if (!empty($good_desc) || !isset($params['goods_desc'])) {
                    if (!isset($params['product_info']) || empty($params['product_info'])) {
                        #没有货品描述  无需添加货品
                        $result = ['code' => 1, 'msg' => '商品添加成功'];
                    } else {
                        #货品添加
                        $product_params = $params['product_info'];
                        foreach ($product_params as $key => $value) {
                            $product_params[$key]['goods_id'] = $goods_id;
                            $product_params[$key]['product_name'] = $goods_data['goods_name'];
                            $product_params[$key]['admin_id'] = $goods_data['admin_id'];
                        }
                        $goods_product = GoodsProducts::goodsProductBatchAdd($product_params);
                        if ($goods_product) {
                            #货品属性添加
                            $attr_params = [];
                            foreach ($goods_product as $k1 => $v1) {
                                foreach ($v1['attr'] as $k2 => $v2) {
                                    $data['goods_id'] = $v1['goods_id'];
                                    $data['product_id'] = $v1['product_id'];
                                    $data['attr_name'] = $v2['attr_name'];
                                    $data['attr_value'] = $v2['attr_value'];
                                    $data['created_at'] = date('Y-m-d H:i:s');
                                    $attr_params[] = $data;
                                }
                            }
                            $goods_attr = GoodsAttr::goodsAttrAdd($attr_params);
                            if ($goods_attr) {
                                $result = ['code' => 1, 'msg' => '商品添加成功'];
                            } else {
                                $result = ['code' => 10243, 'msg' => '货品属性添加失败'];
                            }
                        } else {
                            $result = ['code' => 10244, 'msg' => '货品添加失败'];
                        }
                    }
                } else {
                    $result = ['code' => 10245, 'msg' => '商品描述添加失败'];
                }
            } else {
                $result = ['code' => 10246, 'msg' => '商品主数据添加失败'];
            }
            #结束事务
            if ($result['code'] == 1) {
                DB::commit();
            } else {
                DB::rollBack();
            }
            return $result;
        } catch (\Exception $exception) {
            DB::rollBack();
            return ['code' => 10247, 'msg' => '数据写入失败', 'data' => $exception->getMessage()];
        }
    }

    /**
     * 商品修改
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public function goodsEdit($params)
    {
        $params = json_decode($params['goods'], true);
        if (empty($params)) {
            return ['code' => 90002, 'msg' => 'json格式错误'];
        }
        $validator = \Validator::make(
            $params,
            config('validator.system.goods.goods-edit'),
            config('validator.system.goods.goods-key'),
            config('validator.system.goods.goods-val')
        );
        if ($validator->fails()) {
            return ['code' => 90002, 'msg' => $validator->messages()];
        }
        try {
            DB::beginTransaction();
            $goods_data = $params['goods_info'];

            #货品初始化参数
            $product_add = [];
            $product_edit = [];
            $product_list = GoodsProducts::goodsProductList($goods_data)['goods_product'];
            $product_list = json_decode(json_encode($product_list), true);
            $current_product = array_column($product_list, 'product_id');

            #商品主信息修改
            if (!empty($params['product_info'])) {
                $market_price = [];
                $product_price = [];
                $product_number = [];
                $admin_id = get_admin_id();
                foreach ($params['product_info'] as $product) {
                    #商品信息
                    $market_price[] = $product['market_price'];
                    $product_price[] = $product['product_price'];
                    $product_number[] = $product['product_number'];
                    #货品比较分类
                    if ($product['product_id'] == 0) {
                        #货品添加
                        $product['goods_id'] = $goods_data['goods_id'];
                        $product['product_name'] = $goods_data['goods_name'];
                        $product['admin_id'] = $admin_id;
                        $product_add[] = $product;
                    } elseif (in_array($product['product_id'], $current_product)) {
                        #货品修改
                        $product_edit[$product['product_id']] = $product;
                    }
                }
                #删除货品id
                $product_delete = array_values(array_diff($current_product, array_keys($product_edit)));
                $goods_data['market_price'] = min($market_price);
                $goods_data['goods_price'] = min($product_price);
                $goods_data['goods_number'] = array_sum($product_number);
            } else {
                #删除货品id
                $product_delete = $current_product;
            }
            #去掉图片前缀
            if (!empty($goods_data['goods_thumb'])) {
                foreach ($goods_data['goods_thumb'] as $k => $v) {
                    if (!empty($v)) {
                        $goods_data['goods_thumb'][$k] = str_replace(config('services.oss.host'), '', $v);
                    }
                }
                $goods_data['goods_thumb'] = implode('|', $goods_data['goods_thumb']);
            }
            if (!empty($goods_data['goods_img'])) {
                foreach ($goods_data['goods_img'] as $k => $v) {
                    if (!empty($v)) {
                        $goods_data['goods_img'][$k] = str_replace(config('services.oss.host'), '', $v);
                    }
                }
                $goods_data['goods_img'] = implode('|', $goods_data['goods_img']);
            }
            $goods = Goods::goodsEdit($goods_data);

            #商品描述修改
            if (isset($goods_data['goods_desc'])) {
                GoodsDesc::goodsDescEdit($goods_data);
            }
            #货品新增
            if (!empty($product_add)) {
                foreach ($product_add as $item) {
                    \BackendGoodsService::goodsProductAdd($item);
                }
            }
            #货品删除
            if (!empty($product_delete)) {
                $product_delete = ['product_id' => $product_delete];
                \BackendGoodsService::goodsProductDelete($product_delete);
            }
            #货品修改
            if (!empty($product_edit)) {
                foreach ($product_edit as $key => $item) {
                    $item['goods_id'] = $goods_data['goods_id'];
                    \BackendGoodsService::goodsProductEdit($item);
                }
            }

            if ($goods) {
                DB::commit();
                return ['code' => 1, 'msg' => '商品修改成功'];
            } else {
                DB::rollBack();
                return ['code' => 10248, 'msg' => '商品修改失败'];
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return ['code' => 10247, 'msg' => '数据写入失败', 'data' => $exception];
        }
    }

    /**
     * 商品详情
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public function goodsDetail($params)
    {
        if (empty($params['goods_id'])) {
            return ['code' => 90001, 'msg' => 'goods_id不存在'];
        }
        #商品主信息
        $goods = Goods::goodsDetail($params['goods_id']);
        $goods_thumb = explode('|', $goods['goods_thumb']);
        foreach ($goods_thumb as $k => $v) {
            $goods_thumb[$k] = config('services.oss.host') . $v;
        }
        $goods['goods_thumb'] = $goods_thumb;

        $goods_img = explode('|', $goods['goods_img']);
        foreach ($goods_img as $k => $v) {
            $goods_img[$k] = config('services.oss.host') . $v;
        }
        $goods['goods_img'] = $goods_img;

        $result['goods_detail']['goods_info'] = $goods;
        #货品列表信息
        $goods_product_list = \BackendGoodsService::goodsProductList($params);
        $result['goods_detail']['goods_products'] = empty($goods_product_list) || $goods_product_list['code'] != 1 ? [] : $goods_product_list['data']['goods_product'];
        #商品描述信息
        $goods_desc = GoodsDesc::goodsDescDetail($params)['goods_desc'];
        $result['goods_detail']['goods_info']['goods_desc'] = empty($goods_desc) ? '' : $goods_desc;
        #选择框信息
        $goods_select = \BackendGoodsService::goodsSelect($params);
        if ($goods_select['code'] == 1) {
            $result['goods_select'] = $goods_select['data'];
        }
        return ['code' => 1, 'msg' => '获取成功', 'data' => $result];
    }

    /**
     * 商品删除
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public function goodsDelete($params)
    {
        if (empty($params['goods_id'])) {
            return ['code' => 90001, 'msg' => 'goods_id不存在'];
        }
        try {
            $data['goods_id'] = explode(',', $params['goods_id']);
            $goods = Goods::goodsDelete($data);
            $goods_desc = GoodsDesc::goodsDescDelete($data);
            $goods_product = \BackendGoodsService::goodsProductDelete($data);
            if ($goods && $goods_desc && $goods_product['code'] == 1) {
                return ['code' => 1, 'msg' => '商品删除成功'];
            } else {
                return ['code' => 10249, 'msg' => '商品删除失败'];
            }
        } catch (\Exception $exception) {
            return ['code' => 10247, 'msg' => '数据写入失败'];
        }
    }

    /**
     * 商品列表
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public function goodsList($params)
    {
        $results = Goods::goodsList($params);
        if ($results) {
            return ['code' => 1, 'msg' => '获取成功', 'data' => $results];
        } else {
            return ['code' => 10250, 'msg' => '商品获取失败'];
        }
    }

    /**
     * 商品状态修改
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public function goodsStatusChange($params)
    {
        if (empty($params['goods_id'])) {
            return ['code' => 90001, 'msg' => 'goods_id不存在'];
        }
        $params['goods_id'] = explode(',', $params['goods_id']);
        $results = Goods::goodsStatusChange($params);
        if ($results) {
            return ['code' => 1, 'msg' => '状态修改成功'];
        } else {
            return ['code' => 10251, 'msg' => '状态修改失败'];
        }
    }

    /**
     * 货品添加
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public function goodsProductAdd($params)
    {
        try {
            $params['admin_id'] = get_admin_id();
            $goods_product = GoodsProducts::goodsProductAdd($params);
            if ($goods_product) {
                #货品属性添加
                $attr_params = [];
                $params['attr'] = is_array($params['attr']) ? $params['attr'] : json_decode($params['attr'], true);
                foreach ($params['attr'] as $k => $v) {
                    $data['goods_id'] = $params['goods_id'];
                    $data['product_id'] = $goods_product->product_id;
                    $data['attr_name'] = $v['attr_name'];
                    $data['attr_value'] = $v['attr_value'];
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $attr_params[] = $data;
                }
                $goods_attr = GoodsAttr::goodsAttrAdd($attr_params);
                if ($goods_attr) {
                    return ['code' => 1, 'msg' => '货品添加成功'];
                } else {
                    return ['code' => 10243, 'msg' => '货品属性添加失败'];
                }
            } else {
                return ['code' => 10244, 'msg' => '货品添加失败'];
            }
        } catch (\Exception $exception) {
            return ['code' => 10247, 'msg' => '数据写入失败'];
        }
    }

    /**
     * 货品修改
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public function goodsProductEdit($params)
    {
        if (empty($params['product_id'])) {
            return ['code' => 90001, 'msg' => 'product_id不存在'];
        }
        $attr_add = [];
        $attr_edit = [];
        $attr_delete = [];
        $product_attr = is_array($params['attr']) ? $params['attr'] : json_decode($params['attr'], true);
        $goods_product = GoodsProducts::goodsProductEdit($params);
        $attr_list = GoodsAttr::goodsAttrList([$params['product_id']]);
        $current_attr = array_column($attr_list, 'attr_id');
        #属性分类
        if (!empty($product_attr)) {
            $isset_key = [];
            $db_isset_key = [];
            foreach ($product_attr as $key => $item) {
                foreach ($attr_list as $key2 => $attr) {
                    #匹配到的属性
                    if ($attr['product_id'] == $params['product_id'] &&
                        $attr['attr_name'] == $item['attr_name']
                    ) {
                        #数据中需要修改的属性
                        if ($attr['attr_value'] != $item['attr_value']) {
                            $item['attr_id'] = $attr['attr_id'];
                            $attr_edit[$attr['attr_id']] = $item;
                        }
                        $isset_key[] = $key;
                        $db_isset_key[] = $key2;
                        continue 2;
                    }
                }
            }
            #筛选出需要添加的属性
            foreach ($product_attr as $key => $item) {
                if (!in_array($key, $isset_key)) {
                    $data['product_id'] = $params['product_id'];
                    $data['goods_id'] = $params['goods_id'];
                    $data['attr_name'] = $item['attr_name'];
                    $data['attr_value'] = $item['attr_value'];
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $attr_add[] = $data;
                }
            }
            #筛选出需要添加的属性
            foreach ($attr_list as $key => $item) {
                if (!in_array($key, $db_isset_key)) {
                    $attr_delete[] = $item['attr_id'];
                }
            }
        } else {
            $attr_delete = $current_attr;
        }
        #属性添加
        if (!empty($attr_add)) {
            GoodsAttr::goodsAttrAdd($attr_add);
        }
        #属性删除
        if (!empty($attr_delete)) {
            $attr_delete = ['attr_id' => $attr_delete];
            GoodsAttr::goodsAttrDelete($attr_delete);
        }
        #属性修改
        if (!empty($attr_edit)) {
            foreach ($attr_edit as $item) {
                GoodsAttr::goodsAttrEdit($item);
            }
        }
        if ($goods_product) {
            return ['code' => 1, 'msg' => '货品修改成功'];
        } else {
            return ['code' => 10253, 'msg' => '货品修改失败'];
        }
    }

    /**
     * 货品列表
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public function goodsProductList($params)
    {
        $goods_product = GoodsProducts::goodsProductList($params)['goods_product'];
        #获取所有product_id
        $goods_product = json_decode(json_encode($goods_product), true);
        $goods_product_ids = array_column($goods_product, 'product_id');
        $goods_attrs = GoodsAttr::goodsAttrList($goods_product_ids);
        #匹配相应的货品属性
        if (!empty($goods_product)) {
            foreach ($goods_product as $k1 => $v1) {
                foreach ($goods_attrs as $k2 => $v2) {
                    if ($v2['product_id'] == $v1['product_id']) {
                        $goods_product[$k1]['attr'][] = $v2;
                    }
                }
            }
        }
        if ($goods_product) {
            $result = ['goods_product' => $goods_product];
            return ['code' => 1, 'msg' => '获取成功', 'data' => $result];
        } else {
            return ['code' => 10254, 'msg' => '货品列表获取失败'];
        }
    }

    /**
     * 货品删除
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public function goodsProductDelete($params)
    {
        if (isset($params['product_id']) && $params['product_id'] && !is_array($params['product_id'])) {
            $params['product_id'] = explode(',', $params['product_id']);
        }
        if (isset($params['goods_id']) && $params['goods_id'] && !is_array($params['goods_id'])) {
            $params['goods_id'] = explode(',', $params['goods_id']);
        }
        if (isset($params['product_id']) && !isset($params['goods_id'])) {
            #获取所有货品对应的商品id
            $params['all'] = true;
            $goods_list = GoodsProducts::goodsProductList($params);
            $goods_ids = array_column($goods_list, 'goods_id');
        }
        #删除货品
        $goods_product = GoodsProducts::goodsProductDelete($params);
        #删除货品属性
        $goods_attr = GoodsAttr::goodsAttrDelete($params);
        #删除无货品的商品
        if (isset($params['product_id']) && !isset($params['goods_id']) && !empty($goods_ids)) {
            #获取所有货品对应的商品id
            $data['all'] = true;
            $data['goods_id'] = $goods_ids;
            $new_goods_list = GoodsProducts::goodsProductList($data);
            $new_goods_ids = array_column($new_goods_list, 'goods_id');
            $deleted_goods_ids = array_values(array_diff($goods_ids, $new_goods_ids));
            $goods_data = ['goods_id' => $deleted_goods_ids];
            Goods::goodsDelete($goods_data);
        }

        if ($goods_product && $goods_attr) {
            return ['code' => 1, 'msg' => '删除成功'];
        } else {
            return ['code' => 10255, 'msg' => '货品删除失败'];
        }
    }

    /**
     * 货品状态修改
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public function goodsProductStatusChange($params)
    {
        if (empty($params['product_id'])) {
            return ['code' => 90001, 'msg' => 'product_id不存在'];
        }
        $params['product_id'] = explode(',', $params['product_id']);
        $results = GoodsProducts::goodsProductStatusChange($params);
        if ($results) {
            return ['code' => 1, 'msg' => '货品状态修改成功'];
        } else {
            return ['code' => 10256, 'msg' => '货品状态修改失败'];
        }
    }

    /**
     * 品牌添加
     * Author: CK
     * @param string brand_name 品牌名称
     * @param string brand_thumb 品牌缩略图
     * @param string brand_desc 品牌描述
     * @param init is_show      是否展示（1是 0否）
     * @return array
     */
    public function goodsBrandAdd($params)
    {
        $result = ['code' => 10220, 'msg' => "添加失败"];
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.goodsbrand.goodsbrand-add'),
            \Config::get('validator.system.goodsbrand.goodsbrand-key'),
            \Config::get('validator.system.goodsbrand.goodsbrand-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $addInfo = GoodsBrand::goodsBrandAdd($params);
        if ($addInfo) {
            $result['code'] = 1;
            $result['msg'] = "添加成功";
        }
        return $result;
    }

    /**
     * 品牌列表
     * Author: CK
     * @param $params ['limit'] 一页的数据
     * @param $params ['page'] 当前页
     * @param $params ['keyword'] 查询关键字 可为空
     * @return array
     */
    public function goodsBrandList($params)
    {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $data['list'] = GoodsBrand::goodsBrandList($params);
        $data['page'] = $params['page'];
        $data['total'] = GoodsBrand::goodsBrandCount($params);
        return ['code' => 1, 'data' => $data];
    }

    /**
     * 品牌详情
     * Author: CK
     * @param $params ['brand_id'] 品牌ID
     * @return array
     */
    public function goodsBrandDetail($params)
    {
        if (!isset($params['brand_id'])) {
            return ['code' => 90002, 'msg' => '品牌ID不能为空'];
        }
        $data['Brand_Info'] = GoodsBrand::goodsBrandDetail($params);
        return ['code' => 1, 'data' => $data];
    }

    /**
     * 品牌删除
     * Author: CK
     * @param $params ['brand_id'] 品牌ID
     * @return array
     */
    public function goodsBrandDelete($params)
    {
        $result = ['code' => 10221, 'msg' => "删除失败"];
        if (!isset($params['brand_id'])) {
            return ['code' => 90002, 'msg' => '品牌ID不能为空'];
        }
        $brand_id_arr = explode(',', $params['brand_id']);
        $DeleteInfo = GoodsBrand::goodsBrandDelete($brand_id_arr);
        if ($DeleteInfo) {
            $result['code'] = 1;
            $result['msg'] = "删除成功";
        }
        return $result;
    }

    /**
     * 品牌编辑
     * Author: CK
     * @param $params brand_id 品牌ID
     * @param string brand_name 品牌名称
     * @param string brand_thumb 品牌缩略图
     * @param string brand_desc 品牌描述
     * @param init is_show      是否展示（1是 0否）
     * @return array
     * @return array
     */
    public function goodsBrandEdit($params)
    {
        $result = ['code' => 10222, 'msg' => "更新失败"];
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.goodsbrand.goodsbrand-edit'),
            \Config::get('validator.system.goodsbrand.goodsbrand-key'),
            \Config::get('validator.system.goodsbrand.goodsbrand-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $had = GoodsBrand::find($params['brand_id']);
        if (is_null($had)) {
            return $result;
        }
        $editInfo = GoodsBrand::goodsBrandEdit($params);
        if ($editInfo) {
            $result['code'] = 1;
            $result['msg'] = "更新成功";
        }
        return $result;
    }

    /**
     * 商品种类添加
     * Author: CK
     * @param string pid            父级ID
     * @param string sort_order     排序ID
     * @param string cat_name       分类名称
     * @param string cat_desc       分类描述
     * @param string cat_thumb      分类缩略图
     * @param string keywords       分类关键字
     * @param   init is_show        是否显示（0否 1是）
     * @param   init is_show_nav    是否导航显示（0否 1是）
     * @return array
     */
    public function goodsCategoryAdd($params)
    {
        $result = ['code' => 10220, 'msg' => "添加失败"];
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.goodscategory.goodscategory-add'),
            \Config::get('validator.system.goodscategory.goodscategory-key'),
            \Config::get('validator.system.goodscategory.goodscategory-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $addInfo = GoodsCategory::goodsCategoryAdd($params);
        if ($addInfo) {
            $result['code'] = 1;
            $result['msg'] = "添加成功";
        }
        return $result;
    }

    /**
     * 商品种类详情
     * Author: CK
     * @param $params ['brand_id'] 品牌ID
     * @return array
     */
    public function goodsCategoryDetail($params)
    {
        if (!isset($params['cat_id'])) {
            return ['code' => 90002, 'msg' => '商品种类ID不能为空'];
        }
        $data['Category_Info'] = GoodsCategory::goodsCategoryDetail($params);
        return ['code' => 1, 'data' => $data];
    }

    /**
     * 商品列表层级结构（下拉使用）
     * Author: CK
     * @return array
     */
    public function goodsCategoryListLevel($params)
    {
        $result['code'] = 1;
        $ListALL = GoodsCategory::goodsCategoryListAll($params);
        $newarr = $this->GetLevel($ListALL, 0, 0);
        $result['data']['total'] = count($newarr);
        $result['data']['goodsCategory_list_level'] = $newarr;
        return $result;
    }

    #形成层级结构
    private function GetLevel($arr, $pid, $step)
    {
        global $level;
        foreach ($arr as $key => $val) {
            if ($val['pid'] == $pid) {
                $flg = str_repeat('-', $step + 1);
                $val['cat_name'] = $flg . $val['cat_name'];
                $level[] = $val;
                $this->GetLevel($arr, $val['cat_id'], $step + 1);
            }
        }
        return $level;
    }

    /**
     * 商品列表树状结构
     * Author: CK
     * @return array
     */
    public function goodsCategoryListTree($params)
    {
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $all = GoodsCategory::goodsCategoryListTree($params);
        $data['total'] = GoodsCategory::goodsCategoryCount($params);
        $data['goodsCategory_list_tree'] = $this->GetTree($all);
        return ['code' => 1, 'data' => $data];
    }

    #形成树状格式
    public function GetTree($tree, $rootId = 0, $level = 1)
    {
        $return = array();
        foreach ($tree as $leaf) {
            if ($leaf['pid'] == $rootId) {
                $leaf["level"] = $level;
                foreach ($tree as $subleaf) {
                    if ($subleaf['pid'] == $leaf['cat_id']) {
                        $leaf['children'] = $this->GetTree($tree, $leaf['cat_id'], $level + 1);
                        break;
                    }
                }
                $return[] = $leaf;
            }
        }
        return $return;
    }

    /**
     * 商品种类修改
     * Author: CK
     * @param string cat_id         种类ID
     * @param string pid            父级ID
     * @param string sort_order     排序ID
     * @param string cat_name       分类名称
     * @param string cat_desc       分类描述
     * @param string cat_thumb      分类缩略图
     * @param string keywords       分类关键字
     * @param   init is_show        是否显示（0否 1是）
     * @param   init is_show_nav    是否导航显示（0否 1是）
     * @return array
     *
     * @author caohan
     */
    public function goodsCategoryEdit($params)
    {
        $result = ['code' => 10222, 'msg' => "更新失败"];
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.goodscategory.goodscategory-edit'),
            \Config::get('validator.system.goodscategory.goodscategory-key'),
            \Config::get('validator.system.goodscategory.goodscategory-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $had = GoodsCategory::find($params['cat_id']);
        if (is_null($had)) {
            return $result;
        }
        $editInfo = GoodsCategory::goodsCategoryEdit($params);
        if ($editInfo) {
            $result['code'] = 1;
            $result['msg'] = "更新成功";
        }
        return $result;
    }

    /**
     * 商品种类删除
     * Author: CK
     * @param string cat_id     种类ID
     * @return array
     *
     * @author caohan
     */
    public function goodsCategoryDelete($params)
    {
        $result = ['code' => 10221, 'msg' => "删除失败"];
        if (!isset($params['cat_id']))
            return ['code' => 10223, 'msg' => '请输入分类ID'];
        $cat_id_arr = explode(',', $params['cat_id']);
        foreach ($cat_id_arr as $v) {
            $res = GoodsCategory::where('pid', $v)->get();
            if (count($res) > 0) {
                $son = array();
                foreach ($res as $n => $m) {
                    $son[] = $m['cat_id'];
                }
                $same = array_intersect($son, $cat_id_arr);
                if (count($same) == 0) {
                    return ['code' => 10224, 'msg' => '存在子分类,删除失败'];
                }
                if (count($same) > 0) {
                    if (count($same) !== count($son)) {
                        return ['code' => 10225, 'msg' => '存在其他子分类,删除失败'];
                    }
                }
            }
        }
        $res = GoodsCategory::goodsCategoryDelete($cat_id_arr);
        if ($res) {
            $result['code'] = 1;
            $result['msg'] = "删除成功";
        }
        return $result;
    }


    /**
     * 添加商品属性
     * @params attr_name   string 属性名称
     * @params type_id   int 对应类型id
     * @params sort(选填)  int 排序用
     * @return mixed
     *
     * @author caohan
     */
    public function attributeAdd($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.goodsattribute.attribute-add'),
            \Config::get('validator.system.goodsattribute.attribute-key'),
            \Config::get('validator.system.goodsattribute.attribute-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }

        //查询是否存在
        $find = GoodsAttribute::attributeFindSame2($params);
        if (!is_null($find))
            return ['code' => 90002, 'msg' => '商品属性已经存在'];

        $res = ['code' => 10230, 'msg' => '商品属性添加失败'];
        $add = GoodsAttribute::attributeAdd($params);
        if ($add)
            $res = ['code' => 1, 'msg' => '商品属性添加成功'];
        return $res;
    }

    /**
     * 编辑商品属性
     * @params attr_id
     * @params attr_name   string 属性名称
     * @params type_id   int 对应类型id
     * @params sort(选填)  int 排序用
     * @return mixed
     *
     * @author caohan
     */
    public function attributeEdit($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.goodsattribute.attribute-edit'),
            \Config::get('validator.system.goodsattribute.attribute-key'),
            \Config::get('validator.system.goodsattribute.attribute-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $res = ['code' => 10231, 'msg' => '商品属性修改失败'];
        //1.先查找是否存在一样的
        $find = GoodsAttribute::attributeFindSame($params);
        if (!is_null($find)) {
            return ['code' => 1, 'msg' => '无修改'];
        }
        //2.查询是否有一样的type_name + attr_name
        $find2 = GoodsAttribute::attributeFindSame2($params);
        if (!is_null($find2)) {
            return ['code' => 90002, 'msg' => '不能与其他商品属性相同'];
        }
        $edit = GoodsAttribute::attributeEdit($params);
        if ($edit)
            $res = ['code' => 1, 'msg' => '商品属性修改成功'];
        return $res;
    }

    public function attributeDetail($params)
    {
        $res = ['code' => 1, 'msg' => '查询成功'];
        $data['attr_info'] = GoodsAttribute::attributeDetail($params);
        $res['data'] = $data;
        return $res;
    }

    /**
     * 删除商品属性
     * @params attr_id
     *
     * @author caohan
     */
    public function attributeDel($params)
    {
        if (!isset($params['attr_id']))
            return ['code' => 10237, 'msg' => '请输入商品户型ID'];
        $res = ['code' => 10232, 'msg' => '商品属性删除失败'];
        $attr_id_arr = explode(',', $params['attr_id']);

        $del = GoodsAttribute::attributeDel($attr_id_arr);
        if ($del > 0)
            $res = ['code' => 1, 'msg' => '商品属性删除成功'];
        return $res;
    }

    /**
     * 商品属性列表   下拉用
     * @param type_id int  对应的type_id 的所有
     * @return array
     *
     * @author caohan
     */
    public function attributeList($params)
    {
        $res = ['code' => 1, 'msg' => '查询成功'];
        $data['list'] = GoodsAttribute::attributeList($params);
        $res['data'] = $data;
        return $res;
    }


    /**
     * 添加类型
     * @params type_name   string 类型名称
     * @params sort(选填)  int 排序用
     * @return mixed
     *
     * @author caohan
     */
    public function typeAdd($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.goodstype.type-add'),
            \Config::get('validator.system.goodstype.type-key'),
            \Config::get('validator.system.goodstype.type-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        //查询类型是否存在
        $condition = ['type_name' => $params['type_name']];
        $type_find = GoodsType::typeFindSame($condition);
        if (!is_null($type_find))
            return ['code' => '90002', 'msg' => '类型已存在'];

        $res = ['code' => 10233, 'msg' => '类型添加失败'];
        $add = GoodsType::typeAdd($params);
        if ($add)
            $res = ['code' => 1, 'msg' => '类型添加成功'];
        return $res;
    }

    /**
     * 编辑类型
     * @params type_id 类型id
     * @params type_name   string 属性名称
     * @params sort(选填)  int 排序用
     * @return mixed
     *
     * @author caohan
     */
    public function typeEdit($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.goodstype.type-edit'),
            \Config::get('validator.system.goodstype.type-key'),
            \Config::get('validator.system.goodstype.type-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }

        //查询类型是否存在
        $condition = ['type_name' => $params['type_name']];
        $type_find = GoodsType::typeFindSame($condition);
        if (!is_null($type_find))
            return ['code' => '90002', 'msg' => '类型已存在'];


        $res = ['code' => 10234, 'msg' => '类型修改失败'];
        $edit = GoodsType::typeEdit($params);
        if ($edit)
            $res = ['code' => 1, 'msg' => '类型修改成功'];
        return $res;
    }

    /**
     * 删除类型
     * @params type_id
     *
     * @author caohan
     */
    public function typeDel($params)
    {
        if (!isset($params['type_id']))
            return ['code' => 10236, 'msg' => '请输入类型ID'];
        $res = ['code' => 10235, 'msg' => '类型删除失败'];
        $attr_id_arr = explode(',', $params['type_id']);

        $del = GoodsType::typeDel($attr_id_arr);
        if ($del > 0)
            $res = ['code' => 1, 'msg' => '类型删除成功'];
        return $res;
    }

    public function typeList($params)
    {
        $res = ['code' => 1, 'msg' => '查询成功'];
        $data['list'] = GoodsType::typeList($params);
        $res['data'] = $data;
        return $res;
    }

    public function typeDetail($params)
    {
        $res = ['code' => 1, 'msg' => '查询成功'];
        $data['type_info'] = GoodsType::typeDetail($params);
        $res['data'] = $data;
        return $res;
    }

    public function typeAllList($params)
    {
        $res = ['code' => 1, 'msg' => '查询成功'];
        $list = GoodsType::typeList($params);
        foreach ($list as $key => $value) {
            $attr_list = GoodsAttribute::attributeListByTypeId($value['type_id']);
            $list[$key]['attr_list'] = $attr_list;
        }
        $res['data'] = $list;
        return $res;
    }

    public function attributeListByTypeId($parmas)
    {
        $res = ['code' => 1, 'msg' => '查询成功'];
        $attr_list = GoodsAttribute::attributeListByTypeId($parmas['type_id']);
        $res['data']['list'] = $attr_list;
        return $res;
    }

    /**
     * 添加评论
     * @params comment_id  int     评论ID
     * @params goods_id    int     商品ID
     * @params product_id  int     货品ID
     * @params user_id     int     用户ID
     * @params comment_type  int   评论类型
     * @params comment_star  int   评论星级
     * @params comment_pics  string   评论图片
     * @params comment_desc  string   评论内容
     * @params comment_extra_desc  string   追评内容
     * @params comment_repay  string   评论回复
     * @return mixed
     *
     * @author CK
     */
    public function commentAdd($params)
    {
        $params = json_decode($params['data'], true);
        $result = ['code' => 10280, 'msg' => "添加失败"];
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.goodscomment.goodscomment-add'),
            \Config::get('validator.system.goodscomment.goodscomment-key'),
            \Config::get('validator.system.goodscomment.goodscomment-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        #订单存在
        $orderExist = OrderGoods::where('goods_key', '=', $params['goods_key'])->first();
        #商品存在
        $goodExist = Goods::where('goods_id', '=', $orderExist['goods_id'])->first();
        #用户存在
        $UserExist = User::where('user_id', '=', $params['user_id'])->first();
        #货品存在
        $productExist = GoodsProducts::where('product_id', '=', $orderExist['product_id'])->first();
        if (is_null($goodExist) || is_null($productExist) || is_null($UserExist) || is_null($orderExist)) {
            return $result;
        }
        #订单号与user_id 同步
        $order = OrderInfo::where('order_sn', '=', $params['order_sn'])->first();
//        return $order;
        if ($order['user_id'] != $params['user_id']) {
            return ['code' => 10284, 'msg' => "当前用户没有此订单"];
        }
        #传入了comment_pics
        if (isset($params['comment_pics'])) {
            if (count($params['comment_pics']) > 5) {
                return ['code' => 10281, 'msg' => "图片不能超过五张"];
            } else {
                $params['comment_pics'] = implode("|", $params['comment_pics']);
            }
        }
        unset($params['s']);
        $params['goods_id'] = $orderExist['goods_id'];
        $params['product_id'] = $orderExist['product_id'];
        $addInfo = GoodsComment::commentAdd($params);
        if ($addInfo) {
            #添加成功 重新计算goods表商品评价平均分
            $comment_star = GoodsComment::where('goods_id', $params['goods_id'])->get(array('comment_star'));
            $count = round($comment_star->avg('comment_star'),2);
            $avg = Goods::find($params['goods_id']);
            $avg->comment_star = $count;
            $avg->update();
            $result['code'] = 1;
            $result['msg'] = "添加成功";
        }
        return $result;
    }

    /**
     * 评论列表
     * Author: CK
     * @param $params ['limit'] 一页的数据
     * @param $params ['page'] 当前页
     * @param $params ['keyword'] 查询关键字 可为空
     * @return array
     */
    public function commentListAll($params)
    {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $data['list'] = GoodsComment::commentListAll($params);
        foreach ($data['list'] as $k => $v) {
            $order = GoodsComment::orderDeatil($v['goods_key']);
            $data['list'][$k]['order_goods_detail'] = $order;
        }
        $data['page'] = $params['page'];
        $data['total'] = GoodsComment::commentCount($params);
        foreach ($data['list'] as $k => $v) {
            if (!is_null($v['comment_pics'])) {
                $v['comment_pics'] = explode("|", $v['comment_pics']);
            }
        }
        return ['code' => 1, 'data' => $data];
    }

    /**
     * 评论详情
     * @params comment_id  int     评论ID
     * @params goods_id    int     商品ID
     * @params product_id  int     货品ID
     * @params user_id     int     用户ID
     * @params comment_type  int   评论类型
     * @params comment_star  int   评论星级
     * @params comment_pics  string   评论图片
     * @params comment_desc  string   评论内容
     * @params comment_extra_desc  string   追评内容
     * @params comment_repay  string   评论回复
     * @return mixed
     *
     * @author CK
     */
    public function commentDetail($params)
    {
        if (!isset($params['comment_id'])) {
            return ['code' => 90002, 'msg' => '评论ID不能为空'];
        }
        $commentExist = GoodsComment::where('comment_id', '=', $params['comment_id'])->first();
        if (is_null($commentExist)) {
            return ['code' => 90002, 'data' => '评论不存在'];
        }
        $data = GoodsComment::commentDetail($params);
        $real_name = User::select('real_name')->where('user_id', '=', $data['user_id'])->first();
        $goods_name = Goods::select('goods_name')->where('goods_id', '=', $data['goods_id'])->first();
        $product_name = GoodsProducts::select('product_name')->where('product_id', '=', $data['product_id'])->first();
        $admin_name = Admin::select('admin_name')->where('admin_id', '=', $data['admin_id'])->first();
        $data['user_real_name'] = $real_name['real_name'];
        $data['goods_name'] = $goods_name['goods_name'];
        $data['product_name'] = $product_name['product_name'];
        $data['admin_name'] = $admin_name['admin_name'];
        if (!is_null($data['comment_pics'])) {
            $data['comment_pics'] = explode("|", $data['comment_pics']);
        }
        return ['code' => 1, 'data' => $data];
    }

    /**
     * 追加评论
     * @params user_id     int     用户ID
     * @params comment_id  int     评论ID
     * @params comment_extra_desc   追评价内容
     * @return mixed
     *
     * @author CK
     */
    public function commentEdit($params)
    {
        if (!isset($params['comment_id'])) {
            return ['code' => 90002, 'msg' => '评论ID不能为空'];
        }
        if (!isset($params['user_id'])) {
            return ['code' => 90002, 'msg' => '用户ID不能为空'];
        }
        if (!isset($params['comment_extra_desc'])) {
            return ['code' => 90002, 'msg' => '追加评论内容不能为空'];
        }
        #评论存在
        $commentExist = GoodsComment::where('comment_id', '=', $params['comment_id'])->first();
        if (is_null($commentExist)) {
            return ['code' => 10282, 'msg' => '评论不存在'];
        }
        #评论user_id与评论人相同
        if ($commentExist['user_id'] != $params['user_id']) {
            return ['code' => 10283, 'msg' => '评论人ID不同步'];
        }
        $data = GoodsComment::commentEdit($params);
        if ($data) {
            $result['code'] = 1;
            $result['msg'] = "追加评论成功";
            return $result;
        }
    }

    /**
     * 回复评论
     * @params comment_id  int     评论ID
     * @params comment_repay       回复内容
     * @params admin_id            供应商ID
     * @return mixed
     *
     * @author CK
     */
    public function commentRepay($params)
    {
        if (!isset($params['comment_id'])) {
            return ['code' => 90002, 'msg' => '评论ID不能为空'];
        }
        if (!isset($params['admin_id'])) {
            return ['code' => 90002, 'msg' => '供应商ID不能为空'];
        }
        if (!isset($params['comment_repay'])) {
            return ['code' => 90002, 'msg' => '回复内容不能为空'];
        }
        $adminExist = Admin::where('admin_id', '=', $params['admin_id'])->first();
        if (is_null($adminExist)) {
            return ['code' => 10286, 'msg' => '此供应商不存在'];
        }
        $data = GoodsComment::commentRepay($params);
        if ($data) {
            $result['code'] = 1;
            $result['msg'] = "回复评论成功";
            return $result;
        }
    }

    /**
     * 查看当前商品的所有评论
     * @params goods_id  商品ID
     * @return mixed
     *
     * @author CK
     */
    public function commentListProduct($params)
    {
        if (!isset($params['goods_id'])) {
            return ['code' => 90002, 'msg' => '商品不能为空'];
        }
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['star'] = isset($params['star']) ? $params['star'] : null;
        $params['is_pics'] = isset($params['is_pics']) ? $params['is_pics'] : null;
        $data['list'] = GoodsComment::commentListProduct($params);
        foreach ($data['list'] as $k => $v) {
            $order = GoodsComment::orderDeatil($v['goods_key']);
            $data['list'][$k]['order_goods_detail'] = $order;
        }
        $data['page'] = $params['page'];
        $data['total'] = GoodsComment::commentListProductCount($params);
        foreach ($data['list'] as $k => $v) {
            if (!is_null($v['comment_pics'])) {
                $v['comment_pics'] = explode("|", $v['comment_pics']);
            }
            $user_name=User::where('user_id','=',$v['user_id'])->first();
            $v['credit_point']=$user_name['credit_point'];
            $v['user_name']=$user_name['real_name'];

            $admin_name=Admin::where('admin_id','=',$v['admin_id'])->first();
            $v['admin_name']=$admin_name['admin_name'];

        }
        return ['code' => 1, 'data' => $data];
    }

    /**
     *删除评论（支持批量删除）
     * @author CK
     */
    public function commentDelete($params)
    {
        if (!isset($params['comment_id'])) {
            return ['code' => 90002, 'msg' => '评论ID不能为空'];
        }
        $res = ['code' => 90002, 'msg' => '删除失败'];
        $comment_id = explode(',', $params['comment_id']);
        $data= GoodsComment::find($comment_id);
        $del = GoodsComment::commentDelete($comment_id);
        if ($del > 0){
            #删除数据成功更新评分
            foreach($data as $v){
                $now=Goods::find($v['goods_id']);
                $total = GoodsComment::where('goods_id', $v['goods_id'])->get(array('comment_star'));
                $count = round($total->avg('comment_star'),2);
                $now->comment_star = $count;
                $now->update();
            }
            $res = ['code' => 1, 'msg' => '删除成功'];
        }
        return $res;
    }

    /**
     *当前供应商下的商品所有评论
     * @author CK
     */
    public function commentListAdmin($params)
    {
        if (!isset($params['admin_id'])) {
            return ['code' => 90002, 'msg' => '供应商ID不能为空'];
        }
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $data['list'] = GoodsComment::commentListAdmin($params);
        foreach ($data['list'] as $k => $v) {
            $order = GoodsComment::orderDeatil($v['goods_key']);
            $data['list'][$k]['order_goods_detail'] = $order;
        }
        $data['page'] = $params['page'];
        $data['total'] = count($data['list']);
        foreach ($data['list'] as $k => $v) {
            if (!is_null($v['comment_pics'])) {
                $v['comment_pics'] = explode("|", $v['comment_pics']);
            }
        }
        return ['code' => 1, 'data' => $data];
    }
}