<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/1 0001
 * Time: 下午 16:55
 */

return [
    #riskcontrol
    'riskcontrol'=>[
        'uninstall_contract'=>env('RISK_DOMAIN').'/loan/uninstall-contract',//不分期合同的添加
        'risk_is_user'=>env('RISK_DOMAIN').'/loan/user-detail',//判断分控用户
        'install_contract'=>env('RISK_DOMAIN').'/loan/contract-add',//分期合同添加
        'account_list' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/user-month-account-list',//获取分期List  账单首页
        'credit' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/credit-calculation',//返回信用的完整url
        'account_index' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/user-account-index',//白条首页
        'install_info' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/contract-install-intro',//获取分期所有信息
        'immediate_repayment' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/user-result-add',//立即还款
        'overdue_list' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/loan-overdue-list',//逾期明细
        'all_bill' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/user-account-list',//全部账单
        'install_getinstalltypeplan' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/install-getinstalltypeplan',//获取分期类型详情 3期 6期 ....
        'get_constant'=>env('RISK_DOMAIN','http://r.d.weknet.cn').'/backend/constant-cache',//获取rc的常量
        'pc_user_account_index' => env('RISK_DOMAIN','http://r.d.weknet.cn').'/loan/pc-user-account-index',//pc端白条首页
    ],
    #ideabuy
    'ideabuy'=>[

    ],
    #message
    'message'=>[
        'message_send'=>env('MESSAGE_DOMAIN').'/message/message-send',              //消息发送
        'message_url' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn'),    // 消息服务域名
        'message_push_url' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/message-push',   // 消息推送
        'message_sms_url' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/message-sms',     // 短信发送

        // 后台接口
        'message_announce_backend' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/message-announcelist-backend',   // 消息公告
        'message_notice_backend' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/message-noticelist-backend',       // 消息通知
        'message_sms_backend' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/message-smslist-backend',             // 短信列表
        'message_push_backend' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/push-list',                  // 推送列表
        'message_announce_top' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/message-announce-top',       // 置顶

        // 前端、api接口
        'message_announce_api' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/message-announcelist-api',   // 消息公告
        'message_notice_api' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/message-noticelist-api',       // 消息公告
        'message-read' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/message-set-read',                   // 设置通知已读
        'message-delete' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/message-delete',                   // 删除通知

        // Common
        'message-unread' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/message-unread-number',            // 未读消息数(通知)
        'get-first-announcement' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/get-first-announcement',   // 获取最新公告

        'message-sms-noqueue' => env('MESSAGE_DOMAIN','http://msg.d.weknet.cn').'/message/message-sms-noqueue',         //短信发送 不走队列
    ],
];