<?php
/**
 * PC端GoodsService
 * Created by PhpStorm.
 * User: caohan\fuyuehua
 * Date: 2017/10/12
 */

namespace Modules\Goods\Services;

use Modules\Backend\Models\Admin;
use Modules\Goods\Models\GoodsAttr;
use Modules\Goods\Models\GoodsBrand;
use Modules\Goods\Models\GoodsCart;
use Modules\Goods\Models\Goods;
use Modules\Goods\Models\GoodsDesc;
use Modules\Goods\Models\GoodsProducts;
use Modules\System\Models\Region;

class PcGoodsService
{
    /**
     * 商品列表
     * @author fuyuehua
     * @param $params
     * @return array
     */
    public function goodsList($params)
    {
        $brand_list = GoodsBrand::allgoodsBrandList($params);
        $goods_list = Goods::pcGoodsList($params);
        if ($brand_list) {
            foreach ($goods_list['data'] as &$item) {
                $item['goods_thumb'] = [config('services.oss.host') . array_first(explode('|', $item['goods_thumb']))];
//                $item['goods_img'] = explode('|', $item['goods_img']);
                $item['goods_img'] = [config('services.oss.host') . array_first(explode('|', $item['goods_img']))];
            }

            $data['brand_list'] = $brand_list;
            $data['goods_list'] = $goods_list;
            $result['code'] = 1;
            $result['data'] = $data;
        } else {
            $result = ['code' => 10250, 'msg' => '商品获取失败'];
        }
        return $result;
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
            return ['code' => 90001, 'msg' => '传参错误'];
        }
        #根据attr确定货品
        $attr_id = [];
        $data = [];
        #货品id
        $product_id = '';
        #可以选择的货品id
        $may_select_product_id = [];
        #确定的属性值
        $determine_attr = [];
        #可以选择的属性值
        $may_select_attr_name = [];
        #根据属性值确定货品
        if (isset($params['attr_id'])) {
            $params['attr_id'] = explode(',', $params['attr_id']);
            $attr_id = $params['attr_id'];
            foreach ($attr_id as $k => $v) {
                if (!empty($v)) {
                    $attr_id[$k] = intval($v);
                }
            }
            $attr_info = GoodsAttr::getProductByAttrId($params);
            #生成过滤条件
            $where = [];
            $count = count($attr_info);
            foreach ($attr_info as $key => $value) {
                if (!empty($value)) {
                    $determine_attr[] = $value['attr_name'];
                    $where[] = [
                            ['attr_name', '=', $value['attr_name']],
                            ['attr_value', '=', $value['attr_value']]
                        ];
                }
            }
            $value_data = ['where' => $where, 'goods_id' => $params['goods_id']];
            $product_ids = GoodsAttr::getProductByAttrValue($value_data);
            $product_id_count = array_count_values($product_ids);
            foreach ($product_id_count as $key => $value) {
                if ($value == $count) {
                    if (empty($product_id)) {
                        $product_id = $key;
                    }
                    $may_select_product_id[] = $key;
                }
            }
            #根据已有选择属性  过滤出能够选择的其他属性值
            $select_data['goods_id'] = $params['goods_id'];
            $select_data['attr_name'] = $determine_attr;
            $select_data['product_id'] = $may_select_product_id;
            $may_select_attr_name = GoodsAttr::getOtherAttr($select_data);
        }
        #商品主信息
        $goods_detail = Goods::pcGoodsDetail($params);
        #商品描述
        $goods_desc = GoodsDesc::goodsDescDetail($params);

