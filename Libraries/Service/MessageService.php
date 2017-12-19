<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/30 0030
 * Time: 下午 19:02
 */

namespace Libraries\Service;

use Mockery\Exception;

class MessageService
{
    private $message_type;

    private $audience;

    public function __construct()
    {
        $this->message_type = \Config::get('message.message_type');
        $this->audience = \Config::get('message.audience');
    }

    /**
     * 消息模版的替换
     *
     * @param $template
     * @param $tag
     * @return array|mixed
     *
     * @author  liyongchuan
     * 修改private=>public   caohan
     */
    public function templetMessage($template, $tag)
    {
        try {
            $msgTemplate = \MsgTemplateService::msgTemplateFirst($tag);
            $msg = $msgTemplate['data']['content'];
            if ($msgTemplate['code'] == 1) {
                preg_match_all('/\$\{([a-z_]+)\}/', $msg, $mat);
                foreach ($mat[1] as $item) {
                    if (isset($template[$item])) {
                        $msg = str_replace($item, $template[$item], $msg);
                    } else {
                        return ['code' => 90002, 'msg' => '参数错误'];
                    }
                }
                $msg = str_replace("\${", "", $msg);
                $msg = str_replace("}", "", $msg);
                return ['code' => 1, 'data' => ['msg' => $msg, 'title' => $msgTemplate['data']['msg_title']]];
            } else {
                return $msgTemplate;
            }
        } catch (Exception $e) {
            return ['code' => 99999, 'msg' => '系统异常'];
        }
    }

    /**
     * 消息内容的参数整理
     * @param $content
     * @param $title
     * @param $tag
     * @return array|mixed
     */
    private function content($content, $title, $tag)
    {
        if (!is_null($tag)) {
            $res = $this->templetMessage($content, $tag);
            if ($res['code'] == 1) {
                $send['content'] = $res['data']['msg'];
                $send['title'] = $res['data']['title'];
            } else {
                return $res;
            }
        } else {
            $send['content'] = $content;
            $send['title'] = $title;
        }
        return ['code' => 1, 'data' => $send];
    }

    /**
     * 查询推送第三方toke
     * @param $user_id
     * @return mixed
     */
    private function userThirdFind($user_id)
    {
        return \UserService::userThirdFind(['user_id' => $user_id]);
    }

    /**
     * 推送接口
     * @param $params ['services']       string      消息服务(push,sms,smspush)
     *
     * @param $params ['send_params']    array       发送参数
     * @param $params ['send_params']['title']   string      标题
     * @param $params ['send_params']['content']   string      内容
     * @param $params ['send_params']['type']   int      推送类型(1公告,2通知,默认为-1)
     * @param $params ['send_params']['message_type']   string   消息类型
     * @param $params ['send_params']['operate_type']   int      操作类型(1推送,2自定义消息,3推送+自定义消息)
     * @param $params ['send_params']['send_object']   string      发送对象(all,regis_id 默认为"")
     * @param $params ['send_params']['send_time']   string      定时发送时间(例:'2018-09-09 09:09:09',可为空)
     * @param $params ['send_params']['send_template']   string      自定义模板标识
     * @param $params ['send_params']['data']   array      模板关键词替换数组
     *
     *
     * @param $params ['object_info']    array       用户的详情
     * @param $params ['object_info'][int]['user_id']    int     user_id
     * @param $params ['object_info'][int]['jpush_token']    string     jpush_token
     * @param $params ['object_info'][int]['user_mobile']    string     user手机号
     *
     * @param $params ['result']     array       业务参数(可空)
     * @param $params ['result']['code']     int
     * @param $params ['result']['msg']     string              $params['send_params']['content']
     * @return array|mixed
     *
     * @author      liyongchuan
     */
    public function sendMessage($params)
    {
        $params['sms_send']=isset($params['sms_send'])?$params['sms_send']:0;
        $params['tag']=isset($params['tag'])?$params['tag']:null;
        $params['result']=isset($params['result'])?$params['result']:[];
        $params['user_id']=isset($params['user_id'])?$params['user_id']:[];
        $params['title']=isset($params['title'])?$params['title']:null;
        $con = $this->content($params['keyword'], $params['title'], $params['tag']);
        if ($con['code'] == 1) {
            $send['services']=$params['services'];
            $send['send_params'] = $con['data'];
            $send['send_params']['type'] = isset($params['type']) ? $params['type'] : -1;
            $send['send_params']['message_type'] = isset($params['message_type']) ? $this->message_type[$params['message_type']] : '';
            $send['send_params']['operate_type'] = isset($params['operate_type']) ? $params['operate_type'] : -1;
            $send['send_params']['send_object'] = isset($params['send_object']) ? $this->audience[$params['send_object']] : '';
            $send['send_params']['data'] = $params['keyword'];
            $send['send_params']['send_template'] = $params['tag'];
            if (isset($params['send_time'])) {
                $send['send_params']['send_time'] = $params['send_time'];
            }
            if (count($params['result']) > 0) {
                $send['result'] = $params['result'];
            }
            if(count($params['user_id'])>0){
                if($params['sms_send']==1){
                    foreach ($params['user_id'] as $key=>$vo){
                        $send['object_info'][$key]['user_id']=0;
                        $send['object_info'][$key]['user_mobile']=$vo;
                    }
                }else{
                    $userThird = $this->userThirdFind($params['user_id']);
                    $send['object_info'] = $userThird['data'];
                }
            }else{
                $send['object_info'] = [
                    ['user_id'=>0],
                ];
            }
            $result = vpost(\Config::get('interactive.message.message_send'), ['json'=>json_encode($send)]);
            $return = json_decode($result, true);
            if(!isset($result['code'])){
                $return['code']=1;
            }
        } else {
            $return = $con;
        }
        return $return;
    }

    /**
     * 短信发送接口
     * @param $content      string      短信内容
     * @param $template     string      公司内部模板唯一标识
     * @param $mobile       string      手机号
     * @param $send_id      int         发送者id
     * @param $send_role    int         身份 1会员 2管理员
     * @param $data         string      所有模板标识key拼接string 例子：'number,code,user_id,.........'
     * @param $number       string      所有模板替换标识
     * @param $send_user    array       多用户数组 栗子：[['send_id' => $send_id,'mobile' => $mobile],[] ]
     * ………………………………………………………………
     * @return array|mixed
     */
    public function sendSms($content, $tag, $mobile)
    {
        $templet = $this->templetMessage($content, $tag);
        $vpost_send = [
            'mobile' => json_encode($mobile),//  [1]
            'content' => $templet['data']['msg'],
            'send_template' => $tag,
            'data' => json_encode($content),//  ['month'=>'123','money'=>222]
        ];
        $vpost_res = vpost(\Config::get('interactive.message.message-sms-noqueue'), $vpost_send);
        $vpost_res = json_decode($vpost_res, true);
        if ($vpost_res['code'] == 1) {
            $return = ['code' => 1, 'msg' => '发送成功'];
        } else {
            $return = ['code' => 500, 'msg' => '发送失败'];
        }
        return $return;
    }
}