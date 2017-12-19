<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/30 0030
 * Time: 下午 19:25
 */
/**
 * message项目的配置
 */
return [
    #子消息类型
    'message_type'=>[
        0=>'message_announcement',//群发公告
        1=>'user_apply',// 审核-身份认证
        2=>'active_white',//激活白条
        3=>'order_status',//订单状态
        4=>'repayment_reminder',//还款提醒
        5=>'collection_reminder',//催收提醒
        6=>'credit_score',//信用积分
    ],
    #发送类对象
    'audience'=>[
        0=>'all',//所有人
        1=>'regis_id',//个人
    ]
];