        if ($goods_desc && $goods_detail) {
            #地址转换
            $goods_detail['shipping_range_name'] = Region::regionGet([$goods_detail['shipping_range']])->first();
            #商品信息加工
            $goods_detail['comment_star'] = round($goods_detail['comment_star']);
            $goods_detail['goods_price'] = number_format($goods_detail['goods_price'], 2);
            #供应商信息
            $supplier_info = Admin::adminInfoById($goods_detail['admin_id']);
            if (!empty($supplier_info['city'])) {
                $supplier_info['city_name'] = Region::regionGet([$supplier_info['city']])->first();
            }
            #图片处理
            $goods_thumb = explode('|', $goods_detail['goods_thumb']);
            $goods_img = explode('|', $goods_detail['goods_img']);
            if (!empty($goods_thumb)) {
                foreach ($goods_thumb as $k3 => $v3) {
                    $goods_thumb[$k3] = config('services.oss.host') . $v3;
                }
                $goods_detail['goods_thumb'] = $goods_thumb;
            }
            if (!empty($goods_img)) {
                foreach ($goods_img as $k4 => $v4) {
                    $goods_img[$k4] = config('services.oss.host') . $v4;
                }
                $goods_detail['goods_img'] = $goods_img;
            }

            #详情左侧商品推荐  TODO 暂时三个列表相同  模拟数据用
            $goods_left_type = Goods::pcGoodsListLeft($goods_detail);
            if (!empty($goods_left_type)) {
                foreach ($goods_left_type as $key => $item) {
                    $goods_left_type[$key]['goods_thumb'] = config('services.oss.host') . array_first(explode('|', $item['goods_thumb']));
                    $goods_left_type[$key]['goods_img'] = config('services.oss.host') . array_first(explode('|', $item['goods_img']));
                }
            }
            $goods_left_sales = $goods_left_type;
            $goods_left_history = $goods_left_type;

            $goods_left = [
                'goods_left_type' => $goods_left_type,
                'goods_left_sales' => $goods_left_sales,
                'goods_left_history' => $goods_left_history,
            ];
            #商品价格，销量修改
            if (!empty($goods_product)) {
                $goods_detail['goods_price'] = $goods_product['product_price'];
                $goods_detail['goods_number'] = $goods_product['product_number'];
            }

            #货品列表处理
            #货品详情
            $params['is_show'] = 1;
            $goods_product = GoodsProducts::goodsProductList($params)['goods_product'];
            #获取所有product_id
            $goods_product = json_decode(json_encode($goods_product), true);
            $goods_product_ids = array_column($goods_product, 'product_id');
            $goods_attrs = GoodsAttr::goodsAttrList($goods_product_ids);
            $options = [];
            foreach ($goods_attrs as $attr) {
                $status = false;
                #是否被选中
                $selected = false;
                $attr_value = [];
                $value = [];
                #能否被选择
                if (count($determine_attr) == 1) {
                    $can_select = in_array($attr['attr_name'], $determine_attr) || empty($attr_id) ? true : false;
                } else {
                    $can_select = empty($attr_id) ? true : false;
                }

                #判断该属性是否可被选择
                if (!empty($may_select_attr_name)) {
                    foreach ($may_select_attr_name as $item) {
                        if ($item['attr_name'] == $attr['attr_name'] && $item['attr_value'] == $attr['attr_value']) {
                            $can_select = true;
                        }
                    }
                }
                #判断选中值
                foreach ($attr_id as $k1 => $v1) {
                    if ($v1 == $attr['attr_id']) {
                        $selected = true;
                    }
                }
                #过滤，去重商品属性值
                foreach ($options as $key => $option) {
                    if ($option['attr_name'] == $attr['attr_name']) {
                        foreach ($option['attr_values'] as $k2 => $v2) {
                            if ($v2['attr_value'] == $attr['attr_value']) {
                                continue 3;
                            }
                        }
                        $attr_value['attr_id'] = $attr['attr_id'];
                        $attr_value['attr_value'] = $attr['attr_value'];
                        $attr_value['status'] = $can_select;
                        $attr_value['selected'] = $selected;
                        $options[$key]['attr_values'][] = $attr_value;
                        $status = true;
                        continue;
                    }
                }
                if (!$status) {
                    $attr_value['attr_id'] = $attr['attr_id'];
                    $attr_value['attr_value'] = $attr['attr_value'];
                    $attr_value['status'] = $can_select;
                    $attr_value['selected'] = $selected;
                    $value['attr_name'] = $attr['attr_name'];
                    $value['attr_values'] = [$attr_value];
                    $options[] = $value;
                }
            }

            $data['goods_detail'] = $goods_detail;
            $data['goods_detail']['goods_desc'] = $goods_desc['goods_desc'];
            $data['goods_detail']['attr_id'] = $attr_id;
            $data['goods_detail']['product_id'] = $product_id;
            $data['goods_left'] = $goods_left;
            $data['supplier_info'] = $supplier_info;
            $data['goods_detail']['options'] = $options;
        }
        return $result = ['code' => 1, 'data' => $data];
    }
    /**
     * 货品详情
     * @param $product_id int 货品ID
     */
    public function productDetail($params) {
        if (empty($params['product_id'])) {
            return ['code' => 90001, 'msg' => '传参错误'];
        }
        $product_info = GoodsProducts::goodsProductDetail($params['product_id']);
        if($product_info){
            $result['code'] = 1;
            $result['data']['product_info'] = $product_info;
        }else{
            $result['code'] = 10257;
            $result['msg'] = '找不到该货品';
        }
        return $result;
    }
    /**
     * 查询某用户的购物车列表
     * user_id jwt
     */
    public function cartListByUserId($params) {
        $res = ['code'=>1,'msg'=>'查询成功'];
        //查该用户所有购物车
        $admin_group = GoodsCart::cartAdminIdByUserId($params['user_id']);
        foreach ($admin_group as $key => $value) {
            //查 admin_id  商家名称
            $condition['condition'] = ['user_id'=>$params['user_id'],'admin_id'=>$value['admin_id']];
            //查 该商家下的 该用户的购物车商品
            $admin_group[$key]['cart_list'] = GoodsCart::cartAdminIDListByUserId($condition);
            //查 该商品的attr
            foreach ($admin_group[$key]['cart_list'] as $key1 => $value1) {
                $admin_group[$key]['cart_list'][$key1]['attr_list'] = GoodsAttr::goodsAttrByGoodsId(['goods_id'=>$value1['goods_id'],'product_id'=>$value1['product_id']]);
                $admin_group[$key]['cart_list'][$key1]['goods_thumb'] = empty($admin_group[$key]['cart_list'][$key1]['goods_thumb'])?"":env('ACCESS_HOST').$admin_group[$key]['cart_list'][$key1]['goods_thumb'];
                $admin_group[$key]['cart_list'][$key1]['goods_img'] = empty($admin_group[$key]['cart_list'][$key1]['goods_img'])?"":env('ACCESS_HOST').$admin_group[$key]['cart_list'][$key1]['goods_img'];
            }
        }
        $res['data'] =  $admin_group;
        $res['count'] = GoodsCart::cartCountByUserId($params['user_id']);
        return $res;
    }

    /**
     * 添加购物车
     */
    public function cartAdd($params) {
        //参数过滤
        $validator=\Validator::make(
            $params,
            \Config::get('validator.system.goodscart.cart-add'),
            \Config::get('validator.system.goodscart.cart-key'),
            \Config::get('validator.system.goodscart.cart-val')
        );
        if(!$validator->passes()){
            return ['code'=>90002,'msg'=>$validator->messages()->first()];
        }

        $result = ['code' => 500, 'msg' => "加入购物车失败"];

        $params['goods_thumb'] = str_replace(env('ACCESS_HOST'),"",$params['goods_thumb']);
        $params['goods_img'] = str_replace(env('ACCESS_HOST'),"",$params['goods_img']);

        //查询Goods表
        $goods = Goods::goodsDetail($params['goods_id']);
        $params['goods_name'] = $goods['goods_name'];
        $params['admin_id']= $goods['admin_id'];
        $params['goods_sn']= $goods['goods_sn'];

        //先查询 如果有相同 则数量+1
        $cart_find = ['user_id' => $params['user_id'],
            'admin_id'=> $params['admin_id'],
            'goods_id'=> $params['goods_id'],
            'product_id'=> $params['product_id'],
            'goods_sn'=> $params['goods_sn'],
        ];
        $condition['condition'] = $cart_find;
        $cart = GoodsCart::cartFindSame($condition);
        if (is_null($cart)) {  //如果用户相同商品购物车里没有的话
            //TODO 查询价格
            $product_money = GoodsProducts::goodsProductFirst(['product_id'=>$params['product_id']]);
            $params['market_price'] = $product_money['market_price'];
            $params['product_price'] = $product_money['product_price'];
            $params['goods_attr'] = $product_money['goods_attr'];
            $addInfo = GoodsCart::cartAdd($params);
            if ($addInfo) {
                $result['code'] = 1;
                $result['msg'] = "加入购物车成功";
            }
        } else {  //如果有
            $condition['goods_number'] =  $cart['goods_number'] + $params['goods_number'];
            if ($condition['goods_number'] < 0 ) {
                return ['code'=>500,'msg'=>'购物车商品删除失败'];
            }
            $cart_update = GoodsCart::cartUpdateNumber($condition);
            $result['code'] = 1;
            $result['msg'] = "更新购物车成功";
        }

        return $result;
    }

    /**
     * 用户的购物车删除
     * cart_id  int or arr(1,2,3,4)
     */
    public function cartDel($params) {
        if (!isset($params['cart_id']))
            return ['code' => 90002, 'msg' => '请输入购物车ID'];
        if (strpos($params['cart_id'], ',')) {
            $params['cart_id'] = explode(',', $params['cart_id']);
        }
        $result = ['code' => 500, 'msg' => "删除失败"];
        $delinfo = GoodsCart::cartDel($params['cart_id']);
        if ($delinfo > 0) {
            $result['code'] = 1;
            $result['msg'] = "删除成功";
        }
        return $result;
    }
}