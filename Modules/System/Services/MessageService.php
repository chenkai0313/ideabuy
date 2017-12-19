<?php
/**
 * Created by PhpStorm.
 * User: 叶帆
 * Date: 2017/8/21
 * Time: 13:59
 */

namespace Modules\System\Services;

use Illuminate\Support\Facades\DB;
use Modules\User\Models\UserThird;
use Modules\System\Models\MessageNotice;

class MessageService
{
    // 1公告 2通知 3不需要显示的消息
    protected $type;

    /**
     * 通知 消息子类型：流程中推送消息[订单 激活白条 身份认证 还款提醒 催收提醒]
     */
    protected $message_type = [
        'message_announcement' => '0',  // 群发公告
        'user_apply'   => '1',          // 审核-身份认证
        'active_white' => '2',          // 激活白条
        'order_status' => '3',          // 订单状态
        'repayment_reminder'  => '4',   // 还款提醒
        'collection_reminder' => '5',   // 催收提醒
        'credit_score' => '6',          // 信用积分
    ];

    // 对应状态说明
    protected $message_type_statement = [
        '0' => '群发公告',
        '1' => '审核-身份认证',
        '2' => '激活白条',
        '3' => '订单状态',
        '4' => '还款提醒',
        '5' => '催收提醒',
        '6' => '信用积分',
    ];

    // 发送类型 1仅推送 2短消息 3推送and消息 string  $operate_type
    protected $operate_type;

    // 消息发送状态 10230-10239
    protected $send_code = false;

    // 消息发送结果说明
    protected $send_msg = '未知错误';

    // 更新发送状态 true 新增  false 更新
    protected $message_code = true;

    /*--------------------------------公共方法-Common------------------------------------------------*/

    /**
     * 推送参数获取
     */
    protected function getConfig($params){
        $jpushs = ['user_id'=>$params['user_id']];
        $user_third = UserThird::userThirdDetail($jpushs);
        return $user_third;
    }

    /**
     * 短消息列表-all
     */
    public function messageList($params){
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $res = MessageNotice::MessageList($params);
        foreach($res as $key=>$value){
            if(isset($res[$key]['extra'])){
                $res[$key]['extra'] = json_decode($res[$key]['extra'],true);
            }
        }
        $count = MessageNotice::MessageListCount($params);
        $result['list'] = $res;
        $result['total'] = $count;
        $result['pages'] = ceil($count/$params['limit']);
        if($res){
            return ['code' => 1, 'msg' => '消息获取成功','data'=>$result];
        }else{
            return ['code' => 10239, 'msg' => '消息列表获取失败'];
        }
    }

    // 未读消息总数 - 接口调用封装
    public function messageUnRead($params){
        $number['number'] = $this->unReadMessage($params);
        return ['code' => 1, 'data' => $number];
    }

    /**
     * 未读消息总数 - 公告不计数
     * @params user_id 用户id
     */
    public function unReadMessage($params){
        $url = \Config::get('interactive.message.message-unread');
        $result = vpost($url,$params);
        $result = json_decode($result,true);

        return $result['code'] == 1 ? $result['data'] : 0;
    }

    /*--------------------------------后台-Backend------------------------------------------------*/

    /**
     * 推送列表-push-后台
     */
    public function messagePush($params){
        unset($params['s']);
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;

        $url = \Config::get('interactive.message.message_push_backend');
        $result = vpost($url,$params);
        return json_decode($result,true);
    }

    /**
     * 短消息列表-通知-后台
     * @params message_type 消息类型
     */
    public function messageNoticeBackend($params){
        $params['type'] = 2;
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;

        $url = \Config::get('interactive.message.message_notice_backend');
        $result = vpost($url,$params);
        return json_decode($result,true);
    }

    /**
     * 短消息列表-公告-后台
     * @params message_type 消息类型
     */
    public function messageAnnounceBackend($params){
        $params['type'] = 1;
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;

        $url = \Config::get('interactive.message.message_announce_backend');
        $result = vpost($url,$params);
        return json_decode($result,true);
    }

    /**
     * 消息置顶
     * @params id 消息id
     */
    public function messageTop($params){
        if(!isset($params['id'])){
            return ['code' => 10235, 'msg' => '缺少必要字段-id'];
        }

        $url = \Config::get('interactive.message.message_announce_top');
        $result = vpost($url,$params);
        return json_decode($result,true);
    }

    // 消息中心发送
    public function messageEntry($params){
        unset($params['s']);
        if($params['audience'] == 'all'){
            $params['user_id'] = 0;
        }else{
            $token = $this->getConfig(['user_id' => $params['user_id']]);
            if ($token['jpush_token'] != "" && $token['user_id'] != "") {
                $params['registration_id'] = $token['jpush_token'];
            }
        }

        $params['merge_regis'] = [
            [
                'user_id' => $params['user_id'],
                'registration_id' => isset($params['registration_id'])?$params['registration_id']:'',
            ]
        ];

        $params['merge_regis'] = json_encode($params['merge_regis']);

        $url = \Config::get('interactive.message.message_push_url');
        $result = vpost($url,$params);
        return json_decode($result,true);
    }

