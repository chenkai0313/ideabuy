<?php
/**
 * Created by PhpStorm.
 * User: pc06
 * Date: 2017/8/25
 * Time: 19:14
 */

namespace Modules\Api\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MessageAnnounceController extends Controller
{
    // 短消息公告列表
    public function announceList(Request $request) {
        $params = $request->input();
        $result = \MessageService::messageAnnounce($params);
        return $result;
    }

    // 置顶最高级公告
    public function announceTop(Request $request){
        $params = $request->input();
        $result = \MessageService::messageTop($params);
        return $result;
    }

}