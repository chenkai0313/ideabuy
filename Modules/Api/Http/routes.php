<?php
$api = app('Dingo\Api\Routing\Router');
#公共的url
$api->version(['v1', 'v2'], function ($api) {
    #v1版本公共url
    $api->group(['namespace' => 'Modules\Api\Http\Controllers\V1', 'prefix' => 'api', 'middleware' => ['log-app', 'rsa-mid']], function ($api) {
        #广告获取
        $api->get('ad-obtain', 'AdController@adObtain');
        #用户
        $api->post('sms-send', 'CommonController@addSMS');//短信发送功能
        #版本跟新
        $api->post('ios-version', 'CommonController@iosVersion');
        $api->post('ios-front','CommonController@iosFront');
        $api->post('android-version','CommonController@androidVersion');
        $api->post('android-hotupdate','CommonController@androidHotUpdate');
        $api->post('android-front','CommonController@androidFront');
        $api->get('check-version','CommonController@checkVersion');
        $api->post('yee-return','PayController@yeeReturn');
        $api->post('yee-notify','PayController@yeeNotify');
        $api->get('testSendsms','TestController@testSendsms');
        $api->get('testPush','TestController@testPush');
        $api->get('testRedis','TestController@testRedis');
    });
    #v2版本公共url
    $api->group(['namespace' => 'Modules\Api\Http\Controllers\V2', 'prefix' => 'api', 'middleware' => ''], function ($api) {

    });
});

