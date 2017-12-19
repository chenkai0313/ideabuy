<?php
/**
 * Created by PhpStorm.
 * User: pc06
 * Date: 2017/8/26
 * Time: 16:23
 */

namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MessageController extends Controller
{
    /**
     * 推送列表
     */
    public function pushList(Request $request){
        $params = $request->input();
        $result = \MessageService::messagePush($params);
        return $result;
    }

    /**
     * 消息列表-all
     */
    public function messageList(Request $request){
        $params = $request->input();
        $result = \MessageService::messageList($params);
        return $result;
    }

    /**
     * 消息列表-公告
     */
    public function announceList(Request $request){
        $params = $request->input();
        $result = \MessageService::messageAnnounceBackend($params);
        return $result;
    }

    /**
     * 消息列表-通知
     */
    public function noticeList(Request $request){
        $params = $request->input();
        $result = \MessageService::messageNoticeBackend($params);
        return $result;
    }

    /**
     * 置顶公告
     */
    public function announceTop(Request $request){
        $params = $request->input();
        $result = \MessageService::messageTop($params);
        return $result;
    }

    /**
     * 定时推送接口-处理推送数据
     */
    public function messageTimed(Request $request){
        $params = $request->input();
        $result = \MessageService::scheduleMessage($params);
        return $result;
    }

    /**
     * 后台推送
     */
    public function push(Request $request){
        $params = $request->input();
        $result = \MessageService::messageEntry($params);
        return $result;
    }

    /**
     * 短信列表
     */
    public function smsList(Request $request){
        $params = $request->input();
        $result = \SMSService::smsList($params);
        return $result;
    }

    /**
     * 推送参数选项
     */
    public function pushSelect(Request $request){
        $params = $request->input();
        $result = \MessageService::pushSelect($params);
        return $result;
    }
}