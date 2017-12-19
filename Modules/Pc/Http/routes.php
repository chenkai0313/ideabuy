<?php
$api = app('Dingo\Api\Routing\Router');
#公共的url
$api->version(['v1', 'v2'], function ($api) {
    #v1版本公共url
    $api->group(['namespace' => 'Modules\Pc\Http\Controllers\V1', 'prefix' => 'pc', 'middleware' => ['log-pc', 'rsa-mid']], function ($api) {
        $api->any('liTest','LiyongchuanController@liTest');
        $api->post('sms-send', 'CommonController@addSMS');//短信发送功能
        $api->post('upload-image','CommonController@uploadImage');//图片上传
    });
    #v2版本公共url
    $api->group(['namespace' => 'Modules\Pc\Http\Controllers\V2', 'prefix' => 'pc', 'middleware' => ['log-pc','rsa-mid']], function ($api) {

    });
});

#v1版本
$api->version(['v1'], function ($api) {
    #无需身份验证
    $api->group(['namespace' => 'Modules\Pc\Http\Controllers\V1', 'prefix' => 'pc', 'middleware' => ['log-pc','rsa-mid']], function ($api) {
        $api->get('article-detail','ArticleController@articleDetail');//FAQ
        $api->post('validate-sms','UserController@validateSMSPC');//验证短信验证码
        $api->post('add-verifycode','UserController@addVerifyCode');//生成图形验证码
        $api->post('check-verifycode','UserController@checkVerifyCode');//检验图形验证码
        $api->post('user-forgot', 'UserController@userEdit');//忘记密码
        $api->post('check-allcode', 'UserController@checkAllCode');//找回密码时检查短信验证码和图形验证码
        $api->post('getqruuid','UserController@getQruuid');//pc端网页获取qruuid
        $api->post('check-qruuid','UserController@QruuidFirst');//检查qruuid
        $api->get('goods-list','GoodsController@goodsList');//产品列表
        $api->get('goods-detail','GoodsController@goodsDetail');//产品详情
        #首页
        $api->get('goodscategory-list','IndexController@goodsCategoryList');//商品分类
        $api->get('messageannounce-list','IndexController@messageAnnounceList');//商城公告
        #商品评论
        $api->post('comment-add','CommentController@commentAdd');//评论的添加
        $api->post('comment-edit','CommentController@commentEdit');//追加评论
        $api->get('comment-list-product','CommentController@commentListProduct');//用户查看当前商品评论列表
        #订单支付成功回调
        $api->get('order-pay-finish', 'OrderController@orderPayFinish');
        #公告
        $api->get('announce-list', 'UserController@announceList');
    });
    #需要身份验证
    $api->group(['namespace' => 'Modules\Pc\Http\Controllers\V1', 'prefix' => 'pc', 'middleware' => ['jwt-user','rsa-mid','log-pc']], function ($api) {
        $api->post('user-login', 'UserController@userLogin');//登录
        $api->post('user-register', 'UserController@userAdd');//注册
        $api->get('user-getloginflag', 'UserController@getLoginFlag');//获取登录后状态
        $api->post('user-changepassword', 'UserController@userChangePassword');//用户在登录状态修改登录密码
        $api->post('user-sendsms', 'UserController@addSMS');//登录后发短信
        $api->post('userlink-add', 'UserController@linkManAdd');//常用联系人添加
        $api->post('userinfo-add', 'UserController@userInfoAdd');//完善用户信息
        $api->post('userheadimg-upload', 'UserController@userHeadImgUpload');//用户头像上传
        $api->get('userinfo-detail', 'UserController@userInfoDetail');//用户详细信息(完善用户信息用)
        $api->get('user-whiteindex', 'UserController@whiteindex');//首页 用户白条 右侧小标签
        $api->get('user-getloginflag', 'UserController@getLoginFlag');//获取登录后状态
        $api->post('user-editidcard','UserController@userEditIdCard');//身份证号码的添加
        $api->post('user-editidimg','UserController@userEditIdImg');//身份证照片的添加
        $api->post('user-bankadd','UserController@userBankAdd');//银行卡的添加
        $api->get('user-realname','UserController@userRealName');//银行卡的添加
        $api->get('user-banklist','UserController@userBankList');//银行卡列表
        $api->post('user-bankdelete','UserController@userBankDelete');//银行卡删除
        $api->post('user-setpaypwd','UserController@userSetPayPwd');//设置交易密码
        $api->post('user-editpaypwd','UserController@userEditPayPwd');//重置交易密码
        $api->get('user-whiteindex', 'UserController@whiteIndex');//白条首页
        #购物车模块
        $api->post('cart-add','GoodsController@cartAdd');//用户添加购物车商品
        $api->post('cart-del','GoodsController@cartDel');//用户删除购物车商品
        $api->post('cart-list','GoodsController@cartList');//用户购物车右侧列表
        #订单
        $api->post('order-add', 'OrderController@orderAdd');//提交订单
        $api->get('order-list', 'OrderController@orderList');
        $api->get('order-detail', 'OrderController@orderDetail');
        $api->post('order-delete', 'OrderController@orderDelete');
        $api->post('order-finish', 'OrderController@orderFinish');
        $api->post('trade-info', 'OrderController@tradeInfo');//联通下单
        $api->post('order-confirm', 'OrderController@orderConfirm');//确认订单
        $api->get('invoice-list', 'UserController@invoiceList');//确认订单
        $api->get('invoice-detail', 'UserController@invoiceDetail');//确认订单
        #支付宝
        $api->post('alipay', 'PayController@aliPayWeb');
        #地址管理
        $api->get('user-addressdetail', 'UserController@userAddressDetail');
        $api->post('user-addressdelete', 'UserController@userAddressDelete');
        $api->post('user-addressadd', 'UserController@userAddressAdd');
        $api->post('user-addressedit', 'UserController@userAddressEdit');
        $api->get('user-addresslist', 'UserController@userAddressList');//地址列表
        $api->post('user-addressdefault', 'UserController@userAddressDefault');//设为默认地址
        #通知
        $api->get('notice-list', 'UserController@noticeList');
    });
});
#v2版本
$api->version('v2', function ($api) {

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
// \Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
//    var_dump($query->sql);
//    var_dump($query->bindings);
//    var_dump($query->time);
//
// });