#v1版本
$api->version(['v1'], function ($api) {
    #无需身份验证
    $api->group(['namespace' => 'Modules\Api\Http\Controllers\V1', 'prefix' => 'api', 'middleware' => ['log-app', 'rsa-mid']], function ($api) {
        #RSA
        $api->any('testRSA', 'TestController@testRSA');//测试
        $api->any('testPayload', 'TestController@testPayload');//测试payload
        #前端测试获取常用数据
        $api->get('test', 'TestController@test');
        $api->post('user-test', 'TestController@user_test');
        $api->any('userDelete', 'TestController@userDelete');//用户的关联删除（测试用）
        $api->any('liTest', 'LiyongchuanController@liTest');
        $api->post('user-forgot', 'UserController@userEdit');//忘记密码
        #首页
        $api->get('white-getbanner', 'UserController@whiteGetBanner');//获得banner图
        #支付宝测试
        $api->post('webNotify', 'PayController@webNotify');
        $api->any('order-unloan', 'CommonController@orderUnLoan');
        $api->get('ideabuy-index', 'UserController@ideabuyIndex');//畅想购首页
        $api->post('user-wallet-detail-add', 'UserWalletController@UserWalletDetailAdd');//对接rc 添加账单收支记录
        $api->post('user-white-amount', 'UserController@userWhiteAmount');//对接rc 查询白条可用余额
        $api->post('user-black-status', 'UserController@userBlackStatus');//对接rc 修改用户黑名单状态
        $api->post('user-white-amount', 'UserController@userWhiteAmount');//对接rc 查询白条可用余额
        $api->get('message-announcelist', 'MessageAnnounceController@announceList'); // 短消息 公告列表
        $api->post('userTestWallet', 'TestController@userTestWallet');//测试user_wallet 账单流水
    });
    #需要身份验证
    $api->group(['namespace' => 'Modules\Api\Http\Controllers\V1', 'prefix' => 'api', 'middleware' => ['jwt-user', 'log-app', 'rsa-mid']], function ($api) {
        #用户
        $api->post('user-login', 'UserController@userLogin');//登录
        $api->post('bind-qruuid','UserController@bindQruuid');//App绑定qruuid
        $api->get('user-getloginflag', 'UserController@getLoginFlag');//获取登录后状态
        $api->post('user-register', 'UserController@userAdd');//注册
        $api->post('user-changepassword', 'UserController@userChangePassword');//用户在登录状态修改登录密码
        $api->post('user-sendsms', 'UserController@addSMS');//登录后发短信
        $api->post('userlink-add', 'UserController@linkManAdd');//常用联系人添加
        $api->post('userinfo-add', 'UserController@userInfoAdd');//完善用户信息
        $api->post('userheadimg-upload', 'UserController@userHeadImgUpload');//用户头像上传
        $api->get('userinfo-detail', 'UserController@userInfoDetail');//用户详细信息(完善用户信息用)
//        $api->post('user-changemobile','UserController@userChangeMobile');//修改手机号接口  不是发短信
        $api->get('user-realnameidcard', 'UserController@userRealNameIDCard');//前端调取真实姓名、身份证
        #白条模块
//        $api->get('user-active','UserController@userActiveWhite');//激活白条功能（废弃  审核通过后直接激活 调用service 不走路由）
        $api->get('user-whiteindex', 'UserController@whiteIndex');//白条首页
        $api->get('user-myindex', 'UserController@myIndex');//我的首页
        //$api->post('user-repaymentsindex','UserController@repaymentsIndex');//我的账单
        $api->get('user-repaymentsindex', 'UserController@repaymentsIndex');//我的账单
        $api->get('user-allbill', 'UserController@userAllBill');//全部账单
        $api->get('user-billdetail', 'UserController@userBillDetail');//账单详情
        $api->get('user-instalmentinfo', 'UserController@userInstalmentInfo');//分期明细(该分期的大概信息 + 该分期的所有期数的信息)
        $api->get('user-overdueinfo', 'UserController@userOverdueInfo');//逾期明细
        $api->get('user-creditcode', 'UserController@userCreditCodeJson');//获取该用户的授信码JSON
        $api->post('user-validatecreditcode', 'UserController@userValidateCreditCode');//验证用户授信码
        $api->post('user-confirm-install', 'UserController@confirmInstall');//确认分期
        $api->post('user-immediate-repayment', 'UserController@immediateRepayment');//立即还款按钮
        $api->post('user-getinstalltypeplan', 'UserController@getInstallTypePlan');//获取各个分期方式的金额
        $api->post('user-setpaypwd', 'UserController@userSetPayPwd');
        $api->post('user-editpaypwd', 'UserController@userEditPayPwd');
        $api->post('user-editidcard', 'UserController@userEditIdCard');
        $api->post('user-editidimg', 'UserController@userEditIdImg');
        $api->post('user-carddelete', 'UserController@userCartDelete');
        $api->post('user-cardadd', 'UserController@userCardAdd');
        $api->get('user-cardlist', 'UserController@userCardList');
        $api->get('user-card', 'UserController@userCard');
        #订单相关
        $api->post('api-order-add', 'OrderController@apiOrderAdd');//提交订单
        $api->get('order-list', 'OrderController@orderList');
        $api->get('order-detail', 'OrderController@orderDetail');
        $api->post('order-delete', 'OrderController@orderDelete');
        $api->post('trade-info', 'OrderController@tradeInfo');//联通下单
        #地址管理
        $api->get('user-addressdetail', 'UserController@userAddressDetail');
        $api->post('user-addressdelete', 'UserController@userAddressDelete');
        $api->post('user-addressadd', 'UserController@userAddressAdd');
        $api->post('user-addressedit', 'UserController@userAddressEdit');
        #购物车模块
        $api->post('cart-add','GoodsController@cartAdd');//用户添加购物车商品
        $api->post('cart-del','GoodsController@cartDel');//用户删除购物车商品
        $api->post('cart-list','GoodsController@cartList');//用户购物车右侧列表
        #支付宝
        $api->post('aliPayWeb', 'PayController@aliPayWeb');
        #嘉联支付
        $api->post('yee-pay','PayController@yeePay');
        #快付通
        $api->post('kft-pay','PayController@kftPay');
        $api->post('kft-confirmpay','PayController@kftConfirmPay');
        #短消息
        $api->get('message-noticelist', 'MessageController@noticeList'); // 通知
        $api->post('message-read', 'MessageController@noticeRead');
        $api->post('message-delete', 'MessageController@noticeDelete');
        // $api->post('message-test', 'MessageController@testPushSelf');
        $api->get('message-unread-number', 'MessageController@noticeUnRead');
    });
});
#v2版本
$api->version('v2', function ($api) {
    #无需身份验证UnknownVersionException
    $api->group(['namespace' => 'Modules\Api\Http\Controllers\V2', 'prefix' => 'api', 'middleware' => 'log-app'], function ($api) {
        $api->any('test', 'TestController@test');
    });
    #需要身份验证
    $api->group(['namespace' => 'Modules\Api\Http\Controllers\V2', 'prefix' => 'api', 'middleware' => ['jwt-user', 'log-app']], function ($api) {
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
// \Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
//    var_dump($query->sql);
//    var_dump($query->bindings);
//    var_dump($query->time);
//
// });