    /**
     * 推送参数选项
     */
    public function pushSelect($params){
        return [
            'code' => 1,
            'data' => [
                'type' => [
                    [
                        'key' => '1', 'value' => '1', 'statement' => '公告',
                        'message_type' => [
                            ['key' => '0', 'value' => 'message_announcement', 'statement' => '群发公告'],
                        ]
                    ],
                    [
                        'key' => '2', 'value' => '2', 'statement' => '通知',
                        'message_type' => [
                            ['key' => '1', 'value' => 'user_apply', 'statement' => '审核-身份认证'],
                            ['key' => '2', 'value' => 'active_white', 'statement' => '激活白条'],
                            ['key' => '3', 'value' => 'order_status', 'statement' => '订单状态'],
                            ['key' => '4', 'value' => 'repayment_reminder', 'statement' => '还款提醒'],
                            ['key' => '5', 'value' => 'collection_reminder', 'statement' => '催收提醒'],
                            ['key' => '6', 'value' => 'credit_score', 'statement' => '信用积分'],
                            ['key' => '7', 'value' => 'sms_send', 'statement' => '短信、验证码发送'],
                        ]
                    ],
                ],
                'audience' => [
                    // regis_id 与 alias 需要去获取用户数据
                    ['key' => '1', 'value' => 'all', 'statement' => '所有人'],
                    ['key' => '2', 'value' => 'regis_id', 'statement' => '用户设备号'],
                    [
                        'key' => '3', 'value' => 'tags', 'statement' => '用户群',
                        'tags' => [
                            ['key' => '1', 'value' => 'new_user', 'statement' => '新用户'],
                            ['key' => '2', 'value' => 'old_user', 'statement' => '老用户'],
                        ]
                    ],
                    ['key' => '4', 'value' => 'alias', 'statement' => '用户标识'],
                ],
                'operate_type' => [
                    ['key' => '1', 'value' => '1', 'statement' => '仅推送'],
                    ['key' => '2', 'value' => '2', 'statement' => '短消息'],
                    ['key' => '3', 'value' => '3', 'statement' => '推送and消息'],
                ],
            ]
        ];
    }

    /*--------------------------------App-api------------------------------------------------*/

    /**
     * 短消息列表-通知-会员用户
     * @params message_type 消息类型
     * @params user_id 用户id  通过用户登录token拿到
     */
    public function messageNotice($params){
        if($params['type'] != 2){
            return ["code" => 10231, 'msg' => '错误的消息主类型'];
        }
        if ( !isset($params['user_id']) ) {
            $params['user_id'] = get_user_id();
        }
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;

        $url = \Config::get('interactive.message.message_notice_api');
        $result = vpost($url,$params);
        return json_decode($result,true);
    }

    /**
     * 短消息列表-公告
     * @params message_type 消息类型
     * @params user_id 用户id  通过用户登录token拿到
     */
    public function messageAnnounce($params){
        if($params['type'] != 1){
            return ["code" => 10231, 'msg' => '错误的消息主类型'];
        }
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;

        $url = \Config::get('interactive.message.message_announce_api');
        $result = vpost($url,$params);
        return json_decode($result,true);
    }

    /**
     * 设置消息已读
     * @params id 消息id string   '1,2,3,4,5,6'
     */
    public function messageSetRead($params){
        $data['id'] = explode(',',$params['id']);

        $url = \Config::get('interactive.message.message-read');
        $result = vpost($url,$params);
        return json_decode($result,true);
    }

    /**
     * 删除消息
     * @params id 消息id
     */
    public function messageDelete($params){
        $url = \Config::get('interactive.message.message-delete');
        $result = vpost($url,$params);
        return json_decode($result,true);
    }

    /**
     * 第一条公告提示 不需要登录
     */
    public function getFirstAnnouncement(){
        $url = \Config::get('interactive.message.get-first-announcement');
        $result = vpost($url,[]);
        return json_decode($result,true);
    }


    /*--------------------------------Pc-api------------------------------------------------*/

    /**
     * 短消息列表-公告
     * @params message_type 消息类型
     * @params user_id 用户id  通过用户登录token拿到
     */
    public function pcAnnounce($params){
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['type'] = 1;

        $url = \Config::get('interactive.message.message_announce_backend');
        $result = vpost($url,$params);
        return json_decode($result,true);
    }


    /**
     * 短消息列表-通知-会员用户
     * @params message_type 消息类型
     * @params user_id 用户id  通过用户登录token拿到
     */
    public function pcNotice($params){
        if ( !isset($params['user_id']) ) {
            return ["code" => 10231, 'msg' => '没有用户id'];
        }
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['type'] = 2;

        $url = \Config::get('interactive.message.message_notice_api');
        $result = vpost($url,$params);
        return json_decode($result,true);
    }
}