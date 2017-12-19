<?php
/**
 * Created by PhpStorm.
 * User: 曹晗
 * Date: 2017/7/29
 * Time: 14:07
 */
return [
    #广告分类
    'adtype' => [
        'adtype-val' => [
            'type_name' => '广告分类名',
            'img_size' => '广告图片大小'
        ],
        'adtype-key' => [
            'integer' => ':attribute必须为整数',
            'required' => ':attribute必填',
        ],
        #广告分类的编辑
        'adtype-edit' => [
            'type_id' => 'required|integer',
            'type_name' => 'required',
            'img_size' => 'required',
        ],
        #广告分类的添加
        'adtype-add' => [
            'type_name' => 'required',
            'img_size' => 'required',
        ]
    ],
    #广告表
    'ad' => [
        'ad-val' => [
            'type_id' => '广告分类',
            'ad_id' => '广告ID',
            'ad_img' => '广告图片',
            'is_show' => '是否显示'
        ],
        'ad-key' => [
            'integer' => ':attribute必须为整数',
            'required' => ':attribute必填',
        ],

        #广告的添加
        'ad-add' => [
            'type_id' => 'required|integer',
            'ad_img' => 'required',
            'is_show' => 'required',
        ],
        #广告的编辑
        'ad-edit' => [
            'ad_id' => 'required|integer',
            'type_id' => 'required|integer',
            'ad_img' => 'required',
            'is_show' => 'required',
        ]
    ],


    #内容类型验证
    'articletype' => [
        #类型添加
        'articletype-add' => [
            'type_name' => 'required',
            'parent_id' => 'required|integer',
        ],
        #类型编辑
        'articletype-edit' => [
            'type_id' => 'required',
            'parent_id' => 'required|integer',
        ],
        'articletype-key' => [
            'required' => ':attribute为必填项',
            'min' => ':attribute长度不符合要求',
            'integer' => ':attribute必须是数字',
            'unique' => ':attribute必须唯一'
        ],
        'articletype-val' => [
            'type_name' => '类型名称',
            'parent_id' => '父级ID',
        ]
    ],
    #文章管理验证
    'article' => [
        #文章添加
        'article-add' => [
            'article_title' => 'required',
//            'article_content' => 'required',
            'type_id' => 'required',
        ],
        #文章编辑
        'article-edit' => [
            'article_id' => 'required',
        ],
        'article-key' => [
            'required' => ':attribute为必填项',
            'min' => ':attribute长度不符合要求',
            'integer' => ':attribute必须是数字',
            'unique' => ':attribute必须唯一'
        ],
        'article-val' => [
            'article_id' => '文章ID',
            'article_title' => '文章标题',
            'article_content' => '文章内容',
            'type_id' => '类型ID',
            'admin_id' => '操作员ID'
        ]
    ],

    #sms验证
    'sms' => [
        'sms-val' => [
            'mobile' => '手机号',
            'type' => '类型',
            'code' => '验证码'
        ],
        'sms-key' => [
            'integer' => ':attribute必须为整数',
            'required' => ':attribute必填',
            'regex' => ':attribute格式不正确',
            'unique' => ':attribute已被注册',
        ],
        #发送短信
        'sms-send' => [
            'mobile' => array('regex:/^1[34578]+\d{9}$/', 'required'),
            'type' => 'required'
        ]
    ],
    #constanttype验证
    'constanttype' => [
        'constanttype-val' => [
            'type' => '常量类型',
        ],
        'constanttype-key' => [
            'required' => ':attribute必填',
            'unique' => ':attribute唯一',
        ],
        #发送短信
        'constanttype-add' => [
            'type' => 'required|unique:system_constant_type'
        ]
    ],
    #消息模板验证
    'msgtemplate' => [
        'msgtemplate-val' => [
            'keyword_id' => '消息模板关键字ID',
            'keyword_name' => '消息模板关键字',
            'content' => '消息模板内容',
            'prepare_node' => '预发节点',
            'msg_title'=>'消息标题',
            'id' => '消息模板ID',
            'msg_type' => '信息类型',
            'msg_tag' => '信息标签',
            'keyword_zh'=>'关键字解释'
        ],
        'msgtemplate-key' => [
            'integer' => ':attribute必须为整数',
            'required' => ':attribute必填',
        ],
        #消息关键字的添加
        'msgtemplatetype-add' => [
            'keyword_name' => 'required',
            'keyword_zh'=>'required',
        ],
        #消息关键字的编辑
        'msgtemplatetype-edit' => [
            'keyword_id' => 'required',
            'keyword_name' => 'required',
            'keyword_zh'=>'required',
        ],
        #消息模板的添加
        'msgtemplate-add' => [
            'content' => 'required',
            'prepare_node' => 'required',
            'msg_type' => 'required',
            'msg_tag' => 'required',
            'msg_title'=>'required',
        ],
        #消息模板的修改
        'msgtemplate-edit' => [
            'content' => 'required',
            'prepare_node' => 'required',
            'id' => 'required',
            'msg_type' => 'required',
            'msg_tag' => 'required',
            'msg_title'=>'required',
        ],
    ],
    #商品品牌验证
    'goodsbrand' => [
        'goodsbrand-val' => [
            'brand_name' => '品牌名称',
            'brand_thumb'=>'品牌缩略图',
            'brand_desc'=>'品牌描述',
            'is_show'=>'是否展示',
            'brand_id'=>'品牌ID'
        ],
        'goodsbrand-key' => [
            'required' => ':attribute必填',
            'max' => ':attribute长度不符合',
            'unique' => ':attribute唯一',
        ],
        #添加品牌
        'goodsbrand-add' => [
            'brand_name' => 'required|max:20',
            'brand_thumb' => 'required',
            'brand_desc' => 'required',
            'is_show' => 'required',
        ],
        #编辑品牌
        'goodsbrand-edit' => [
            'brand_name' => 'required|max:20',
            'brand_thumb' => 'required',
            'brand_desc' => 'required',
            'is_show' => 'required',
            'brand_id' => 'required',
        ]
    ],
    #商品种类验证
    'goodscategory' => [
        'goodscategory-val' => [
            'cat_id' => '商品分类ID',
            'pid'=>'父级ID',
            'sort_order'=>'排序ID',
            'cat_name'=>'分类名称',
            'cat_desc'=>'分类描述',
            'cat_thumb'=>'分类缩略图',
            'keywords'=>'分类关键字',
            'is_show'=>'是否展示',
            'is_show_nav'=>'是否在导航展示',
        ],
        'goodscategory-key' => [
            'required' => ':attribute必填',
            'max' => ':attribute长度不符合',
            'unique' => ':attribute唯一',
        ],
        #添加种类
        'goodscategory-add' => [
            'pid' => 'required',
            'sort_order' => 'required',
            'cat_name' => 'required|max:60',
            'cat_desc' => 'required|max:255',
            'cat_thumb' => 'required',
            'keywords' => 'required|max:255',
            'is_show' => 'required',
            'is_show_nav' => 'required',
        ],
        #编辑种类
       'goodscategory-edit' => [
           'cat_id'=>'required',
           'pid' => 'required',
           'sort_order' => 'required',
           'cat_name' => 'required|max:60',
           'cat_desc' => 'required',
           'cat_thumb' => 'required',
           'keywords' => 'required|max:255',
           'is_show' => 'required',
           'is_show_nav' => 'required',
       ]
    ],

    #商品验证
    'goods' => [
        'goods-val' => [
            #商品
            'goods_info.goods_name' => '商品名称',
            'goods_info.cat_id' => '商品分类id',
            'goods_info.brand_id' => '商品品牌id',
            'goods_info.type_id' => '商品类型id',
            'goods_info.shipping_range' => '配送地区id',
            'goods_info.goods_id' => '商品id',
        ],
        'goods-key' => [
            'unique' => ':attribute已存在',
            'required' => ':attribute必填',
            'integer' => ':attribute必须为整型',
            'json' => ':attribute必须为JSON',
            'array' => ':attribute必须为数组',
        ],
        #商品添加
        'goods-add' => [
            'goods_info' => 'required|array',
            'goods_info.goods_name' => 'required',
            'goods_info.cat_id' => 'required|integer',
            'goods_info.brand_id' => 'required|integer',
            'goods_info.type_id' => 'required|integer',
            'goods_info.is_real' => 'required|integer',
            'goods_info.is_shipping' => 'required|integer',
            'goods_info.shipping_range' => 'required|integer',
            'product_info' => 'array',
        ],
        #商品修改
        'goods-edit' => [
            'goods_info' => 'required|array',
            'goods_info.goods_id' => 'required|integer',
            'goods_info.cat_id' => 'required|integer',
            'goods_info.brand_id' => 'required|integer',
            'goods_info.type_id' => 'required|integer',
            'goods_info.is_real' => 'required|integer',
            'goods_info.is_shipping' => 'required|integer',
            'goods_info.shipping_range' => 'required|integer',
            'product_info' => 'required|array',
        ],
    ],

    'goodsattribute' => [
        'attribute-val' => [
            'attr_id' => '商品属性ID',
            'attr_name' => '商品属性名称',
            'type_id' => '类型ID',
        ],
        'attribute-key' => [
            'unique' => ':attribute已存在',
            'required' => ':attribute必填',
            'integer' => ':attribute必须为整型',
        ],
        #商品属性添加
        'attribute-add' => [
            'attr_name' => 'required',
            'type_id' => 'required',
        ],
        #商品属性编辑
        'attribute-edit' => [
            'attr_id' => 'required',
            'attr_name' => 'required',
            'type_id' => 'required',
        ],
    ],
    #类型
    'goodstype' => [
        'type-val' => [
            'type_id' => '类型ID',
            'type_name' => '类型名称',
        ],
        'type-key' => [
            'unique' => ':attribute已存在',
            'required' => ':attribute必填',
            'integer' => ':attribute必须为整型',
        ],
        #商品类型添加
        'type-add' => [
            'type_name' => 'required',
        ],
        #商品类型编辑
        'type-edit' => [
            'type_id' => 'required',
            'type_name' => 'required',
        ],
    ],
    #购物车
    'goodscart' => [
        'cart-val' => [
            'admin_id'=>'供应商ID',
            'goods_id'=>'商品ID',
            'product_id'=>'货品ID',
            'goods_sn'=>'商品编号',
            'goods_name'=>'商品名称',
            'goods_number'=>'商品数量',
            'goods_attr'=>'货品属性',
            'market_price'=>'市场价',
            'product_price'=>'平台价',
            'goods_thumb'=>'商品缩略图（小）',
            'goods_img'=>'商品缩略图（大）',
        ],
        'cart-key' => [
            'required' => ':attribute必填',
            'integer' => ':attribute必须为整型',
        ],
        #购物车添加
        'cart-add' => [
            'goods_id'=>'required|integer',
            'product_id'=>'required|integer',
            'goods_number'=>'required|integer',
            'goods_thumb'=>'required',
            'goods_img'=>'required',
        ],
    ],
    #商品评论
    'goodscomment' => [
        'goodscomment-val' => [
            'comment_id' => '评论ID',
            'goods_id' => '商品ID',
            'product_id' => '货品ID',
            'user_id' => '用户ID',
            'order_sn' => '订单编号',
            'comment_type' => '评论类型',
            'comment_star' => '评论星级',
            'comment_pics' => '评论图片',
            'comment_desc' => '评论内容',
            'comment_extra_desc' => '追评内容',
            'comment_repay' => '评论回复',
            'goods_key' => '订单商品唯一码'
        ],
        'goodscomment-key' => [
            'unique' => ':attribute已存在',
            'required' => ':attribute必填',
            'integer' => ':attribute必须为整型',
            'unique' => ':attribute已存在',
        ],
        #评论添加
        'goodscomment-add' => [
            'user_id' => 'required',
            'comment_type' => 'required',
            'comment_star' => 'required',
            'order_sn' => 'required',
            'comment_desc'=>'required',
            'goods_key' => 'required|unique:goods_comment',
        ],
    ],


];