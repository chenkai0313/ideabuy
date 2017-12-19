<?php
/**
 * Created by PhpStorm.
 * User: pc06
 * Date: 2017/8/24
 * Time: 15:14
 */

namespace Modules\Api\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MessageController extends Controller
{
    // 短消息列表
    public function noticeList(Request $request) {
        $params = $request->input();
        $result = \MessageService::messageNotice($params);
        return $result;
    }

    // 短消息设置已读
    public function noticeRead(Request $request) {
        $params = $request->input();
        $result = \MessageService::messageSetRead($params);
        return $result;
    }

    // 短消息删除
    public function noticeDelete(Request $request) {
        $params = $request->input();
        $result = \MessageService::messageDelete($params);
        return $result;
    }

    // 推送测试
    public function testPushSelf(Request $request){
        return ['code' => 1, 'msg'=>'api推送测试接口已废弃'];
    }

    // 未读总数
    public function noticeUnRead(Request $request){
        $params = $request->input();
        $params['user_id'] = get_user_id();
        $result = \MessageService::messageUnRead($params);
        return $result;
    }
}