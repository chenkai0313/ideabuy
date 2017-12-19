<?php
$api = app('Dingo\Api\Routing\Router');
$api->version(['v1', 'v2'], function ($api) {
    $api->group(['namespace'=>'Modules\Backend\Http\Controllers','prefix' => 'backend','middleware'=>['log-admin']],function ($api){
        $api->post('forget-cache','CommonController@forgetCache');//清除key的缓存
    });
});
$api->version('v1',function ($api) {
    #无需身份验证
    $api->group(['namespace' => 'Modules\Backend\Http\Controllers','prefix' => 'backend'], function ($api) {
        #Common
        $api->post('base64Image','CommonController@base64Image');
        $api->post('imgupload','CommonController@imgUpload');
        $api->post('file-upload','CommonController@fileUpload');
        #定时推送脚本接口
        $api->post('message-timed', 'MessageController@messageTimed');
        #订单
        $api->post('order-add', 'OrderController@orderAdd');//提交订单
        $api->post('order-clear', 'OrderController@orderClear');//清除会员用户订单
    });
    #需要身份验证
    $api->group(['namespace' => 'Modules\Backend\Http\Controllers','prefix' => 'backend','middleware'=>['jwt-admin','log-admin']], function ($api) {
        #管理员
        $api->post('admin-login', 'AdminController@adminLogin');
        $api->get('admin-list', 'AdminController@adminList');
        $api->get('admin-detail', 'AdminController@adminDetail');
        $api->post('admin-delete', 'AdminController@adminDelete');
        $api->post('admin-add', 'AdminController@adminAdd');
        $api->post('admin-edit', 'AdminController@adminEdit');
        #RBAC权限
        $api->get('role-list', 'RbacController@roleList');
        $api->get('role-list-all', 'RbacController@roleListAll');
        $api->get('role-detail', 'RbacController@roleDetail');
        $api->post('role-delete', 'RbacController@roleDelete');
        $api->post('role-add', 'RbacController@roleAdd');
        $api->post('role-edit', 'RbacController@roleEdit');
        $api->get('permission-type', 'RbacController@permissionType');
        $api->get('permission-list', 'RbacController@permissionList');
        $api->get('permission-detail', 'RbacController@permissionDetail');
        $api->post('permission-delete', 'RbacController@permissionDelete');
        $api->post('permission-add', 'RbacController@permissionAdd');
        $api->post('permission-edit', 'RbacController@permissionEdit');
        $api->get('role-admin-list', 'RbacController@roleAdminList');
        $api->get('role-admin-detail', 'RbacController@roleAdminDetail');
        $api->post('role-admin-add', 'RbacController@roleAdminAdd');
        $api->get('permission-role-list', 'RbacController@permissionRoleList');
        $api->get('permission-role-detail', 'RbacController@permissionRoleDetail');
        $api->post('permission-role-add', 'RbacController@permissionRoleAdd');
        $api->get('permission-left', 'RbacController@permissionLeft');
        #广告分类
        $api->post('adtype-add','AdController@adTypeAdd');
        $api->get('adtype-list','AdController@adTypeList');
        $api->get('adtype-detail','AdController@adTypeDetail');
        $api->post('adtype-edit','AdController@adTypeEdit');
        #广告
        $api->get('ad-list','AdController@adList');
        $api->post('ad-delete','AdController@adDelete');
        $api->post('ad-add','AdController@adAdd');
        $api->get('ad-detail','AdController@adDetail');
        $api->post('ad-edit','AdController@adEdit');
        #文章分类
        $api->post('articletype-add','ArticleController@articleTypeAdd');
        $api->post('articletype-delete','ArticleController@articleTypeDelete');
        $api->post('articletype-edit','ArticleController@articleTypeEdit');
        $api->get('articletype-list','ArticleController@articleTypeList');
        $api->get('articletype-detail','ArticleController@articleTypeDetail');
        $api->get('articletype-select','ArticleController@articleTypeSelect');
        #文章
        $api->post('article-add','ArticleController@articleAdd');
        $api->post('article-delete','ArticleController@articleDelete');
        $api->post('article-edit','ArticleController@articleEdit');
        $api->get('article-detail','ArticleController@articleDetail');
        $api->get('article-list','ArticleController@articleList');
        $api->get('adtype-spinner','AdController@adTypeSpinner');
        #用户管理
        $api->get('user-list', 'UserController@userList');#用户列表
        $api->get('user-detail', 'UserController@userInfoDetail');#用户详情
        $api->get('userapply-detail','UserController@userApplyReview');#审核详情
        $api->get('userapply-list','UserController@userApplyReviewList');#审核列表
        $api->post('userapply-edit','UserController@userReviewOperatio');#审核操作
        $api->post('user-add', 'UserController@userAdd');  #用户添加
        #订单管理
        $api->get('order-list','OrderController@orderList');
        $api->get('order-detail','OrderController@orderDetail');
        $api->post('order-apart','OrderController@orderApart');
        $api->post('order-assign','OrderController@orderAssign');
        #短信模板管理
        $api->post('msgtemplate-add','MsgTemplateController@msgTemplateAdd');
        $api->get('msgtemplate-list','MsgTemplateController@msgTemplateList');
        $api->get('msgtemplate-detail','MsgTemplateController@msgTemplateDetail');
        $api->post('msgtemplate-edit','MsgTemplateController@msgTemplateEdit');
        $api->post('msgtemplate-delete','MsgTemplateController@msgTemplateDelete');
        #短信模板对应类型关键字
        $api->post('msgtemplatekeyword-add','MsgTemplateController@msgTemplateKeywordAdd');
        $api->get('msgtemplatekeyword-list','MsgTemplateController@msgTemplateKeywordList');
        $api->get('msgtemplatekeyword-detail','MsgTemplateController@msgTemplateKeywordDetail');
        $api->post('msgtemplatekeyword-edit','MsgTemplateController@msgTemplateKeywordEdit');
        $api->post('msgtemplatekeyword-delete','MsgTemplateController@msgTemplateKeywordDelete');
        #操作日志管理
        $api->get('log-list','LogController@logList');
        $api->get('log-detail','LogController@logDetail');
        #推送、消息、短信列表
        $api->get('message-list', 'MessageController@messageList'); // 短消息列表
        $api->get('push-list', 'MessageController@pushList');       // 推送列表
        $api->post('message-announcetop', 'MessageController@announceTop'); // 消息 最高置顶公告信息
        $api->get('message-announcelist', 'MessageController@announceList');// 消息 公告列表
        $api->get('message-noticelist', 'MessageController@noticeList');    // 消息 通知列表
        $api->get('message-smslist', 'MessageController@smsList');          //短信列表
        $api->post('message-push', 'MessageController@push');               // 推送、消息群发
        $api->get('message-push-select', 'MessageController@pushSelect');  // 消息发送下拉列表
        #数据统计
        $api->get('user-statistics', 'DataStatisticsController@userCountStatistics');
        #version
        $api->get('version-list','VersionController@versionList');
        $api->post('version-delete','VersionController@versionDelete');
        $api->post('version-add','VersionController@versionAdd');
        $api->get('version-dispaly','VersionController@versionAddDisplay');
        #商品品牌管理
        $api->post('goodsbrand-add','GoodsController@goodsBrandAdd');
        $api->get('goodsbrand-list','GoodsController@goodsBrandList');
        $api->get('goodsbrand-detail','GoodsController@goodsBrandDetail');
        $api->post('goodsbrand-edit','GoodsController@goodsBrandEdit');
        $api->post('goodsbrand-delete','GoodsController@goodsBrandDelete');
        #商品分类管理
        $api->post('goodscategory-add','GoodsController@goodsCategoryAdd');
        $api->get('goodscategory-list-level','GoodsController@goodsCategoryListLevel');
        $api->get('goodscategory-list-tree','GoodsController@goodsCategoryListTree');
        $api->get('goodscategory-detail','GoodsController@goodsCategoryDetail');
        $api->post('goodscategory-edit','GoodsController@goodsCategoryEdit');
        $api->post('goodscategory-delete','GoodsController@goodsCategoryDelete');
        #商品
        $api->get('goods-select', 'GoodsController@goodsSelect');
        $api->get('goods-list', 'GoodsController@goodsList');
        $api->get('goods-detail', 'GoodsController@goodsDetail');
        $api->post('goods-add', 'GoodsController@goodsAdd');
        $api->post('goods-edit', 'GoodsController@goodsEdit');
        $api->post('goods-delete', 'GoodsController@goodsDelete');
        $api->post('goods-status-change', 'GoodsController@goodsStatusChange');
        #货品
        $api->get('goods-product-list', 'GoodsController@goodsProductList');
        $api->post('goods-product-add', 'GoodsController@goodsProductAdd');
        $api->post('goods-product-edit', 'GoodsController@goodsProductEdit');
        $api->post('goods-product-delete', 'GoodsController@goodsProductDelete');
        $api->post('goods-product-status-change', 'GoodsController@goodsProductStatusChange');
        #商品属性
        $api->post('attribute-add','GoodsController@attributeAdd');//属性添加
        $api->post('attribute-edit','GoodsController@attributeEdit');//属性修改
        $api->post('attribute-del','GoodsController@attributeDel');//属性删除
        $api->get('attribute-list','GoodsController@attributeList');//属性列表
        $api->get('attribute-detail','GoodsController@attributeDetail');//属性详情
        #类型添加
        $api->post('type-add','GoodsController@typeAdd');//类型添加
        $api->get('type-detail','GoodsController@typeDetail');//单个类型查询
        $api->post('type-edit','GoodsController@typeEdit');//类型修改
        $api->post('type-del','GoodsController@typeDel');//类型删除
        $api->get('type-list','GoodsController@typeList');//类型列表
        $api->get('type-all-list','GoodsController@typeAllList');//所有类型的列表
        $api->get('type-attr-list','GoodsController@attributeListByTypeId');//通过typeid查类型
        #商品评论
        $api->post('comment-add','GoodsController@commentAdd');//评论的添加
        $api->post('comment-edit','GoodsController@commentEdit');//追加评论
        $api->post('comment-repay','GoodsController@commentRepay');//评论回复
        $api->post('comment-delete','GoodsController@commentDelete');//评论删除
        $api->get('comment-list-all','GoodsController@commentListAll');//管理员后台查看所有评论列表
        $api->get('comment-list-product','GoodsController@commentListProduct');//供应商或者用户查看当前商品评论列表
        $api->get('comment-detail','GoodsController@commentDetail');//评论详情
        $api->get('comment-list-admin','GoodsController@commentListAdmin');//当前供应商下所有商品评价

    });

});
app('Dingo\Api\Exception\Handler')->register(function (\Dingo\Api\Exception\UnknownVersionException $exception) {
    return ['code'=>99999, 'msg'=>"版本错误"];
});
app('Dingo\Api\Exception\Handler')->register(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception) {
    return ['code'=>99999, 'msg'=>"该版本没有这个接口"];
});
app('Dingo\Api\Exception\Handler')->register(function (\Symfony\Component\HttpKernel\Exception\BadRequestHttpException $exception){
    return ['code'=>99999,'msg'=>'该接口不支持该访问方式'];
});
//\Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
//    var_dump($query->sql);
//    var_dump($query->bindings);
//    var_dump($query->time);
//});

